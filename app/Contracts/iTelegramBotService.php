<?php

namespace App\Contracts;

interface iTelegramBotService
{
    /**
     * Send a text message to a chat.
     */
    public function sendMessage(
        int|string $chatId,
        string $text,
        array $replyMarkup = [],
        ?string $parseMode = 'HTML'
    ): array;

    /**
     * Send a photo to a chat with optional caption and buttons.
     */
    public function sendPhoto(
        int|string $chatId,
        string $photo,
        ?string $caption = null,
        array $replyMarkup = [],
        ?string $parseMode = 'HTML'
    ): array;

    /**
     * Answer an inline callback query.
     */
    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false
    ): array;

    /**
     * Edit message reply markup.
     */
    public function editMessageReplyMarkup(
        int|string $chatId,
        int $messageId,
        array $replyMarkup = []
    ): array;

    /**
     * Set telegram webhook URL.
     */
    public function setWebhook(string $url): array;

    /**
     * Delete telegram webhook.
     */
    public function deleteWebhook(): array;

    /**
     * Get webhook info.
     */
    public function getWebhookInfo(): array;

    /**
     * Get updates for long polling.
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array;
}
