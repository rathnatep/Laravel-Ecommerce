<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    color: #1A1A1A;
    line-height: 1.5;
    background: #fff;
}

.page {
    padding: 48px 52px;
}

/* ---- Handwriting elements ---- */
.hw {
    font-family: Georgia, 'Times New Roman', serif;
}

.store-name {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 34px;
    color: #B08D57;
    letter-spacing: 1px;
}

.invoice-label {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 20px;
    color: #555;
    margin-top: 2px;
}

/* ---- Divider ---- */
.divider {
    border: none;
    border-top: 1px solid #e0e0e0;
    margin: 18px 0;
}

/* ---- Meta table ---- */
.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.meta-table td {
    vertical-align: top;
    padding: 2px 0;
}

.label-text {
    font-size: 10px;
    text-transform: uppercase;
    color: #999;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
}

/* ---- Items table ---- */
.section-heading {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 15px;
    color: #1A1A1A;
    margin-top: 24px;
    margin-bottom: 8px;
    border-bottom: 2px solid #B08D57;
    padding-bottom: 4px;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th {
    font-size: 10px;
    text-transform: uppercase;
    color: #888;
    letter-spacing: 0.5px;
    padding: 6px 8px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.items-table th.right,
.items-table td.right {
    text-align: right;
}

.items-table th.center,
.items-table td.center {
    text-align: center;
}

.items-table td {
    padding: 8px 8px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}

.items-table tbody tr:last-child td {
    border-bottom: none;
}

/* ---- Total row ---- */
.total-separator {
    border: none;
    border-top: 2px solid #1A1A1A;
    margin: 0;
}

.total-row td {
    padding-top: 10px;
    padding-bottom: 4px;
}

.total-label {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 18px;
    font-weight: bold;
    text-align: right;
    padding-right: 8px;
}

.total-amount {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 18px;
    font-weight: bold;
    color: #B08D57;
    text-align: right;
    padding-right: 8px;
}

/* ---- Payment instructions ---- */
.payment-box {
    margin-top: 28px;
    padding: 14px 16px;
    border: 1px solid #e5e5e5;
    background: #FAFAF7;
}

.payment-heading {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 14px;
    margin-bottom: 6px;
    color: #1A1A1A;
}

.payment-text {
    font-size: 12px;
    color: #555;
    line-height: 1.6;
}

/* ---- QR image / fallback ---- */
.qr-image {
    width: 110px;
    height: 110px;
    display: block;
}

.qr-box {
    width: 110px;
    height: 110px;
    border: 2px dashed #ccc;
    text-align: center;
    padding-top: 42px;
    font-size: 10px;
    color: #bbb;
    line-height: 1.5;
}

.payment-layout {
    width: 100%;
    border-collapse: collapse;
}

.payment-layout td {
    vertical-align: top;
    padding: 0;
}

/* ---- Footer ---- */
.footer {
    margin-top: 36px;
    text-align: center;
    font-size: 10px;
    color: #aaa;
    border-top: 1px solid #f0f0f0;
    padding-top: 12px;
}
</style>
</head>
<body>
<div class="page">

    {{-- ===== Header ===== --}}
    <table width="100%">
        <tr>
            <td>
                <div class="store-name">PickCloth</div>
                <div class="invoice-label">Invoice</div>
            </td>
            <td align="right" style="vertical-align: top; padding-top: 6px;">
                <span class="label-text">Order</span>
                <strong>#{{ $order->id }}</strong>
                <br>
                <span class="label-text" style="margin-top: 6px;">Date</span>
                {{ $order->created_at->format('d M Y') }}
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ===== Customer info ===== --}}
    <table class="meta-table">
        <tr>
            <td width="55%">
                <span class="label-text">Billed To</span>
                <strong>{{ $order->user->name }}</strong><br>
                {{ $order->phone }}<br>
                <span style="font-size: 13px;">{{ $order->address_kh }}</span>
            </td>
            <td width="45%" align="right" style="vertical-align: top;">
                <span class="label-text">Order Status</span>
                <strong>{{ ucfirst($order->status) }}</strong><br>
                <span class="label-text" style="margin-top: 6px;">Payment</span>
                {{ $order->paymentStatusLabel() }}
            </td>
        </tr>
    </table>

    {{-- ===== Items ===== --}}
    <div class="section-heading">Order Items</div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th class="center">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->size }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">${{ number_format($item->price, 2) }}</td>
                <td class="right">${{ number_format($item->lineTotal(), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="total-separator">

    <table width="100%">
        <tr class="total-row">
            <td class="total-label" width="80%">Total</td>
            <td class="total-amount" width="20%">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    {{-- ===== Payment instructions ===== --}}
    <div class="payment-box">
        <div class="payment-heading">Payment Instructions</div>
        <table class="payment-layout">
            <tr>
                <td style="padding-right: 16px;">
                    <div class="payment-text">
                        Scan the QR code to pay via {{ config('services.qr_payment.bank_name') }}.<br>
                        After completing payment, upload your screenshot on the order page.<br>
                        Your order will be processed once payment is confirmed.
                    </div>
                </td>
                <td style="width: 120px; text-align: center;">
                    @if ($qrSrc)
                        <img class="qr-image" src="{{ $qrSrc }}" alt="Payment QR Code">
                    @else
                        <div class="qr-box">QR Code<br>pending</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== Footer ===== --}}
    <div class="footer">
        PickCloth &mdash; Thank you for your order! &mdash; Order #{{ $order->id }}
    </div>

</div>
</body>
</html>
