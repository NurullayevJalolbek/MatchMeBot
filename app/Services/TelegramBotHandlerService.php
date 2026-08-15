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
        $text = trim($message['text'] ?? '');

        if (!$chatId || empty($from)) {
            return;
        }

        // Save / update user in database
        $this->userService->getOrCreateUser($from);

        if (str_starts_with($text, '/start') || str_starts_with($text, '/lang') || str_starts_with($text, '/language')) {
            $this->sendLanguageSelection($chatId, $from['first_name'] ?? 'foydalanuvchi');
            return;
        }

        // Fallback response for other text messages
        $user = $this->userService->getByTelegramId($from['id']);
        $lang = $user?->language_code ?? 'uz';
        $this->sendWelcomeBanner($chatId, $lang);
    }

    /**
     * Handle incoming callback query.
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackQueryId = $callbackQuery['id'] ?? '';
        $from = $callbackQuery['from'] ?? [];
        $data = $callbackQuery['data'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? ($from['id'] ?? null);

        if (!$chatId) {
            return;
        }

        // Handle language selection callback
        if ($data === 'set_lang:change') {
            $this->botService->answerCallbackQuery($callbackQueryId);
            $this->sendLanguageSelection($chatId, $from['first_name'] ?? 'Foydalanuvchi');
            return;
        }

        if (str_starts_with($data, 'set_lang:') || in_array($data, ['lang_uz', 'lang_ru', 'lang_en'])) {
            $lang = match ($data) {
                'set_lang:ru', 'lang_ru' => 'ru',
                'set_lang:en', 'lang_en' => 'en',
                default => 'uz',
            };

            // Save selected language to DB
            $this->userService->updateLanguage($from['id'], $lang);

            // Acknowledge callback query
            $alertText = match ($lang) {
                'ru' => "Язык сохранен: Русский",
                'en' => "Language set: English",
                default => "Til saqlandi: O'zbekcha",
            };
            $this->botService->answerCallbackQuery($callbackQueryId, $alertText, false);

            // Send banner with WebApp button
            $this->sendWelcomeBanner($chatId, $lang);
        }
    }

    /**
     * Send language selection keyboard with flags.
     */
    public function sendLanguageSelection(int|string $chatId, string $name = 'Foydalanuvchi'): void
    {
        $text = "👋 <b>Assalomu alaykum, " . htmlspecialchars($name) . "!</b>\n\n"
            . "✨ <b>MatchMe</b> — o'zingizga mos bo'lgan insonni topishda yordam beramiz!\n\n"
            . "🌐 <i>Iltimos, o'zingizga qulay tilni tanlang:</i>\n"
            . "🌐 <i>Пожалуйста, выберите удобный язык:</i>\n"
            . "🌐 <i>Please select your preferred language:</i>";
            

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => "🇺🇿 O'zbekcha", 'callback_data' => 'set_lang:uz'],
                ],
                [
                    ['text' => "🇷🇺 Русский", 'callback_data' => 'set_lang:ru'],
                ],
                [
                    ['text' => "🇬🇧 English", 'callback_data' => 'set_lang:en'],
                ],
            ],
        ];

        $this->botService->sendMessage($chatId, $text, $keyboard, 'HTML');
    }

    /**
     * Send welcome banner with WebApp button.
     */
    public function sendWelcomeBanner(int|string $chatId, string $lang = 'uz'): void
    {
        $webAppUrl = config('services.telegram.webapp_url') ?: env('APP_URL', 'https://matchme.uz');

        $caption = match ($lang) {
            'ru' => "❤️ <b>Добро пожаловать в MatchMe!</b>\n\n"
                . "🔥 Находите взаимные симпатии, общайтесь в чатах и знакомьтесь с классными людьми рядом с вами прямо в Telegram!\n\n"
                . "👇 <b>Нажмите кнопку ниже, чтобы войти в MatchMe:</b>",
            'en' => "❤️ <b>Welcome to MatchMe!</b>\n\n"
                . "🔥 Discover mutual matches, chat, and connect with amazing people nearby right inside Telegram!\n\n"
                . "👇 <b>Tap the button below to start MatchMe:</b>",
            default => "❤️ <b>MatchMe ilovasiga xush kelibsiz!</b>\n\n"
                . "🔥 O'zaro yoqtirishlar, qiziqarli suhbatlar va yoningizdagi yangi insonlarni to'g'ridan-to'g'ri Telegram ichida toping!\n\n"
                . "👇 <b>Tanishuvni boshlash uchun quyidagi tugmani bosing:</b>",
        };

        $buttonText = match ($lang) {
            'ru' => "🚀 Открыть MatchMe",
            'en' => "🚀 Open MatchMe",
            default => "🚀 MatchMe'ni ochish",
        };

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => $buttonText,
                        'web_app' => [
                            'url' => $webAppUrl,
                        ],
                    ],
                ],
                [
                    [
                        'text' => "🌐 " . match ($lang) {
                            'ru' => "Сменить язык",
                            'en' => "Change language",
                            default => "Tilni o'zgartirish",
                        },
                        'callback_data' => 'set_lang:change',
                    ],
                ],
            ],
        ];

        // If user tapped "Change language"
        // Let's also check banner image
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
