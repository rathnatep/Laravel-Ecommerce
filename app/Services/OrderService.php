<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CartService     $cartService,
        private TelegramService $telegramService,
    ) {}

    /**
     * Create a pending order from the current cart, then clear it.
     * Stock is NOT touched here.
     */
    public function createFromCart(User $user, string $phone, string $addressKh): Order
    {
        $items = $this->cartService->items();

        abort_if(empty($items), 422, 'Your cart is empty.');

        $total = array_reduce($items, fn ($carry, $item) => bcadd($carry, $item['line_total'], 2), '0.00');

        $order = DB::transaction(function () use ($user, $phone, $addressKh, $items, $total) {
            $order = Order::create([
                'user_id'        => $user->id,
                'status'         => 'pending',
                'total'          => $total,
                'address_kh'     => $addressKh,
                'phone'          => $phone,
                'payment_status' => 'unpaid',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id'      => $item['product']->id,
                    'product_size_id' => $item['product_size_id'],
                    'product_name'    => $item['product']->name,
                    'size'            => $item['size'],
                    'price'           => $item['unit_price'],
                    'quantity'        => $item['quantity'],
                ]);
            }

            return $order;
        });

        $this->cartService->clear();

        return $order;
    }

    /**
     * Set payment_status = confirmed after admin reviews proof on Telegram.
     */
    public function confirmPayment(Order $order): void
    {
        abort_if(
            $order->payment_status !== 'proof_uploaded',
            422,
            'Payment can only be confirmed after the customer uploads a proof.'
        );

        $order->update(['payment_status' => 'confirmed']);
    }

    /**
     * Approve a pending order: reduce each size's stock once, bump sold_count.
     * Pessimistic lock guards against double-approval.
     */
    public function approveOrder(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new \RuntimeException('Order is not pending and cannot be approved.');
        }

        DB::transaction(function () use ($order) {
            $locked = Order::lockForUpdate()->find($order->id);

            if ($locked->status !== 'pending') {
                return;
            }

            if ($locked->payment_status !== 'confirmed') {
                throw new \RuntimeException('Payment must be confirmed before approving the order.');
            }

            $locked->load('items');

            foreach ($locked->items as $item) {
                $updated = ProductSize::where('id', $item->product_size_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);

                if (!$updated) {
                    throw new \RuntimeException("Insufficient stock for size ID {$item->product_size_id}.");
                }

                Product::where('id', $item->product_id)
                    ->increment('sold_count', $item->quantity);
            }

            $locked->update([
                'status'      => 'approved',
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Cancel a pending order. Cannot cancel approved or completed orders.
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::lockForUpdate()->find($order->id);

            abort_if(
                in_array($locked->status, ['approved', 'completed', 'cancelled']),
                422,
                'Cannot cancel an approved, completed, or already cancelled order.'
            );

            $locked->update(['status' => 'cancelled']);
        });
    }

    /**
     * Store payment proof screenshot; update payment_status to proof_uploaded.
     * Does NOT change order status or touch stock.
     */
    public function uploadPaymentProof(Order $order, UploadedFile $file): void
    {
        $path = Storage::disk(config('filesystems.default'))
            ->putFileAs('payment_proofs', $file, Str::uuid() . '.' . $file->extension());

        $order->update([
            'payment_proof'  => $path,
            'payment_status' => 'proof_uploaded',
        ]);

        $this->telegramService->sendPaymentProof($order);
    }
}
