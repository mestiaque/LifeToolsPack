@extends('me::master')

@section('title', 'HerCycle - Period Tracking')

@push('css')
<style>
    :root {
        --her-pink-light: #FFE4E6;
        --her-pink: #F472B6;
        --her-pink-dark: #DB2777;
        --her-purple-light: #F3E8FF;
        --her-purple: #A855F7;
        --her-purple-dark: #7C3AED;
        --her-green-light: #DCFCE7;
        --her-green: #22C55E;
        --her-yellow-light: #FEF9C3;
        --her-yellow: #EAB308;
        --her-red-light: #FEE2E2;
        --her-red: #EF4444;
    }

    .hercycle-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .hercycle-header {
        background: linear-gradient(135deg, var(--her-pink-light) 0%, var(--her-purple-light) 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(212, 123, 179, 0.15);
    }

    .hercycle-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--her-pink-dark);
        margin-bottom: 10px;
    }

    .hercycle-subtitle {
        color: var(--her-purple-dark);
        font-size: 1.1rem;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid var(--her-pink);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-card.prediction {
        border-left-color: var(--her-purple);
    }

    .stat-card.fertile {
        border-left-color: var(--her-green);
    }

    .stat-card.pms {
        border-left-color: var(--her-yellow);
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--her-pink-dark);
        margin-top: 5px;
    }

    .stat-date {
        font-size: 0.9rem;
        color: var(--her-purple-dark);
        margin-top: 5px;
    }

    /* Calendar */
    .calendar-container {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .calendar-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--her-pink-dark);
    }

    .calendar-nav {
        display: flex;
        gap: 10px;
    }

    .calendar-nav-btn {
        background: var(--her-pink-light);
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        color: var(--her-pink-dark);
        cursor: pointer;
        transition: background 0.2s;
    }

    .calendar-nav-btn:hover {
        background: var(--her-pink);
        color: white;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }

    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        color: #6B7280;
        padding: 10px;
        font-size: 0.85rem;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        font-size: 0.9rem;
    }

    .calendar-day:hover {
        background: var(--her-pink-light);
    }

    .calendar-day.other-month {
        color: #D1D5DB;
    }

    .calendar-day.today {
        background: var(--her-pink-light);
        font-weight: 700;
        border: 2px solid var(--her-pink);
    }

    .calendar-day.period {
        background: var(--her-red-light);
    }

    .calendar-day.period.light {
        background: #FFF1F2;
    }

    .calendar-day.period.medium {
        background: #FFE4E6;
    }

    .calendar-day.period.heavy {
        background: var(--her-red-light);
    }

    .calendar-day.fertile {
        background: var(--her-green-light);
    }

    .calendar-day.pms {
        background: var(--her-yellow-light);
    }

    .calendar-day.predicted {
        border: 2px dashed var(--her-purple);
    }

    .calendar-day.ovulation {
        background: var(--her-purple-light);
        font-weight: 700;
    }

    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #E5E7EB;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #6B7280;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .btn-her {
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
    }

    .btn-her-primary {
        background: linear-gradient(135deg, var(--her-pink) 0%, var(--her-pink-dark) 100%);
        color: white;
    }

    .btn-her-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(212, 123, 179, 0.4);
    }

    .btn-her-secondary {
        background: var(--her-purple-light);
        color: var(--her-purple-dark);
    }

    .btn-her-secondary:hover {
        background: var(--her-purple);
        color: white;
    }

    .btn-her-outline {
        background: white;
        border: 2px solid var(--her-pink);
        color: var(--her-pink-dark);
    }

    .btn-her-outline:hover {
        background: var(--her-pink-light);
    }

    /* Charts Section */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--her-pink-dark);
        margin-bottom: 15px;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlide 0.3s ease;
    }

    @keyframes modalSlide {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .modal-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--her-pink-dark);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6B7280;
        padding: 5px;
        line-height: 1;
    }

    .modal-close:hover {
        color: var(--her-red);
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--her-pink);
    }

    .flow-buttons {
        display: flex;
        gap: 10px;
    }

    .flow-btn {
        flex: 1;
        padding: 12px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .flow-btn:hover {
        border-color: var(--her-pink);
    }

    .flow-btn.active {
        background: var(--her-pink-light);
        border-color: var(--her-pink);
        color: var(--her-pink-dark);
    }

    .flow-btn.light.active {
        background: #FFF1F2;
        border-color: #FCA5A5;
        color: #DC2626;
    }

    .flow-btn.medium.active {
        background: #FFE4E6;
        border-color: var(--her-pink);
        color: var(--her-pink-dark);
    }

    .flow-btn.heavy.active {
        background: var(--her-red-light);
        border-color: var(--her-red);
        color: var(--her-red);
    }

    /* Symptom Checkboxes */
    .symptom-group {
        margin-bottom: 15px;
    }

    .symptom-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
        display: block;
    }

    .symptom-checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .symptom-checkbox {
        display: none;
    }

    .symptom-checkbox + label {
        padding: 8px 15px;
        border-radius: 20px;
        background: #F3F4F6;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .symptom-checkbox:checked + label {
        background: var(--her-pink-light);
        border-color: var(--her-pink);
        color: var(--her-pink-dark);
    }

    /* Range Slider */
    .range-container {
        margin-top: 10px;
    }

    .range-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #6B7280;
        margin-top: 5px;
    }

    input[type="range"] {
        width: 100%;
        height: 8px;
        border-radius: 5px;
        background: #E5E7EB;
        appearance: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--her-pink);
        cursor: pointer;
    }

    /* Notification Toggles */
    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #E5E7EB;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .toggle-switch {
        position: relative;
        width: 50px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #E5E7EB;
        transition: 0.3s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: var(--her-pink);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    /* Period History */
    .history-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-radius: 10px;
        background: #F9FAFB;
        margin-bottom: 10px;
    }

    .history-date {
        font-weight: 600;
        color: #374151;
    }

    .history-flow {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .history-flow.light {
        background: #FFF1F2;
        color: #DC2626;
    }

    .history-flow.medium {
        background: #FFE4E6;
        color: var(--her-pink-dark);
    }

    .history-flow.heavy {
        background: var(--her-red-light);
        color: var(--her-red);
    }

    /* Alerts */
    .alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: var(--her-green-light);
        color: #166534;
    }

    .alert-info {
        background: var(--her-purple-light);
        color: var(--her-purple-dark);
    }

    .alert-warning {
        background: var(--her-yellow-light);
        color: #92400E;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .calendar-day {
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="hercycle-container">
    <!-- Header -->
    <div class="hercycle-header">
        <h1 class="hercycle-title">💝 HerCycle</h1>
        <p class="hercycle-subtitle">Welcome back, {{ $profile->name }}! Track your cycle with love and care.</p>
    </div>

    <!-- Alerts -->
    @if($nextPeriod)
        @php
            $daysUntilPeriod = \Carbon\Carbon::now()->diffInDays($nextPeriod['start'], false);
        @endphp
        @if($daysUntilPeriod >= 0 && $daysUntilPeriod <= 7)
            <div class="alert alert-warning">
                <span>📅</span>
                <span>Your next period is expected in {{ $daysUntilPeriod }} day(s)!</span>
            </div>
        @endif
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Avg Cycle Length</div>
            <div class="stat-value">{{ $stats['averageCycleLength'] }} days</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Period Length</div>
            <div class="stat-value">{{ $stats['averagePeriodLength'] }} days</div>
        </div>
        <div class="stat-card prediction">
            <div class="stat-label">Next Period</div>
            @if($nextPeriod)
                <div class="stat-value">{{ $nextPeriod['start']->format('M d') }}</div>
                <div class="stat-date">{{ $nextPeriod['start']->diffForHumans() }}</div>
            @else
                <div class="stat-value">--</div>
            @endif
        </div>
        <div class="stat-card fertile">
            <div class="stat-label">Fertile Window</div>
            @if($fertileWindow)
                <div class="stat-value">{{ $fertileWindow['start']->format('M d') }} - {{ $fertileWindow['end']->format('M d') }}</div>
            @else
                <div class="stat-value">--</div>
            @endif
        </div>
        <div class="stat-card pms">
            <div class="stat-label">PMS Phase</div>
            @if($pmsPeriod)
                <div class="stat-value">{{ $pmsPeriod['start']->format('M d') }}</div>
                <div class="stat-date">Prepare for {{ $pmsPeriod['start']->diffInDays(\Carbon\Carbon::now()) }} days</div>
            @else
                <div class="stat-value">--</div>
            @endif
        </div>
        <div class="stat-card">
            <div class="stat-label">Cycle Regularity</div>
            @if($stats['isRegular'] !== null)
                <div class="stat-value">{{ $stats['isRegular'] ? '✅ Regular' : '⚠️ Irregular' }}</div>
            @else
                <div class="stat-value">📊 Need more data</div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn-her btn-her-primary" onclick="openModal('periodModal')">
            <span>🩸</span> Log Period
        </button>
        <button class="btn-her btn-her-secondary" onclick="openModal('symptomModal')">
            <span>📝</span> Log Symptoms
        </button>
        <button class="btn-her btn-her-outline" onclick="openModal('profileModal')">
            <span>⚙️</span> Settings
        </button>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <div class="calendar-header">
            <h2 class="calendar-title">{{ $calendarData['monthName'] }}</h2>
            <div class="calendar-nav">
                <button class="calendar-nav-btn" onclick="changeMonth(-1)">← Prev</button>
                <button class="calendar-nav-btn" onclick="changeMonth(1)">Next →</button>
            </div>
        </div>
        
        <div class="calendar-grid">
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
            
            @foreach($calendarData['days'] as $day)
                <div class="calendar-day @if(!$day['isCurrentMonth']) other-month @endif @if($day['isToday']) today @endif @if($day['isPeriod']) period {{ $day['flowIntensity'] }} @endif @if($day['isFertile']) fertile @endif @if($day['isPMS']) pms @endif @if($day['isPredictedPeriod'] && !$day['isPeriod']) predicted @endif @if($day['isOvulation']) ovulation @endif"
                     onclick="showDayDetails('{{ $day['date'] }}')">
                    <span>{{ $day['day'] }}</span>
                    @if($day['isOvulation'])
                        <span>🥚</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="calendar-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: var(--her-red-light)"></div>
                <span>Period</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: var(--her-green-light)"></div>
                <span>Fertile</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: var(--her-purple-light)"></div>
                <span>Ovulation</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: var(--her-yellow-light)"></div>
                <span>PMS</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="border: 2px dashed var(--her-purple)"></div>
                <span>Predicted</span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">📊 Cycle Length Trends</h3>
            <canvas id="cycleChart" height="200"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">🩸 Flow Intensity</h3>
            <canvas id="flowChart" height="200"></canvas>
        </div>
    </div>

    <!-- Period History -->
    <div class="chart-card" style="margin-bottom: 30px;">
        <h3 class="chart-title">📅 Period History</h3>
        <div class="history-list">
            @forelse($periods as $period)
                <div class="history-item">
                    <div>
                        <div class="history-date">{{ $period->start_date->format('M d, Y') }} - {{ $period->end_date ? $period->end_date->format('M d, Y') : 'Ongoing' }}</div>
                        <small style="color: #6B7280;">{{ $period->start_date->diffInDays($period->end_date ?? $period->start_date) + 1 }} days</small>
                    </div>
                    @if($period->flow_intensity)
                        <span class="history-flow {{ $period->flow_intensity }}">{{ $period->flow_intensity }}</span>
                    @endif
                </div>
            @empty
                <p style="text-align: center; color: #6B7280; padding: 20px;">No periods recorded yet. Start tracking today!</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Period Modal -->
<div class="modal-overlay" id="periodModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">🩸 Log Period</h3>
            <button class="modal-close" onclick="closeModal('periodModal')">&times;</button>
        </div>
        <form action="{{ route('admin.hercycle.period.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Flow Intensity</label>
                <div class="flow-buttons">
                    <button type="button" class="flow-btn light" onclick="selectFlow(this, 'light')">Light</button>
                    <button type="button" class="flow-btn medium" onclick="selectFlow(this, 'medium')">Medium</button>
                    <button type="button" class="flow-btn heavy" onclick="selectFlow(this, 'heavy')">Heavy</button>
                </div>
                <input type="hidden" name="flow_intensity" id="flowIntensity">
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-input" rows="3" placeholder="Any notes..."></textarea>
            </div>
            <button type="submit" class="btn-her btn-her-primary" style="width: 100%;">Save Period</button>
        </form>
    </div>
</div>

<!-- Symptom Modal -->
<div class="modal-overlay" id="symptomModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">📝 Log Symptoms & Mood</h3>
            <button class="modal-close" onclick="closeModal('symptomModal')">&times;</button>
        </div>
        <form action="{{ route('admin.hercycle.symptom.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
            
            <div class="symptom-group">
                <label class="symptom-label">Physical Symptoms</label>
                <div class="symptom-checkboxes">
                    @foreach(['cramps', 'bloating', 'headaches', 'breast_tenderness', 'fatigue', 'backache'] as $symptom)
                        <input type="checkbox" name="physical_symptoms[]" value="{{ $symptom }}" id="phys_{{ $symptom }}" class="symptom-checkbox">
                        <label for="phys_{{ $symptom }}">{{ ucwords(str_replace('_', ' ', $symptom)) }}</label>
                    @endforeach
                </div>
            </div>

            <div class="symptom-group">
                <label class="symptom-label">Emotional State</label>
                <div class="symptom-checkboxes">
                    @foreach(['happy', 'sad', 'anxious', 'irritable', 'energetic', 'calm', 'moody'] as $mood)
                        <input type="checkbox" name="emotional_symptoms[]" value="{{ $mood }}" id="emo_{{ $mood }}" class="symptom-checkbox">
                        <label for="emo_{{ $mood }}">{{ ucfirst($mood) }}</label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Sleep Quality: <span id="sleepValue">5</span>/10</label>
                <div class="range-container">
                    <input type="range" name="sleep_quality" min="1" max="10" value="5" oninput="document.getElementById('sleepValue').textContent = this.value">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Energy Level: <span id="energyValue">5</span>/10</label>
                <div class="range-container">
                    <input type="range" name="energy_level" min="1" max="10" value="5" oninput="document.getElementById('energyValue').textContent = this.value">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Custom Symptoms</label>
                <input type="text" name="custom_symptoms" class="form-input" placeholder="Add your own symptoms...">
            </div>

            <button type="submit" class="btn-her btn-her-secondary" style="width: 100%;">Save Symptoms</button>
        </form>
    </div>
</div>

<!-- Profile/Settings Modal -->
<div class="modal-overlay" id="profileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">⚙️ Settings</h3>
            <button class="modal-close" onclick="closeModal('profileModal')">&times;</button>
        </div>
        
        <form action="{{ route('admin.hercycle.profile.update', $profile->id) }}" method="POST">
            @csrf
            @method('PUT')
            <h4 style="margin-bottom: 15px; color: var(--her-pink-dark);">Profile Information</h4>
            
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" value="{{ $profile->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Age</label>
                <input type="number" name="age" class="form-input" value="{{ $profile->age }}" min="10" max="100">
            </div>
            <div class="form-group">
                <label class="form-label">Average Cycle Length (days)</label>
                <input type="number" name="cycle_length" class="form-input" value="{{ $profile->cycle_length }}" min="20" max="45">
            </div>
            <div class="form-group">
                <label class="form-label">Average Period Length (days)</label>
                <input type="number" name="period_length" class="form-input" value="{{ $profile->period_length }}" min="2" max="10">
            </div>
            
            <button type="submit" class="btn-her btn-her-primary" style="width: 100%; margin-bottom: 20px;">Update Profile</button>
        </form>

        <hr style="margin: 20px 0; border-color: #E5E7EB;">

        <h4 style="margin-bottom: 15px; color: var(--her-pink-dark);">🔔 Notification Settings</h4>
        <form action="{{ route('admin.hercycle.notifications.update', $profile->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="notification-item">
                <span>Period Reminder</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="period_reminder" value="1" {{ $profile->notification->first() && $profile->notification->first()->period_reminder ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notification-item">
                <span>PMS Reminder</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="pms_reminder" value="1" {{ $profile->notification->first() && $profile->notification->first()->pms_reminder ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notification-item">
                <span>Fertile Window Reminder</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="fertile_reminder" value="1" {{ $profile->notification->first() && $profile->notification->first()->fertile_reminder ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notification-item">
                <span>Symptom Log Reminder</span>
                <label class="toggle-switch">
                    <input type="checkbox" name="symptom_reminder" value="1" {{ $profile->notification->first() && $profile->notification->first()->symptom_reminder ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <button type="submit" class="btn-her btn-her-secondary" style="width: 100%;">Save Notifications</button>
        </form>
    </div>
</div>

<!-- Day Detail Modal -->
<div class="modal-overlay" id="dayDetailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="dayDetailTitle">Day Details</h3>
            <button class="modal-close" onclick="closeModal('dayDetailModal')">&times;</button>
        </div>
        <div id="dayDetailContent">
            <!-- Content loaded dynamically -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Flow intensity selection
    function selectFlow(btn, value) {
        document.querySelectorAll('.flow-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('flowIntensity').value = value;
    }

    // Change month
    let currentMonth = '{{ $calendarData["month"] }}';
    
    function changeMonth(direction) {
        const [year, month] = currentMonth.split('-');
        let date = new Date(year, parseInt(month) - 1 + direction, 1);
        currentMonth = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
        
        fetch(`{{ route('admin.hercycle.month-data') }}?month=${currentMonth}`)
            .then(response => response.json())
            .then(data => {
                // Update calendar - reload page for simplicity
                window.location.reload();
            });
    }

    // Show day details
    function showDayDetails(date) {
        document.getElementById('dayDetailTitle').textContent = new Date(date).toLocaleDateString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
        
        const content = document.getElementById('dayDetailContent');
        content.innerHTML = '<p style="text-align: center; color: #6B7280;">Loading...</p>';
        
        openModal('dayDetailModal');
        
        // Find day data from calendar
        const dayData = @json($calendarData['days']).find(d => d.date === date);
        
        if (dayData) {
            let html = '';
            
            if (dayData.isPeriod) {
                html += '<div class="alert alert-warning">🩸 Period Day (' + (dayData.flowIntensity || 'unknown') + ')</div>';
            }
            if (dayData.isFertile) {
                html += '<div class="alert alert-info">🌸 Fertile Window</div>';
            }
            if (dayData.isOvulation) {
                html += '<div class="alert alert-info">🥚 Ovulation Day</div>';
            }
            if (dayData.isPMS) {
                html += '<div class="alert alert-warning">😔 PMS Phase</div>';
            }
            if (dayData.isPredictedPeriod) {
                html += '<div class="alert alert-info">📅 Predicted Period</div>';
            }
            
            if (dayData.hasSymptoms) {
                const symptoms = dayData.symptoms;
                html += '<h4 style="margin-top: 15px;">Logged Symptoms</h4>';
                if (symptoms.physical_symptoms && symptoms.physical_symptoms.length > 0) {
                    html += '<p><strong>Physical:</strong> ' + symptoms.physical_symptoms.join(', ') + '</p>';
                }
                if (symptoms.emotional_symptoms && symptoms.emotional_symptoms.length > 0) {
                    html += '<p><strong>Emotional:</strong> ' + symptoms.emotional_symptoms.join(', ') + '</p>';
                }
                if (symptoms.sleep_quality) {
                    html += '<p><strong>Sleep Quality:</strong> ' + symptoms.sleep_quality + '/10</p>';
                }
                if (symptoms.energy_level) {
                    html += '<p><strong>Energy Level:</strong> ' + symptoms.energy_level + '/10</p>';
                }
            }
            
            if (!html) {
                html = '<p style="text-align: center; color: #6B7280;">No data for this day. <a href="#" onclick="openModal(\'symptomModal\'); closeModal(\'dayDetailModal\');">Log symptoms?</a></p>';
            }
            
            content.innerHTML = html;
        }
    }

    // Initialize Charts
    const cycleCtx = document.getElementById('cycleChart').getContext('2d');
    const cycleLengths = @json($stats['cycleLengths'] ?? []);
    
    new Chart(cycleCtx, {
        type: 'line',
        data: {
            labels: cycleLengths.map((_, i) => 'Cycle ' + (i + 1)),
            datasets: [{
                label: 'Cycle Length (days)',
                data: cycleLengths,
                borderColor: '#F472B6',
                backgroundColor: 'rgba(244, 114, 182, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 20,
                    max: 35
                }
            }
        }
    });

    const flowCtx = document.getElementById('flowChart').getContext('2d');
    const flowData = @json($periods->pluck('flow_intensity')->countBy());
    
    new Chart(flowCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(flowData),
            datasets: [{
                label: 'Flow Intensity Count',
                data: Object.values(flowData),
                backgroundColor: [
                    '#FCA5A5',
                    '#F472B6',
                    '#EF4444'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
