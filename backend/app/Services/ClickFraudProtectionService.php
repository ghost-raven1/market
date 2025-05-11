<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\Click;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class ClickFraudProtectionService
{
    protected $agent;
    protected $suspiciousPatterns = [
        'rapid_clicks' => 5, // количество кликов за период
        'rapid_period' => 60, // период в секундах
        'ip_threshold' => 10, // максимальное количество кликов с одного IP
        'user_agent_threshold' => 5, // максимальное количество кликов с одного User-Agent
    ];

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function validateClick(Advertisement $advertisement, string $ip, string $userAgent): bool
    {
        try {
            // Проверка на бота
            if ($this->isBot($userAgent)) {
                return false;
            }

            // Проверка на подозрительные паттерны
            if ($this->hasSuspiciousPatterns($ip, $userAgent)) {
                return false;
            }

            // Проверка на повторные клики
            if ($this->isDuplicateClick($advertisement->id, $ip)) {
                return false;
            }

            // Проверка на географию
            if (!$this->isValidLocation($ip)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Click fraud protection failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function isBot(string $userAgent): bool
    {
        return $this->agent->isRobot();
    }

    protected function hasSuspiciousPatterns(string $ip, string $userAgent): bool
    {
        // Проверка на быстрые клики
        $rapidClicks = Cache::get("rapid_clicks:{$ip}", 0);
        if ($rapidClicks >= $this->suspiciousPatterns['rapid_clicks']) {
            return true;
        }

        // Проверка на количество кликов с IP
        $ipClicks = Cache::get("ip_clicks:{$ip}", 0);
        if ($ipClicks >= $this->suspiciousPatterns['ip_threshold']) {
            return true;
        }

        // Проверка на количество кликов с User-Agent
        $userAgentClicks = Cache::get("ua_clicks:{$userAgent}", 0);
        if ($userAgentClicks >= $this->suspiciousPatterns['user_agent_threshold']) {
            return true;
        }

        return false;
    }

    protected function isDuplicateClick(int $advertisementId, string $ip): bool
    {
        $key = "duplicate_click:{$advertisementId}:{$ip}";
        return Cache::has($key);
    }

    protected function isValidLocation(string $ip): bool
    {
        // Здесь будет интеграция с сервисом геолокации
        // Например, MaxMind GeoIP2 или аналогичный сервис
        return true;
    }

    public function recordClick(Advertisement $advertisement, string $ip, string $userAgent): void
    {
        // Запись клика в базу данных
        Click::create([
            'advertisement_id' => $advertisement->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'is_valid' => $this->validateClick($advertisement, $ip, $userAgent)
        ]);

        // Обновление кэша для проверки паттернов
        $this->updateClickPatterns($ip, $userAgent);
    }

    protected function updateClickPatterns(string $ip, string $userAgent): void
    {
        // Обновление счетчика быстрых кликов
        Cache::increment("rapid_clicks:{$ip}");
        Cache::expire("rapid_clicks:{$ip}", $this->suspiciousPatterns['rapid_period']);

        // Обновление счетчика кликов с IP
        Cache::increment("ip_clicks:{$ip}");
        Cache::expire("ip_clicks:{$ip}", 3600); // 1 час

        // Обновление счетчика кликов с User-Agent
        Cache::increment("ua_clicks:{$userAgent}");
        Cache::expire("ua_clicks:{$userAgent}", 3600); // 1 час
    }
} 