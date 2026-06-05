<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CheckoutRequest;
use App\Http\Requests\Order\UploadProofRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService  $cartService,
    ) {}

    public function checkout(): View|RedirectResponse
    {
        if ($this->cartService->count() === 0) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $items = $this->cartService->items();
        $total = $this->cartService->total();
        $user  = Auth::user();

        return view('orders.checkout', compact('items', 'total', 'user'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $data  = $request->validated();
        $order = $this->orderService->createFromCart(
            Auth::user(),
            $data['phone'],
            $data['address_kh'],
        );

        return redirect()->route('orders.show', $order)
            ->with('status', 'Order placed successfully! Please complete payment and upload your proof.');
    }

    public function index(): View
    {
        $orders = Auth::user()
            ->orders()
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('items');

        $qrImageFile = config('services.qr_payment.image_file');
        $qrExists    = file_exists(public_path($qrImageFile));
        $qrAssetUrl  = $qrExists ? asset($qrImageFile) : null;

        return view('orders.show', compact('order', 'qrExists', 'qrAssetUrl'));
    }

    public function uploadProof(UploadProofRequest $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== Auth::id(), 403);
        abort_if($order->payment_status === 'confirmed', 422, 'Payment already confirmed.');
        abort_if($order->status !== 'pending', 422, 'Payment proof can only be uploaded for pending orders.');

        $this->orderService->uploadPaymentProof($order, $request->file('payment_proof'));

        return redirect()->route('orders.show', $order)
            ->with('status', 'Payment proof uploaded. We will review it shortly.');
    }

    public function downloadInvoice(Order $order): Response
    {
        $user = Auth::user();
        abort_if(!$user->is_admin && $order->user_id !== $user->id, 403);

        $order->load('items', 'user');

        $qrImageFile = config('services.qr_payment.image_file');
        $qrFilePath  = public_path($qrImageFile);
        $qrSrc       = file_exists($qrFilePath)
            ? 'file:///' . ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $qrFilePath), '/')
            : null;

        $pdf = Pdf::loadView('invoices.invoice', compact('order', 'qrSrc'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-order-' . $order->id . '.pdf');
    }
}
