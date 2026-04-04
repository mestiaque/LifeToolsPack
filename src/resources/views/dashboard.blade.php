@extends('me::master')
@section('title', trans('Dashboard'))

@section('content')
<div class="db-root">
    {{-- ══ WELCOME HERO ══ --}}
    <div class="db-hero">
        <div class="db-hero-left">
            <span class="db-date-chip">
                <i class="fas fa-calendar-day me-1"></i>
                {{ now()->format('l, d F Y') }}
                &nbsp;·&nbsp;
                <i class="fas fa-clock me-1"></i>
                <span id="live-tz">{{ date_default_timezone_get() }}</span>
            </span>
            <h1 class="db-greeting">
                @php
                    $hour = (int) date('H');
					$user = auth()->user()->name ?? 'Stranger';
                    $greet = 'Hello' ;
                @endphp
                {{ $greet }} {{ $user }} <span class="db-wave">👋</span>
            </h1>
            <p class="db-subtitle">Here's an overview of everything in your life today.</p>
        </div>
        <div class="db-hero-clock">
            <canvas id="analog-clock" width="130" height="130"></canvas>
            {{-- <span class="clock-tz-badge">{{ date_default_timezone_get() }}</span> --}}
        </div>
    </div>

    {{-- ══ KPI STRIP ══ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-indigo">
                <div class="kpi-icon-wrap"><i class="fas fa-images"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num counter" data-target="{{ $totalGalleryImages }}">{{ $totalGalleryImages }}</div>
                    <div class="kpi-lbl">Gallery</div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-images"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-violet">
                <div class="kpi-icon-wrap"><i class="fas fa-envelope"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num counter" data-target="{{ $totalMessages }}">{{ $totalMessages }}</div>
                    <div class="kpi-lbl">Messages</div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-rose">
                <div class="kpi-icon-wrap"><i class="fas fa-calendar-check"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num counter" data-target="{{ $runningEventsWithExpenses->count() }}">{{ $runningEventsWithExpenses->count() }}</div>
                    <div class="kpi-lbl">Live Events</div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-amber">
                <div class="kpi-icon-wrap"><i class="fas fa-folder-open"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num">{{ $totalFolders }}</div>
                    <div class="kpi-lbl">Folders <span class="kpi-sub">/ {{ $totalFiles }} Files</span></div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-folder-open"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-teal">
                <div class="kpi-icon-wrap"><i class="fas fa-hdd"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num">{{ $totalDisks }}</div>
                    <div class="kpi-lbl">Disks</div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-hdd"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi kpi-emerald">
                <div class="kpi-icon-wrap"><i class="fas fa-project-diagram"></i></div>
                <div class="kpi-body">
                    <div class="kpi-num counter" data-target="{{ $totalProjects }}">{{ $totalProjects }}</div>
                    <div class="kpi-lbl">Projects</div>
                </div>
                <div class="kpi-bg-icon"><i class="fas fa-project-diagram"></i></div>
            </div>
        </div>
    </div>

    {{-- ══ MAIN GRID ══ --}}
    <div class="row g-4 mb-4">

        {{-- Calendar --}}
        <div class="col-lg-8">
            <div class="db-card d-flex flex-column">
                <div class="db-card-head">
                    <span><i class="fas fa-calendar-alt me-2"></i>Calendar</span>
                    <button id="add-event-btn" class="db-btn-accent">
                        <i class="fas fa-plus me-1"></i>Add Event
                    </button>
                </div>
                <div class="db-card-body p-3" style="max-height: 36rem">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Loan Overview --}}
            <div class="db-card">
                <div class="db-card-head">
                    <span><i class="fas fa-hand-holding-usd me-2"></i>Loan Overview</span>
                </div>
                <div class="db-card-body p-4">
                    <div class="loan-wrap">
                        <canvas id="loanPieChart" width="100" height="100"></canvas>
                        <div class="loan-legend">
                            <div class="loan-item">
                                <span class="loan-dot" style="background:#ef4444"></span>
                                <div>
                                    <div class="loan-label">Payable</div>
                                    <div class="loan-val text-danger fw-bold">৳ {{ number_format($totalPayable, 2) }}</div>
                                </div>
                            </div>
                            <div class="loan-item mt-3">
                                <span class="loan-dot" style="background:#10b981"></span>
                                <div>
                                    <div class="loan-label">Receivable</div>
                                    <div class="loan-val text-success fw-bold">৳ {{ number_format($totalReceivable, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HerCycle --}}
            <div class="db-card">
                <div class="db-card-head db-card-head-rose">
                    <span><i class="fas fa-venus me-2"></i>HerCycle</span>
                </div>
                <div class="db-card-body p-4">
                    <div class="hc-row">
                        <div class="hc-dot hc-dot-last"></div>
                        <div>
                            <div class="hc-lbl">Last Period</div>
                            <div class="hc-val">{{ $lastPeriodStart ? $lastPeriodStart->format('d M, Y') : '—' }}</div>
                        </div>
                    </div>
                    <div class="hc-divider"></div>
                    <div class="hc-row">
                        <div class="hc-dot hc-dot-next"></div>
                        <div>
                            <div class="hc-lbl">Next Predicted</div>
                            <div class="hc-val">{{ $predictedNextStart ? $predictedNextStart->format('d M, Y') : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Disk Storage --}}
            <div class="db-card">
                <div class="db-card-head">
                    <span><i class="fas fa-server me-2"></i>Disk Storage</span>
                    <span class="db-head-badge">{{ $totalDisks }} disk(s)</span>
                </div>
                <div class="db-card-body p-4">
                    @php
                        $diskPct = $totalDiskCapacity > 0 ? min(100, round(($totalDiskUsed / $totalDiskCapacity) * 100)) : 0;
                        $diskColor = $diskPct >= 85 ? '#ef4444' : ($diskPct >= 60 ? '#f59e0b' : '#6366f1');
                    @endphp
                    <div class="disk-pct-label">
                        <span>{{ number_format($totalDiskUsed, 2) }} GB used</span>
                        <span class="fw-semibold" style="color:{{ $diskColor }}">{{ $diskPct }}%</span>
                    </div>
                    <div class="disk-bar">
                        <div class="disk-fill" style="width:{{ $diskPct }}%; background:{{ $diskColor }}"></div>
                    </div>
                    <div class="disk-pct-label mt-2">
                        <span class="text-muted small">Total: {{ number_format($totalDiskCapacity, 2) }} GB</span>
                        <span class="text-muted small">Free: {{ number_format($totalDiskCapacity - $totalDiskUsed, 2) }} GB</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ RUNNING EVENTS TABLE ══ --}}
    <div class="db-card">
        <div class="db-card-head db-card-head-event">
            <span><i class="fas fa-bolt me-2"></i>Running Events &amp; Expenses</span>
            <span class="db-head-badge">{{ $runningEventsWithExpenses->count() }} active</span>
        </div>
        <div class="db-card-body p-0">
            @if($runningEventsWithExpenses->count())
            <div class="table-responsive">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Event Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th class="text-end">Total Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($runningEventsWithExpenses as $i => $event)
                        <tr>
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $event['title'] }}</td>
                            <td class="small text-muted">{{ $event['start'] }}</td>
                            <td class="small text-muted">{{ $event['end'] ?? '—' }}</td>
                            <td class="text-end">
                                <span class="db-expense-chip">৳ {{ number_format($event['expense'], 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="db-empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No running events at the moment.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('css')
	<!-- Google Font: Inter -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- FullCalendar CSS -->
	<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

	<style>
		/* ═══════════════════════════════════════
		   DESIGN TOKENS — scoped vars, no global impact
		═══════════════════════════════════════ */
		:root {
			--db-font:       'Inter', system-ui, sans-serif;
			--glass:         rgba(255,255,255,0.58);
			--glass-md:      rgba(255,255,255,0.76);
			--glass-blur:    blur(20px) saturate(160%);
			--glass-border:  rgba(255,255,255,0.72);
			--glass-shadow:  0 4px 24px rgba(99,102,241,0.09), 0 1px 4px rgba(15,23,42,0.06), inset 0 1px 0 rgba(255,255,255,0.95);
			--glass-shadow-h:0 10px 44px rgba(99,102,241,0.16), inset 0 1px 0 rgba(255,255,255,1);
			--db-text:       #0f172a;
			--db-muted:      #64748b;
			--db-radius:     16px;
			--db-radius-sm:  9px;
			--brand:         #6366f1;
			--brand-dark:    #4f46e5;
		}

		/* ═══════════════════════════════════════
		   ROOT WRAPPER + LIGHT AURORA BG
		   (all rules scoped inside .db-root)
		═══════════════════════════════════════ */
		.db-root {
			position: relative;
		}
		@media (max-width: 767px) {
			.db-root { padding: 1rem .75rem 2rem; }
		}

		/* ═══════════════════════════════════════
		   WELCOME HERO
		═══════════════════════════════════════ */
		.db-root .db-hero {
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-wrap: wrap;
			gap: 1.5rem;
			background: linear-gradient(135deg, #c2c2c224 0%, #109ad508 100%);
			backdrop-filter: var(--glass-blur);
			-webkit-backdrop-filter: var(--glass-blur);
			border: 1px solid rgba(99,102,241,0.20);
			border-radius: var(--db-radius);
			padding: 2rem 2.25rem;
			margin-bottom: 2rem;
			position: relative;
			overflow: hidden;
			box-shadow: 0 8px 40px rgba(99,102,241,0.14), inset 0 1px 0 rgba(255,255,255,0.90);
		}
		/* White shine sweep */
		.db-root .db-hero::before {
			content: '';
			position: absolute;
			top: -40%; left: -25%;
			width: 55%; height: 190%;
			background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.38) 50%, transparent 60%);
			transform: skewX(-18deg);
			pointer-events: none;
		}
		.db-root .db-hero::after {
			content: '';
			position: absolute;
			bottom: -60px; right: -40px;
			width: 180px; height: 180px;
			background: radial-gradient(circle, rgba(168,85,247,0.10) 0%, transparent 70%);
			border-radius: 50%;
			pointer-events: none;
		}
		.db-root .db-hero-left { z-index: 1; }
		.db-root .db-date-chip {
			display: inline-flex;
			align-items: center;
			background: rgba(99,102,241,0.10);
			color: #4f46e5;
			font-size: .78rem;
			font-weight: 600;
			padding: .3rem .85rem;
			border-radius: 100px;
			margin-bottom: .85rem;
			letter-spacing: .01em;
			border: 1px solid rgba(99,102,241,0.20);
			backdrop-filter: blur(8px);
		}
		.db-root .db-greeting {
			font-size: 2rem;
			font-weight: 800;
			color: #1e1b4b;
			margin: 0 0 .4rem;
			letter-spacing: -.02em;
			line-height: 1.2;
		}
		.db-root .db-wave { animation: dbWave 2.4s ease-in-out infinite; display:inline-block; transform-origin: 70% 70%; }
		@keyframes dbWave {
			0%,100% { transform: rotate(0deg); }
			15%      { transform: rotate(14deg); }
			30%      { transform: rotate(-8deg); }
			45%      { transform: rotate(10deg); }
			60%      { transform: rotate(-4deg); }
		}
		.db-root .db-subtitle {
			margin: 0;
			color: #64748b;
			font-size: .93rem;
		}
		.db-root .db-hero-clock {
			z-index: 1;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: .5rem;
		}
		.db-root .db-hero-clock canvas {
			border-radius: 50%;
			box-shadow: 0 0 0 3px rgba(99,102,241,0.22), 0 8px 32px rgba(99,102,241,0.18);
		}
		.db-root .clock-tz-badge {
			font-size: .7rem;
			color: #6366f1;
			background: rgba(99,102,241,0.08);
			padding: .2rem .7rem;
			border-radius: 100px;
			border: 1px solid rgba(99,102,241,0.16);
		}
		@media (max-width: 575px) {
			.db-root .db-hero            { padding: 1.4rem 1.25rem; }
			.db-root .db-greeting        { font-size: 1.5rem; }
			.db-root .db-hero-clock canvas { width: 90px; height: 90px; }
		}

		/* ═══════════════════════════════════════
		   KPI STRIP
		═══════════════════════════════════════ */
		.db-root .kpi {
			position: relative;
			background: rgba(255,255,255,0.18);
			backdrop-filter: blur(24px) saturate(200%);
			-webkit-backdrop-filter: blur(24px) saturate(200%);
			border-radius: var(--db-radius);
			padding: 1.1rem 1.25rem 1rem;
			box-shadow:
				0 4px 24px rgba(99,102,241,0.10),
				0 1px 4px rgba(15,23,42,0.06),
				inset 0 1.5px 0 rgba(255,255,255,0.95),
				inset 0 -1px 0 rgba(255,255,255,0.30);
			border: 1px solid rgba(255,255,255,0.70);
			overflow: hidden;
			display: flex;
			align-items: center;
			gap: .9rem;
			transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
		}
		/* Top specular highlight (bright white edge = liquid glass look) */
		.db-root .kpi::before {
			content: '';
			position: absolute;
			top: 0; left: 0; right: 0;
			height: 2px;
			background: linear-gradient(90deg, transparent 5%, rgba(255,255,255,1) 50%, transparent 95%);
			border-radius: var(--db-radius) var(--db-radius) 0 0;
		}
		.db-root .kpi:hover {
			transform: translateY(-4px);
			background: rgba(255,255,255,0.30);
			box-shadow:
				0 10px 44px rgba(99,102,241,0.16),
				inset 0 1.5px 0 rgba(255,255,255,1),
				inset 0 -1px 0 rgba(255,255,255,0.40);
		}
		.db-root .kpi-icon-wrap {
			width: 44px; height: 44px;
			border-radius: 10px;
			display: flex; align-items: center; justify-content: center;
			flex-shrink: 0;
			font-size: 1.15rem;
			border: 1px solid rgba(255,255,255,0.65);
		}
		.db-root .kpi-body { flex: 1; min-width: 0; }
		.db-root .kpi-num {
			font-size: 1.55rem;
			font-weight: 800;
			line-height: 1;
			letter-spacing: -.03em;
		}
		.db-root .kpi-lbl {
			font-size: .72rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: .06em;
			color: var(--db-muted);
			margin-top: .2rem;
		}
		.db-root .kpi-sub { font-weight: 400; font-size: .68rem; }
		.db-root .kpi-bg-icon {
			position: absolute;
			right: -8px; bottom: -10px;
			font-size: 4.5rem;
			opacity: .07;
			pointer-events: none;
			line-height: 1;
		}
		/* KPI color variants */
		.db-root .kpi-indigo  { border-top: 2px solid rgba(99,102,241,0.55); }
		.db-root .kpi-indigo  .kpi-icon-wrap { background:rgba(99,102,241,0.10); color:#6366f1; }
		.db-root .kpi-indigo  .kpi-num       { color:#4f46e5; }
		.db-root .kpi-violet  { border-top: 2px solid rgba(139,92,246,0.55); }
		.db-root .kpi-violet  .kpi-icon-wrap { background:rgba(139,92,246,0.10); color:#7c3aed; }
		.db-root .kpi-violet  .kpi-num       { color:#6d28d9; }
		.db-root .kpi-rose    { border-top: 2px solid rgba(244,63,94,0.55); }
		.db-root .kpi-rose    .kpi-icon-wrap { background:rgba(244,63,94,0.10); color:#e11d48; }
		.db-root .kpi-rose    .kpi-num       { color:#be123c; }
		.db-root .kpi-amber   { border-top: 2px solid rgba(245,158,11,0.55); }
		.db-root .kpi-amber   .kpi-icon-wrap { background:rgba(245,158,11,0.10); color:#d97706; }
		.db-root .kpi-amber   .kpi-num       { color:#b45309; }
		.db-root .kpi-teal    { border-top: 2px solid rgba(20,184,166,0.55); }
		.db-root .kpi-teal    .kpi-icon-wrap { background:rgba(20,184,166,0.10); color:#0d9488; }
		.db-root .kpi-teal    .kpi-num       { color:#0f766e; }
		.db-root .kpi-emerald { border-top: 2px solid rgba(16,185,129,0.55); }
		.db-root .kpi-emerald .kpi-icon-wrap { background:rgba(16,185,129,0.10); color:#059669; }
		.db-root .kpi-emerald .kpi-num       { color:#047857; }

		/* ═══════════════════════════════════════
		   GLASS CARDS
		═══════════════════════════════════════ */
		.db-root .db-card {
			background: rgba(255,255,255,0.18);
			backdrop-filter: blur(24px) saturate(200%);
			-webkit-backdrop-filter: blur(24px) saturate(200%);
			border-radius: var(--db-radius);
			border: 1px solid rgba(255,255,255,0.70);
			box-shadow:
				0 4px 24px rgba(99,102,241,0.10),
				0 1px 4px rgba(15,23,42,0.06),
				inset 0 1.5px 0 rgba(255,255,255,0.95),
				inset 0 -1px 0 rgba(255,255,255,0.30);
			overflow: hidden;
			position: relative;
		}
		/* Top specular highlight */
		.db-root .db-card::before {
			content: '';
			position: absolute;
			top: 0; left: 0; right: 0;
			height: 2px;
			background: linear-gradient(90deg, transparent 5%, rgba(255,255,255,1) 50%, transparent 95%);
			border-radius: var(--db-radius) var(--db-radius) 0 0;
			z-index: 1;
			pointer-events: none;
		}
		.db-root .db-card-head {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: .85rem 1.4rem;
			font-size: .78rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .07em;
			color: var(--db-muted);
			border-bottom: 1px solid rgba(0,0,0,0.05);
			background: rgba(255,255,255,0.42);
		}
		.db-root .db-card-head-rose  { background: rgba(244,63,94,0.06);  color: #be123c; border-bottom-color: rgba(244,63,94,0.10); }
		.db-root .db-card-head-event { background: rgba(99,102,241,0.07); color: #4f46e5; border-bottom-color: rgba(99,102,241,0.10); }
		.db-root .db-card-body {}

		.db-root .db-head-badge {
			font-size: .71rem;
			font-weight: 600;
			background: rgba(99,102,241,0.10);
			color: #4f46e5;
			padding: .25rem .7rem;
			border-radius: 100px;
			text-transform: none;
			letter-spacing: 0;
			border: 1px solid rgba(99,102,241,0.20);
		}
		.db-root .db-btn-accent {
			font-size: .78rem;
			font-weight: 600;
			padding: .35rem .9rem;
			border-radius: 8px;
			background: rgba(99,102,241,0.12);
			color: #4f46e5;
			border: 1px solid rgba(99,102,241,0.25);
			cursor: pointer;
			transition: background .2s, transform .15s;
			text-transform: none;
			letter-spacing: 0;
			backdrop-filter: blur(6px);
		}
		.db-root .db-btn-accent:hover { background: rgba(99,102,241,0.22); transform: translateY(-1px); }

		/* ═══════════════════════════════════════
		   FULLCALENDAR — glass + compact desktop
		═══════════════════════════════════════ */
		.db-root #calendar { min-height: 200px; }
		@media (min-width: 992px) {
			.db-root .fc .fc-daygrid-day         { max-height: 58px; }
			.db-root .fc .fc-daygrid-day-frame   { min-height: 78px !important; }
			.db-root .fc .fc-daygrid-body        { font-size: .72rem; }
			/* .db-root .fc .fc-daygrid-day-number  { padding: 2px 5px !important; font-size: .7rem; } */
			.db-root .fc .fc-scrollgrid-sync-table { height: auto !important; }
		}
		.db-root .fc .fc-toolbar.fc-header-toolbar {
			background: rgba(99,102,241,0.10);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border: 1px solid rgba(99,102,241,0.18);
			border-bottom: none;
			padding: .45rem .85rem;
			border-radius: var(--db-radius-sm) var(--db-radius-sm) 0 0;
			margin-bottom: 0;
		}
		.db-root .fc .fc-toolbar-title { font-size: .9rem; font-weight: 700; color: #1e1b4b !important; }
		.db-root .fc .fc-button {
			background: rgba(255,255,255,0.72);
			border: 1px solid rgba(99,102,241,0.22);
			color: #4f46e5;
			font-size: .75rem;
			font-weight: 600;
			border-radius: 6px;
			padding: 3px 9px;
			transition: background .18s;
		}
		.db-root .fc .fc-button:hover,
		.db-root .fc .fc-button-active { background: rgba(99,102,241,0.12) !important; }
		.db-root .fc .fc-button-primary:not(:disabled):active { background: rgba(99,102,241,0.20) !important; box-shadow: none !important; }
		.db-root .fc-theme-standard .fc-scrollgrid { border-color: rgba(0,0,0,0.07); }
		.db-root .fc .fc-col-header-cell {
			background: rgba(99,102,241,0.05);
			border-color: rgba(0,0,0,0.06);
		}
		.db-root .fc .fc-col-header-cell-cushion {
			font-size: .72rem; font-weight: 700;
			color: #6366f1;
			text-transform: uppercase; letter-spacing: .06em;
			padding: 5px 4px;
			text-decoration: none;
		}
		.db-root .fc .fc-daygrid-day { background: transparent; border-color: rgba(0,0,0,0.05);}
		.db-root .fc .fc-daygrid-day-number {
			font-size: .76rem; font-weight: 600;
			color: #475569; padding: 3px 6px;
			text-decoration: none;
		}
		.db-root .fc .fc-day-today { background: rgba(99,102,241,0.08) !important; }
		.db-root .fc .fc-day-today .fc-daygrid-day-number {
			color: #4f46e5;
			background: rgba(99,102,241,0.14);
			border-radius: 50%;
			width: 22px; height: 22px;
			display: flex; align-items: center; justify-content: center;
			font-weight: 700;
		}
		.db-root .fc .fc-event {
			border: none !important;
			border-radius: 4px !important;
			font-size: .7rem; font-weight: 600;
			padding: 1px 4px;
		}
		@media (max-width: 767px) {
			.db-root #calendar { min-height: 240px; }
			.db-root .fc .fc-daygrid-day-number { padding: 2px 5px !important; font-size: .7rem; }
		}

		/* ═══════════════════════════════════════
		   LOAN WIDGET
		═══════════════════════════════════════ */
		.db-root .loan-wrap { display: flex; align-items: center; gap: 1.5rem; }
		.db-root .loan-legend { flex: 1; }
		.db-root .loan-item { display: flex; align-items: flex-start; gap: .65rem; }
		.db-root .loan-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: .35rem; flex-shrink: 0; }
		.db-root .loan-label { font-size: .75rem; color: var(--db-muted); font-weight: 500; }
		.db-root .loan-val   { font-size: 1.05rem; font-weight: 700; color: var(--db-text); }

		/* ═══════════════════════════════════════
		   HERCYCLE
		═══════════════════════════════════════ */
		.db-root .hc-row { display: flex; align-items: center; gap: .9rem; }
		.db-root .hc-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
		.db-root .hc-dot-last { background: #f43f5e; box-shadow: 0 0 0 3px rgba(244,63,94,0.18); }
		.db-root .hc-dot-next { background: #a855f7; box-shadow: 0 0 0 3px rgba(168,85,247,0.18); }
		.db-root .hc-lbl { font-size: .72rem; font-weight: 600; color: var(--db-muted); text-transform: uppercase; letter-spacing: .06em; }
		.db-root .hc-val { font-size: 1rem; font-weight: 700; color: var(--db-text); margin-top: .1rem; }
		.db-root .hc-divider { height: 1px; background: rgba(0,0,0,0.07); margin: 1rem 0; }

		/* ═══════════════════════════════════════
		   DISK STORAGE BAR
		═══════════════════════════════════════ */
		.db-root .disk-pct-label { display: flex; justify-content: space-between; font-size: .78rem; color: var(--db-muted); margin-bottom: .5rem; }
		.db-root .disk-bar { height: 7px; background: rgba(99,102,241,0.10); border-radius: 100px; overflow: hidden; }
		.db-root .disk-fill { height: 100%; border-radius: 100px; transition: width .8s ease; }

		/* ═══════════════════════════════════════
		   EVENTS TABLE
		═══════════════════════════════════════ */
		.db-root .db-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
		.db-root .db-table thead th {
			padding: .7rem 1.25rem;
			font-size: .7rem; font-weight: 700;
			text-transform: uppercase; letter-spacing: .07em;
			color: var(--db-muted);
			background: rgba(255,255,255,0.42);
			border-bottom: 1px solid rgba(0,0,0,0.06);
		}
		.db-root .db-table tbody tr { border-bottom: 1px solid rgba(0,0,0,0.04); transition: background .15s; color: var(--db-text); }
		.db-root .db-table tbody tr:last-child { border-bottom: none; }
		.db-root .db-table tbody tr:hover { background: rgba(99,102,241,0.04); }
		.db-root .db-table tbody td { padding: .75rem 1.25rem; vertical-align: middle; }
		.db-root .db-expense-chip {
			display: inline-flex; align-items: center;
			font-size: .8rem; font-weight: 700;
			background: rgba(16,185,129,0.10);
			color: #047857;
			border: 1px solid rgba(16,185,129,0.25);
			padding: .25rem .75rem; border-radius: 100px;
		}

		/* ═══════════════════════════════════════
		   EMPTY STATE
		═══════════════════════════════════════ */
		.db-root .db-empty-state {
			display: flex; flex-direction: column; align-items: center; justify-content: center;
			padding: 3rem 1rem; color: var(--db-muted); gap: .75rem;
		}
		.db-root .db-empty-state i { font-size: 2.5rem; opacity: .25; }
		.db-root .db-empty-state p { margin: 0; font-size: .9rem; }

		/* ═══════════════════════════════════════
		   COUNTER
		═══════════════════════════════════════ */
		.db-root .counter { font-weight: 800; letter-spacing: -.03em; }

	</style>

@endpush

@push('js')

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- FullCalendar -->
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

	<script>
	/* ── Counter up ── */
	(function () {
		document.querySelectorAll('.counter').forEach(el => {
			const target = parseInt(el.dataset.target ?? el.innerText) || 0;
			if (!target) return;
			let current = 0;
			const step = Math.max(1, Math.floor(target / 60));
			const tick = () => {
				current = Math.min(current + step, target);
				el.textContent = current.toLocaleString();
				if (current < target) requestAnimationFrame(tick);
			};
			setTimeout(tick, 150 + Math.random() * 400);
		});
	})();

	/* ── Loan Donut ── */
	document.addEventListener('DOMContentLoaded', function () {
		const ctx = document.getElementById('loanPieChart').getContext('2d');
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ['Payable', 'Receivable'],
				datasets: [{
					data: [{{ $totalPayable }}, {{ $totalReceivable }}],
					backgroundColor: ['#ef4444', '#10b981'],
					borderWidth: 0,
					hoverOffset: 6
				}]
			},
			options: {
				cutout: '68%',
				responsive: false,
				plugins: {
					legend: { display: false },
					tooltip: { callbacks: { label: ctx => ' ৳ ' + ctx.parsed.toLocaleString() } }
				}
			}
		});
	});

	/* ── FullCalendar ── */
	document.addEventListener('DOMContentLoaded', function () {
		const calEl = document.getElementById('calendar');
		const mobile = window.matchMedia('(max-width: 767.98px)').matches;
		const calendar = new FullCalendar.Calendar(calEl, {
			initialView: 'dayGridMonth',
			headerToolbar: mobile
				? { left: 'prev', center: 'title', right: 'next' }
				: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
			editable: true, selectable: false, selectMirror: false, dayMaxEvents: true,
			events: '/calendar/events',
			eventResizableFromStart: true, eventDurationEditable: true,
			dateClick: function (info) {
				const now = Date.now();
				if (info.dayEl._lastClick && now - info.dayEl._lastClick < 350) {
					// double-click detected
					info.dayEl._lastClick = 0;
					const title = prompt('Add event on ' + info.dateStr + ':');
					if (!title) return;
					fetch('/calendar/events/store', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
						body: JSON.stringify({ title, start: info.dateStr, allDay: true })
					}).then(r => r.json()).then(ev => calendar.addEvent(ev));
				} else {
					info.dayEl._lastClick = now;
				}
			},
			eventClick: function () {},
			eventDidMount: function (info) {
				let timer = null;
				info.el.addEventListener('click', () => {
					if (timer) return;
					timer = setTimeout(() => { timer = null; alert('Title: ' + info.event.title); }, 250);
				});
				info.el.addEventListener('dblclick', () => {
					if (timer) { clearTimeout(timer); timer = null; }
					const action = prompt('Type:\ncreate – add new\nedit – edit this\ndelete – delete this');
					if (action === 'create') {
						const t = prompt('New event title:'); if (!t) return;
						const d = prompt('Date (YYYY-MM-DD):', info.event.startStr); if (!d) return;
						fetch('/calendar/events/store', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ title: t, start: d }) }).then(r => r.json()).then(ev => calendar.addEvent(ev));
					} else if (action === 'edit') {
						const nt = prompt('Edit title:', info.event.title);
						if (nt && nt !== info.event.title) {
							fetch('/calendar/events/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id: info.event.id, title: nt }) }).then(() => info.event.setProp('title', nt));
						}
					} else if (action === 'delete') {
						if (confirm('Delete "' + info.event.title + '"?')) {
							fetch('/calendar/events/delete', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id: info.event.id }) }).then(() => info.event.remove());
						}
					}
				});
			},
			eventDrop: function (info) {
				fetch('/calendar/events/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id: info.event.id, start: info.event.startStr, end: info.event.endStr }) });
			},
			eventResize: function (info) {
				let end = info.event.end;
				if (end) {
					end = new Date(end); end.setDate(end.getDate() - 1);
					const pad = n => String(n).padStart(2, '0');
					end = `${end.getFullYear()}-${pad(end.getMonth()+1)}-${pad(end.getDate())}T${pad(end.getHours())}:${pad(end.getMinutes())}:${pad(end.getSeconds())}`;
				} else { end = info.event.endStr; }
				fetch('/calendar/events/update', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id: info.event.id, start: info.event.startStr, end }) });
			}
		});
		calendar.render();

		document.getElementById('add-event-btn').addEventListener('click', function () {
			const t = prompt('Event title:'); if (!t) return;
			const d = prompt('Date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]); if (!d) return;
			fetch('/calendar/events/store', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ title: t, start: d }) }).then(r => r.json()).then(ev => calendar.addEvent(ev));
		});
	});

	/* ── Analog Clock ── */
	(function () {
		const canvas = document.getElementById('analog-clock');
		if (!canvas) return;
		const ctx = canvas.getContext('2d');
		const W = canvas.width, H = canvas.height;
		const cx = W / 2, cy = H / 2;
		const R = Math.min(cx, cy) - 4;

		function draw() {
			ctx.clearRect(0, 0, W, H);
			const now = new Date();
			const sec = now.getSeconds(), min = now.getMinutes(), hr = now.getHours() % 12;

			/* face */
			const bg = ctx.createRadialGradient(cx, cy, R * .1, cx, cy, R);
			bg.addColorStop(0, '#1e1b4b'); bg.addColorStop(1, '#0f0a24');
			ctx.beginPath(); ctx.arc(cx, cy, R, 0, 2 * Math.PI);
			ctx.fillStyle = bg; ctx.fill();

			/* bezel */
			ctx.beginPath(); ctx.arc(cx, cy, R, 0, 2 * Math.PI);
			ctx.strokeStyle = 'rgba(255,255,255,.12)'; ctx.lineWidth = 2; ctx.stroke();

			/* ticks */
			for (let i = 0; i < 60; i++) {
				const a = (i * Math.PI / 30) - Math.PI / 2;
				const major = i % 5 === 0;
				const r1 = R - (major ? 10 : 5), r2 = R - 2;
				ctx.beginPath();
				ctx.moveTo(cx + Math.cos(a) * r1, cy + Math.sin(a) * r1);
				ctx.lineTo(cx + Math.cos(a) * r2, cy + Math.sin(a) * r2);
				ctx.strokeStyle = major ? 'rgba(255,255,255,.55)' : 'rgba(255,255,255,.18)';
				ctx.lineWidth = major ? 2 : 1; ctx.stroke();
			}

			/* numerals */
			ctx.fillStyle = 'rgba(255,255,255,.8)';
			ctx.font = `bold ${Math.round(R * .13)}px Inter, sans-serif`;
			ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
			for (let n = 1; n <= 12; n++) {
				const a = (n * Math.PI / 6) - Math.PI / 2;
				ctx.fillText(n, cx + Math.cos(a) * (R - 22), cy + Math.sin(a) * (R - 22));
			}

			const hand = (angle, length, width, color) => {
				ctx.beginPath(); ctx.lineWidth = width; ctx.lineCap = 'round'; ctx.strokeStyle = color;
				ctx.moveTo(cx, cy);
				ctx.lineTo(cx + Math.cos(angle) * length, cy + Math.sin(angle) * length);
				ctx.stroke();
			};

			hand((hr + min / 60 + sec / 3600) * Math.PI / 6 - Math.PI / 2, R * .48, 5, '#f0abfc');
			hand((min + sec / 60) * Math.PI / 30 - Math.PI / 2,             R * .68, 3.5, '#c4b5fd');
			hand(sec * Math.PI / 30 - Math.PI / 2,                          R * .80, 1.5, '#f43f5e');

			/* center dot */
			ctx.beginPath(); ctx.arc(cx, cy, 5, 0, 2 * Math.PI);
			ctx.fillStyle = '#fff'; ctx.fill();
			ctx.beginPath(); ctx.arc(cx, cy, 2.5, 0, 2 * Math.PI);
			ctx.fillStyle = '#f43f5e'; ctx.fill();
		}

		draw();
		setInterval(draw, 1000);
	})();
	</script>
@endpush
