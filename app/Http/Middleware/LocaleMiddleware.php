<?php

namespace App\Http\Middleware;

use App\Enums\General\LanguageCodeEnum;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('lang')
            ?? $request->header('Accept-Language')
            ?? $request->input('lang')
            ?? session('lang');

        // Normalize if comma-separated or like en-US,en;q=0.9
        if ($lang && str_contains($lang, ',')) {
            $lang = explode(',', $lang)[0];
        }
        if ($lang && str_contains($lang, '-')) {
            $lang = explode('-', $lang)[0];
        }

        $lang = strtolower(trim((string) $lang));

        if (!empty($lang)) {
            if (!LanguageCodeEnum::isValid($lang)) {
                $available = implode(', ', LanguageCodeEnum::values());

                return new JsonResponse([
                    'status' => false,
                    'message' => __('message.Invalid language', ['languages' => $available], config('app.locale', 'uz')),
                    'errors' => [
                        'lang' => [
                            __('message.Invalid language', ['languages' => $available], config('app.locale', 'uz')),
                        ],
                    ],
                ], 422);
            }

            app()->setLocale($lang);
            session(['lang' => $lang]);
        } else {
            app()->setLocale(config('app.locale', 'uz'));
        }

        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('Content-Language', app()->getLocale());
        }

        return $response;
    }
}
