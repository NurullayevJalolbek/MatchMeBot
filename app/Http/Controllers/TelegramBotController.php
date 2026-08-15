<?php

namespace App\Http\Controllers;

use App\Contracts\iTelegramBotService;
use App\Services\TelegramBotHandlerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function __construct(
        protected iTelegramBotService $telegramService,
        protected TelegramBotHandlerService $handlerService
    ) {}

    /**
     * Handle incoming webhook updates from Telegram.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $update = $request->all();

        if (empty($update)) {
            return response()->json(['status' => 'empty update'], 400);
        }

        $this->handlerService->handleUpdate($update);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Set webhook for Telegram Bot.
     */
    public function setWebhook(Request $request): JsonResponse
    {
        $url = $request->input('url', url('/api/telegram/webhook'));
        $result = $this->telegramService->setWebhook($url);

        return response()->json($result);
    }

    /**
     * Delete webhook for Telegram Bot.
     */
    public function deleteWebhook(): JsonResponse
    {
        $result = $this->telegramService->deleteWebhook();

        return response()->json($result);
    }

    /**
     * Get webhook status and information.
     */
    public function getWebhookInfo(): JsonResponse
    {
        $result = $this->telegramService->getWebhookInfo();

        return response()->json($result);
    }
}
