<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiInteractionLog;
use Illuminate\Support\Facades\DB;

class AiMonitorController extends Controller
{
    public function index()
    {
        // ── Konfigurasi API ───────────────────────────────────────────────────
        $configuredKeys = collect(config('services.groq.api_keys', []))
            ->filter()
            ->values();

        $apiConfig = [
            'total_keys'  => $configuredKeys->count(),
            'model'       => config('services.groq.model', '-'),
            'rate_limits' => config('ai.rate_limits'),
        ];

        // ── Statistik Penggunaan ──────────────────────────────────────────────
        $usage = [
            'today' => $this->getUsageStats(today()),
            'week'  => $this->getUsageStats(now()->startOfWeek()),
            'month' => $this->getUsageStats(now()->startOfMonth()),
            'total' => $this->getUsageStats(null),
        ];

        // ── Log Terbaru (50 entri terakhir) ──────────────────────────────────
        $recentLogs = AiInteractionLog::with(['user', 'activity'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.ai-monitor', compact('apiConfig', 'usage', 'recentLogs'));
    }

    /**
     * Hitung statistik penggunaan mulai dari tanggal tertentu.
     * Jika $from null, hitung semua data (total keseluruhan).
     */
    private function getUsageStats(?\Illuminate\Support\Carbon $from): array
    {
        $query = AiInteractionLog::query();

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        return [
            'total'         => (clone $query)->count(),
            'chat'          => (clone $query)->where('type', 'chat')->count(),
            'submit'        => (clone $query)->where('type', 'submit')->count(),
            'tokens'        => (clone $query)->sum('tokens_used'),
            'avg_response'  => round((clone $query)->avg('response_time') ?? 0, 2),
        ];
    }
}
