<?php

namespace App\Services;

use App\Contracts\iTelegramBotService;
use App\Contracts\iTelegramUserService;
use Illuminate\Support\Facades\Log;

class TelegramBotHandlerService
{
    public function __construct(
        protected iTelegramBotService $botService,
        protected iTelegramUserService $userService
    ) {}

    /**
     * Handle incoming update from webhook or long polling.
     */
    public function handleUpdate(array $update): void
    {
        try {
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }
        } catch (\Throwable $e) {
            Log::error("Error handling Telegram update: " . $e->getMessage(), [
                'update' => $update,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Handle incoming text message.
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;
        $from = $message['from'] ?? [];

        if (!$chatId || empty($from)) {
            return;
        }

        // Save / update user in database
        $this->userService->getOrCreateUser($from);

        $this->sendWelcomeBanner($chatId);
    }

    /**
     * Handle incoming callback query.
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackQueryId = $callbackQuery['id'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? ($callbackQuery['from']['id'] ?? null);

        if (!$chatId) {
            return;
        }

        $this->botService->answerCallbackQuery($callbackQueryId);
        $this->sendWelcomeBanner($chatId);
    }

    /**
     * Send welcome banner with WebApp button in Uzbek.
     */
    public function sendWelcomeBanner(int|string $chatId): void
    {
        $webAppUrl = config('services.telegram.webapp_url') ?: env('APP_URL', 'https://matchme.uz');

        $caption = "❤️ <b>MatchMe ilovasiga xush kelibsiz!</b>\n\n"
            . "🔥 O'zaro yoqtirishlar, qiziqarli suhbatlar va yoningizdagi yangi insonlarni to'g'ridan-to'g'ri Telegram ichida toping!\n\n"
            . "👇 <b>Tanishuvni boshlash uchun quyidagi tugmani bosing:</b>";

        $user = $this->userService->getByTelegramId($chatId);
        $targetUrl = ($user && $user->onboarding_completed) ? rtrim($webAppUrl, '/') . '/discovery' : $webAppUrl;

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => "🚀 MatchMe'ni ochish",
                        'web_app' => [
                            'url' => $targetUrl,
                        ],
                    ],
                ],
            ],
        ];

        $localImagePath = public_path('images/welcome-banner.jpg');
        $fallbackImageUrl = 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=1000&auto=format&fit=crop';

        $photo = file_exists($localImagePath) ? $localImagePath : $fallbackImageUrl;

        $response = $this->botService->sendPhoto($chatId, $photo, $caption, $keyboard, 'HTML');

        // If sending photo failed, fallback to sending message
        if (isset($response['ok']) && !$response['ok']) {
            $this->botService->sendMessage($chatId, $caption, $keyboard, 'HTML');
        }
    }
}
