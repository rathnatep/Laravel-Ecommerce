<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($payment = $request->get('payment_status')) {
            $query->where('payment_status', $payment);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items');

        return view('admin.orders.show', compact('order'));
    }

    public function confirmPayment(Order $order): RedirectResponse
    {
        $this->orderService->confirmPayment($order);

        return back()->with('status', 'Payment confirmed.');
    }

    public function approve(Order $order): RedirectResponse
    {
        try {
            $this->orderService->approveOrder($order);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Order approved. Stock adjusted.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->orderService->cancelOrder($order);

        return back()->with('status', 'Order cancelled.');
    }
}
