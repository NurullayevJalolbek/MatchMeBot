<?php

namespace App\Services;

use App\Contracts\iTelegramBotService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService implements iTelegramBotService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = (string) (config('services.telegram.bot_token') ?? env('BOT_TOKEN', ''));
        $this->apiUrl = rtrim((string) (config('services.telegram.api_url') ?? 'https://api.telegram.org'), '/');
    }

    /**
     * Build base Telegram Bot API URL.
     */
    protected function endpoint(string $method): string
    {
        return "{$this->apiUrl}/bot{$this->botToken}/{$method}";
    }

    /**
     * Send a text message to a chat.
     */
    public function sendMessage(
        int|string $chatId,
        string $text,
        array $replyMarkup = [],
        ?string $parseMode = 'HTML'
    ): array {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($parseMode) {
            $payload['parse_mode'] = $parseMode;
        }

        if (!empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->sendRequest('sendMessage', $payload);
    }

    /**
     * Send a photo to a chat with optional caption and buttons.
     */
    public function sendPhoto(
        int|string $chatId,
        string $photo,
        ?string $caption = null,
        array $replyMarkup = [],
        ?string $parseMode = 'HTML'
    ): array {
        $params = [
            'chat_id' => (string) $chatId,
        ];

        if ($caption !== null) {
            $params['caption'] = $caption;
        }

        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        if (!empty($replyMarkup)) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        // If local file exists, attach it as multipart
        if (file_exists($photo)) {
            $response = Http::attach(
                'photo',
                file_get_contents($photo),
                basename($photo)
            )->post($this->endpoint('sendPhoto'), $params);

            return $response->json() ?? [];
        }

        // If photo is URL or file_id
        $params['photo'] = $photo;
        return $this->sendRequest('sendPhoto', $params);
    }

    /**
     * Answer an inline callback query.
     */
    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false
    ): array {
        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        return $this->sendRequest('answerCallbackQuery', $payload);
    }

    /**
     * Edit message reply markup.
     */
    public function editMessageReplyMarkup(
        int|string $chatId,
        int $messageId,
        array $replyMarkup = []
    ): array {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => json_encode($replyMarkup),
        ];

        return $this->sendRequest('editMessageReplyMarkup', $payload);
    }

    /**
     * Set telegram webhook URL.
     */
    public function setWebhook(string $url): array
    {
        return $this->sendRequest('setWebhook', [
            'url' => $url,
            'allowed_updates' => json_encode(['message', 'callback_query', 'inline_query']),
        ]);
    }

    /**
     * Delete telegram webhook.
     */
    public function deleteWebhook(): array
    {
        return $this->sendRequest('deleteWebhook', [
            'drop_pending_updates' => true,
        ]);
    }

    /**
     * Get webhook info.
     */
    public function getWebhookInfo(): array
    {
        return $this->sendRequest('getWebhookInfo');
    }

    /**
     * Get updates for long polling.
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array
    {
        return $this->sendRequest('getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ]);
    }

    /**
     * Generic POST request to Telegram API.
     */
    protected function sendRequest(string $method, array $params = []): array
    {
        try {
            $response = Http::timeout(15)->post($this->endpoint($method), $params);
            $data = $response->json();

            if (!$response->successful() || (isset($data['ok']) && !$data['ok'])) {
                Log::warning("Telegram API method [{$method}] returned non-success response: ", [
                    'status' => $response->status(),
                    'body' => $data ?? $response->body(),
                ]);
            }

            return $data ?? [];
        } catch (\Throwable $e) {
            Log::error("Telegram API Request Error [{$method}]: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return [
                'ok' => false,
                'description' => $e->getMessage(),
            ];
        }
    }
}
