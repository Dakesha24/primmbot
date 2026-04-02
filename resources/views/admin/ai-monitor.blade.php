<x-layouts.admin title="Monitor Penggunaan AI">
    <x-slot:styles>
        <style>
            /* ── Grid Stats ── */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-card {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 6px;
                padding: 20px 22px;
            }

            .stat-label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin-bottom: 8px;
            }

            .stat-value {
                font-size: 26px;
                font-weight: 800;
                color: #0f1b3d;
                line-height: 1;
                margin-bottom: 6px;
            }

            .stat-sub {
                font-size: 11.5px;
                color: #6b7a99;
            }

            .stat-sub span {
                font-weight: 700;
                color: #3b5bdb;
            }

            /* ── Section header ── */
            .section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 14px;
            }

            .section-title {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
            }

            .section-hint {
                font-size: 12px;
                color: #94a3b8;
            }

            /* ── API Config card ── */
            .config-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            /* ── Rate limit grid ── */
            .limit-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            .limit-card {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 6px;
                padding: 16px 20px;
            }

            .limit-label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin-bottom: 6px;
            }

            .limit-value {
                font-size: 20px;
                font-weight: 800;
                color: #0f1b3d;
                line-height: 1;
                margin-bottom: 4px;
            }

            .limit-sub {
                font-size: 11px;
                color: #94a3b8;
            }

            .config-item {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 6px;
                padding: 18px 20px;
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .config-icon {
                width: 38px;
                height: 38px;
                border-radius: 8px;
                background: #eef2ff;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .config-icon svg {
                width: 18px;
                height: 18px;
                color: #3b5bdb;
            }

            .config-text-label {
                font-size: 11px;
                font-weight: 600;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                margin-bottom: 3px;
            }

            .config-text-value {
                font-size: 14px;
                font-weight: 700;
                color: #0f1b3d;
            }

            .badge-active {
                display: inline-block;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
                background: #dcfce7;
                color: #166534;
                margin-left: 6px;
            }

            .badge-warn {
                display: inline-block;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
                background: #fef9c3;
                color: #854d0e;
                margin-left: 6px;
            }

            /* ── Period tabs ── */
            .period-tabs {
                display: flex;
                gap: 6px;
            }

            .period-tab {
                font-size: 12px;
                font-weight: 600;
                padding: 5px 14px;
                border-radius: 5px;
                border: 1px solid #dde1ea;
                background: #fff;
                color: #6b7a99;
                cursor: pointer;
                transition: all 0.12s;
            }

            .period-tab.active,
            .period-tab:hover {
                background: #0f1b3d;
                color: #fff;
                border-color: #0f1b3d;
            }

            /* ── Stats period panels ── */
            .period-panel { display: none; }
            .period-panel.active { display: block; }

            /* ── Log table ── */
            .log-table-wrap {
                overflow-x: auto;
            }

            .log-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12.5px;
            }

            .log-table th {
                background: #f8f9fc;
                padding: 9px 14px;
                text-align: left;
                font-weight: 700;
                font-size: 11px;
                color: #6b7a99;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                border-bottom: 1px solid #e8eaf0;
                white-space: nowrap;
            }

            .log-table td {
                padding: 10px 14px;
                border-bottom: 1px solid #f0f2f7;
                color: #1a2332;
                vertical-align: middle;
            }

            .log-table tbody tr:last-child td { border-bottom: none; }
            .log-table tbody tr:hover td { background: #f8f9fc; }

            .type-badge {
                display: inline-block;
                font-size: 10.5px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .type-chat   { background: #dbeafe; color: #1e40af; }
            .type-submit { background: #dcfce7; color: #166534; }

            .text-muted { color: #94a3b8; font-style: italic; }
            .text-mono  { font-family: 'Courier New', monospace; font-size: 11.5px; }

            /* ── Empty state ── */
            .empty-state {
                text-align: center;
                padding: 40px 20px;
                color: #94a3b8;
                font-size: 13px;
            }

            .empty-state svg {
                width: 36px;
                height: 36px;
                margin: 0 auto 12px;
                display: block;
                opacity: 0.35;
            }
        </style>
    </x-slot:styles>

    <div class="page-header">
        <h1>Monitor Penggunaan AI</h1>
        <p>Statistik penggunaan Groq API dan log interaksi virtual assistant.</p>
    </div>

    {{-- ── Konfigurasi API ── --}}
    <div class="section-head">
        <span class="section-title">Konfigurasi API</span>
    </div>

    <div class="config-grid">

        {{-- Jumlah Key --}}
        <div class="config-item">
            <div class="config-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <div class="config-text-label">API Key Aktif</div>
                <div class="config-text-value">
                    {{ $apiConfig['total_keys'] }} key
                    @if($apiConfig['total_keys'] >= 2)
                        <span class="badge-active">Failover Aktif</span>
                    @else
                        <span class="badge-warn">Tanpa Backup</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Model --}}
        <div class="config-item">
            <div class="config-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                </svg>
            </div>
            <div>
                <div class="config-text-label">Model</div>
                <div class="config-text-value text-mono">{{ $apiConfig['model'] }}</div>
            </div>
        </div>

        {{-- Total semua waktu --}}
        <div class="config-item">
            <div class="config-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="config-text-label">Total Request (Semua Waktu)</div>
                <div class="config-text-value">{{ number_format($usage['total']['total']) }}</div>
            </div>
        </div>

    </div>

    {{-- ── Rate Limit ── --}}
    <div class="section-head" style="margin-top: 8px;">
        <span class="section-title">Rate Limit Groq API</span>
        <span class="section-hint">Sesuaikan di <code style="font-size:11px;background:#f0f2f7;padding:1px 5px;border-radius:3px;">config/ai.php</code> jika plan berubah</span>
    </div>

    <div class="limit-grid">
        <div class="limit-card">
            <div class="limit-label">Requests / Menit</div>
            <div class="limit-value">{{ number_format($apiConfig['rate_limits']['rpm']) }}</div>
            <div class="limit-sub">RPM</div>
        </div>
        <div class="limit-card">
            <div class="limit-label">Requests / Hari</div>
            <div class="limit-value">{{ number_format($apiConfig['rate_limits']['rpd']) }}</div>
            <div class="limit-sub">RPD</div>
        </div>
        <div class="limit-card">
            <div class="limit-label">Tokens / Menit</div>
            <div class="limit-value">{{ number_format($apiConfig['rate_limits']['tpm']) }}</div>
            <div class="limit-sub">TPM</div>
        </div>
        <div class="limit-card">
            <div class="limit-label">Tokens / Hari</div>
            <div class="limit-value">{{ number_format($apiConfig['rate_limits']['tpd']) }}</div>
            <div class="limit-sub">TPD</div>
        </div>
    </div>

    {{-- ── Statistik per Periode ── --}}
    <div class="section-head" style="margin-top: 8px;">
        <span class="section-title">Statistik Penggunaan</span>
        <div class="period-tabs">
            <button class="period-tab active" onclick="switchPeriod('today', this)">Hari Ini</button>
            <button class="period-tab" onclick="switchPeriod('week', this)">Minggu Ini</button>
            <button class="period-tab" onclick="switchPeriod('month', this)">Bulan Ini</button>
        </div>
    </div>

    @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini'] as $period => $label)
        <div class="period-panel {{ $period === 'today' ? 'active' : '' }}" id="panel-{{ $period }}">
            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-label">Total Request</div>
                    <div class="stat-value">{{ number_format($usage[$period]['total']) }}</div>
                    <div class="stat-sub">{{ $label }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Request Chat</div>
                    <div class="stat-value">{{ number_format($usage[$period]['chat']) }}</div>
                    <div class="stat-sub">
                        Tanya jawab siswa dengan VA
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Request Submit</div>
                    <div class="stat-value">{{ number_format($usage[$period]['submit']) }}</div>
                    <div class="stat-sub">
                        Evaluasi jawaban siswa
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Token Digunakan</div>
                    <div class="stat-value">{{ $usage[$period]['tokens'] > 0 ? number_format($usage[$period]['tokens']) : '—' }}</div>
                    <div class="stat-sub">Total token {{ $label }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Rata-rata Response</div>
                    <div class="stat-value">{{ $usage[$period]['avg_response'] > 0 ? $usage[$period]['avg_response'] . 's' : '—' }}</div>
                    <div class="stat-sub">Waktu respons API</div>
                </div>

            </div>
        </div>
    @endforeach

    {{-- ── Log Terbaru ── --}}
    <div class="card" style="margin-top: 8px;">
        <div class="section-head" style="margin-bottom: 16px;">
            <span class="section-title">Log Terbaru</span>
            <span class="section-hint">50 interaksi terakhir</span>
        </div>

        @if($recentLogs->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Belum ada log interaksi AI.
            </div>
        @else
            <div class="log-table-wrap">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Siswa</th>
                            <th>Aktivitas</th>
                            <th>Tipe</th>
                            <th>Token</th>
                            <th>Response Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr>
                                <td class="text-mono" style="white-space:nowrap;color:#6b7a99;">
                                    {{ $log->created_at->format('d M H:i:s') }}
                                </td>
                                <td>{{ $log->user?->name ?? '<span class="text-muted">—</span>' }}</td>
                                <td>
                                    @if($log->activity)
                                        <span style="font-size:11px;background:#f0f2f7;padding:2px 7px;border-radius:4px;font-weight:600;color:#4a5568;text-transform:uppercase;">
                                            {{ $log->activity->stage }}
                                        </span>
                                        <span style="font-size:12px;color:#6b7a99;margin-left:4px;">#{{ $log->activity_id }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="type-badge type-{{ $log->type }}">
                                        {{ $log->type === 'chat' ? 'Chat' : 'Submit' }}
                                    </span>
                                </td>
                                <td style="color:#6b7a99;">
                                    {{ $log->tokens_used ? number_format($log->tokens_used) : '<span class="text-muted">—</span>' }}
                                </td>
                                <td style="color:#6b7a99;">
                                    {{ $log->response_time ? $log->response_time . 's' : '<span class="text-muted">—</span>' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-slot:scripts>
        <script>
            function switchPeriod(period, btn) {
                // Sembunyikan semua panel
                document.querySelectorAll('.period-panel').forEach(p => p.classList.remove('active'));
                // Nonaktifkan semua tab
                document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));

                // Tampilkan panel dan aktifkan tab yang dipilih
                document.getElementById('panel-' + period).classList.add('active');
                btn.classList.add('active');
            }
        </script>
    </x-slot:scripts>
</x-layouts.admin>
