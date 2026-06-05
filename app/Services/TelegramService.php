
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    private string $token;
    private string $chatId;

    public function __construct()
    {
        $this->token  = config('services.telegram.token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    /**
     * Called after the customer uploads a payment proof screenshot.
     * Uploads the image binary directly so it works in both dev and prod (R2).
     */
    public function sendPaymentProof(Order $order): void
    {
        if (empty($this->token) || empty($this->chatId)) {
            Log::warning('TelegramService: token or chat_id not configured — skipping sendPaymentProof.');
            return;
        }

        try {
            $order->loadMissing('items', 'user');

            $itemLines = $order->items->map(fn ($item) =>
                "  • {$item->product_name} ({$item->size}) x{$item->quantity} — $" . number_format($item->price, 2)
            )->join("\n");

            $caption = "📸 Payment Proof — Order #{$order->id}\n"
                . "Customer: " . ($order->user->name ?? '—') . "\n"
                . "Phone: {$order->phone}\n"
                . "Address: {$order->address_kh}\n\n"
                . $itemLines . "\n\n"
                . "Total: $" . number_format($order->total, 2);

            $disk     = config('filesystems.default');
            $contents = Storage::disk($disk)->get($order->payment_proof);
            $filename = basename($order->payment_proof);

            Http::attach('photo', $contents, $filename)
                ->post("https://api.telegram.org/bot{$this->token}/sendPhoto", [
                    'chat_id' => $this->chatId,
                    'caption' => mb_substr($caption, 0, 1024),
                ]);
        } catch (\Throwable $e) {
            Log::error('TelegramService::sendPaymentProof failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
            ]);
        }
    }
}
