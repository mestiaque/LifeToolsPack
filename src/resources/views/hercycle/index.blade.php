@extends('me::master')

@section('title', 'HerCycle - Period Tracking')

@push('buttons')
    <button class="btn btn-encodex-create align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#periodModal">
        <span>🩸</span> Log Period
    </button>
    <button class="btn btn-encodex-edit align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#profileModal">
        <span>⚙️</span> Settings
    </button>
@endpush

@push('css')
<style>
    .bg-gradient-her {
        background: linear-gradient(135deg, #FFE4E6 0%, #F3E8FF 100%);
    }
    .text-her-pink { color: #DB2777; }
    .text-her-purple { color: #7C3AED; }
    .border-her-pink { border-left-color: #F472B6 !important; }
    .border-her-purple { border-left-color: #A855F7 !important; }
    .border-her-green { border-left-color: #22C55E !important; }
    .bg-her-light { background-color: #FFF8FA; }
    .bg-her-light-pink { background-color: #fbe5fd; }
    /* New Premium Profile Header Styles */
    .profile-header-premium {
        background: linear-gradient(135deg, #FFF0F5 0%, #eeebff 50%, #eff6ff 100%);
        border: 2px solid rgba(255, 255, 255, 0.95);
        box-shadow: 0 20px 40px rgba(219, 39, 119, 0.12), inset 0 0 20px rgba(255,255,255,0.8);
        position: relative;
        overflow: hidden;
    }

    .bgM{
        background: linear-gradient(135deg, #FFF0F5 0%, #eeebff 50%, #eff6ff 100%) !important;
    }
    .stat-pill {
        background: #ffddfea6;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        flex: 1 1 auto;
        min-width: 200px;
    }
    .stat-pill:hover {
        transform: translateY(-6px) scale(1.02);
        background: #f3b4f8f2;
        border-color: #ffffff;
        box-shadow: 0 15px 30px rgba(168, 85, 247, 0.12), 0 5px 10px rgba(219, 39, 119, 0.05);
    }
    .bg-pink-soft { background-color: #FDF2F8; border: 1px solid #FCE7F3; }
    .bg-purple-soft { background-color: #F5F3FF; border: 1px solid #EDE9FE; }
    .bg-red-soft { background-color: #FEF2F2; border: 1px solid #FEE2E2; }
    .bg-blue-soft { background-color: #EFF6FF; border: 1px solid #DBEAFE; }
    .bg-orange-soft { background-color: #FFF7ED; border: 1px solid #FFEDD5; }
    .bg-green-soft { background-color: #F0FDF4; border: 1px solid #DCFCE7; }
</style>

@endpush

@section('content')
<div class="container-fluid py-4">
        <!-- Header -->
    <div class="profile-header-premium rounded-4 p-4 p-md-5 mb-5 mt-2">
        <!-- Decorative Background Element -->
        <div style="font-size: 20rem; position: absolute; right: -50px; top: -140px; opacity: 0.15; transform: rotate(15deg); pointer-events: none; mix-blend-mode: multiply;">🌸</div>

        <div class="position-relative z-1">
            <div class="d-flex flex-wrap align-items-center mb-4 gap-4">
                <div class=" rounded-circle shadow d-flex justify-content-center align-items-center border border-3 border-white p-1" style="width: 85px; height: 85px; overflow: hidden;">
                    <img src="{{ asset('assets/img/girlcartoon.png') }}" alt="Profile Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                <div>
                    <h1 class="text-her-pink fw-bolder mb-1" style="letter-spacing: 0.5px; font-size: 4.5rem;">{{ $profile->name }}</h1>
                    {{-- <h5 class="text-secondary mb-0 fw-normal fs-5">Welcome back, <span class="text-her-purple fw-bold">{{ $profile->name }}</span>! ✨</h5> --}}
                </div>
            </div>

            @php
                $bmi = null;
                $bmiColor = 'text-dark';
                $detailedAge = '--';
                if ($profile->weight && $profile->height) {
                    $heightInMeters = $profile->height / 100;
                    if ($heightInMeters > 0) {
                        $bmi = round($profile->weight / ($heightInMeters * $heightInMeters), 1);
                        if($bmi < 18.5) $bmiColor = 'text-info';
                        elseif($bmi >= 18.5 && $bmi <= 24.9) $bmiColor = 'text-success';
                        elseif($bmi >= 25 && $bmi <= 29.9) $bmiColor = 'text-warning';
                        else $bmiColor = 'text-danger';
                    }
                }

                if ($profile->dob) {
                    $now = \Carbon\Carbon::now();
                    $dob = \Carbon\Carbon::parse($profile->dob);
                    $diff = $now->diff($dob);
                    $detailedAge = $diff->y . ' Y - ' . $diff->m . ' M - ' . $diff->d . ' D';
                }
            @endphp

            <div class="d-flex flex-wrap gap-3 mt-4 w-100">
                <!-- Stat Pill -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-pink-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">🎂</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">DATE OF BIRTH</div>
                        <div class="fw-bolder text-dark fs-5 mt-1" style="line-height:1;">{{ $profile->dob ? \Carbon\Carbon::parse($profile->dob)->format('d M, Y') : '--' }}</div>
                    </div>
                </div>
                <!-- Age -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-purple-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">✨</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">AGE</div>
                        <div class="fw-bolder text-dark mt-1" style="font-size: 1.15rem; line-height:1;">{{ $detailedAge }}</div>
                    </div>
                </div>
                <!-- Blood -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-red-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">🩸</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">BLOOD GROUP</div>
                        <div class="fw-bolder text-danger fs-5 mt-1" style="line-height:1;">{{ $profile->blood_group ?? '--' }}</div>
                    </div>
                </div>
                <!-- Height -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-blue-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">📏</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">HEIGHT</div>
                        <div class="fw-bolder text-dark fs-5 mt-1" style="line-height:1;">{{ $profile->height ? $profile->height : '--' }} <span class="fw-normal small text-secondary">cm</span></div>
                    </div>
                </div>
                <!-- Weight -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-orange-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">⚖️</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">WEIGHT</div>
                        <div class="fw-bolder text-dark fs-5 mt-1" style="line-height:1;">{{ $profile->weight ? $profile->weight : '--' }} <span class="fw-normal small text-secondary">kg</span></div>
                    </div>
                </div>
                <!-- BMI -->
                <div class="stat-pill rounded-4 px-4 py-3 d-flex align-items-center gap-3">
                    <div class="bg-green-soft rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <span class="fs-4">📊</span>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">BMI INDEX</div>
                        <div class="fw-bolder {{ $bmiColor }} fs-5 mt-1" style="line-height:1;">{{ $bmi ?? '--' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Alerts -->
    @if($nextStart)
        @php
            $daysUntilPeriod = \Carbon\Carbon::now()->diffInDays($nextStart, false);
        @endphp
        @if($daysUntilPeriod >= 0 && $daysUntilPeriod <= 7)
            <div class="alert alert-warning d-flex align-items-center mb-4 rounded-3">
                <span class="me-2 fs-4">📅</span>
                <span>Your next period is expected in {{ $daysUntilPeriod }} day(s)!</span>
            </div>
        @endif
    @endif

    <!-- Simple Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0 border-start border-4 border-her-pink rounded-3 transition-hover bgM">
                <div class="card-body">
                    <div class="text-uppercase text-muted small fw-semibold tracking-wide">Average Cycle Length</div>
                    <h3 class="text-her-pink fw-bold mt-2 mb-0">{{ $avgCycle ?? '--' }} days</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0 border-start border-4 border-her-pink rounded-3 bgM">
                <div class="card-body">
                    <div class="text-uppercase text-muted small fw-semibold tracking-wide">Average Period Length</div>
                    <h3 class="text-her-pink fw-bold mt-2 mb-0">{{ $avgPeriod ?? '--' }} days</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0 border-start border-4 border-her-purple rounded-3 bgM">
                <div class="card-body">
                    <div class="text-uppercase text-muted small fw-semibold tracking-wide">Next Period</div>
                    @if($nextStart)
                        <h4 class="text-her-pink fw-bold mt-2 mb-1">{{ $nextStart->format('M d, Y') }}</h4>
                        <div class="text-her-purple small mb-0">to {{ $nextEnd ? $nextEnd->format('M d, Y') : '--' }}</div>
                    @else
                        <h4 class="text-her-pink fw-bold mt-2 mb-0">--</h4>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0 border-start border-4 border-her-green rounded-3 bgM">
                <div class="card-body">
                    <div class="text-uppercase text-muted small fw-semibold tracking-wide">Flow Prediction</div>
                    <h3 class="text-her-pink fw-bold mt-2 mb-0">{{ $flowPrediction }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Log -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 bgM">
        <div class="card-body p-4">
            <h4 class="text-her-pink fw-bold mb-4">📅 Period Log</h4>
            <div class="history-list pe-2" style="max-height: 300px; overflow-y: auto;">
                @forelse($periods as $period)
                    <div class="d-flex justify-content-between align-items-center p-3 border border-light-subtle rounded-3 mb-3 bg-her-light-pink">
                        <div>
                            <div class="fw-bold text-dark">{{ $period->start_date->format('M d, Y') }} - {{ $period->end_date ? $period->end_date->format('M d, Y') : 'Ongoing' }}</div>
                            <small class="text-muted">{{ $period->start_date->diffInDays($period->end_date ?? $period->start_date) + 1 }} days</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted p-4 rounded-3 bg-her-light-pink">No periods recorded yet. Start tracking today!</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Period Modal -->
<div class="modal fade" id="periodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            @php
                $latestPeriod = $periods->first();
                $hasOpenPeriod = $latestPeriod && !$latestPeriod->end_date;
            @endphp
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-her-pink fw-bold">🩸 Log Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4">
                @if($latestPeriod)
                    <div class="p-3 bg-her-light border border-danger-subtle rounded-3 mb-4">
                        <strong class="d-block text-danger mb-1">Last Log</strong>
                        <span class="text-muted small">
                            {{ $latestPeriod->start_date->format('M d, Y') }} - {{ $latestPeriod->end_date ? $latestPeriod->end_date->format('M d, Y') : 'Ongoing' }}
                        </span>
                    </div>
                @endif

                <form action="{{ $hasOpenPeriod ? route('admin.hercycle.period.update', $latestPeriod->id) : route('admin.hercycle.period.store') }}" method="POST">
                    @csrf
                    @if($hasOpenPeriod)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-lg" required value="{{ $hasOpenPeriod ? $latestPeriod->start_date->format('Y-m-d') : date('Y-m-d') }}" {{ $hasOpenPeriod ? 'readonly' : '' }}>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">End Date {{ $hasOpenPeriod ? '' : '(optional)' }}</label>
                        <input type="date" name="end_date" class="form-control form-control-lg" {{ $hasOpenPeriod ? 'required' : '' }} {{ $hasOpenPeriod ? 'min=' . $latestPeriod->start_date->copy()->addDay()->format('Y-m-d') : '' }}>
                        <div class="form-text mt-2">
                            {{ $hasOpenPeriod ? 'Complete your last ongoing period by adding the end date.' : 'You can add or update end date later.' }}
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2 rounded-3 fw-bold">{{ $hasOpenPeriod ? 'Update End Date' : 'Save Period' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Symptom Modal -->
<div class="modal fade" id="symptomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-her-pink fw-bold">📝 Log Symptoms & Mood</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4">
                <form action="{{ route('admin.hercycle.symptom.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Physical Symptoms</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['cramps', 'bloating', 'headaches', 'breast_tenderness', 'fatigue', 'backache'] as $symptom)
                                <input type="checkbox" class="btn-check" name="physical_symptoms[]" id="phys_{{ $symptom }}" value="{{ $symptom }}">
                                <label class="btn btn-outline-danger shadow-none rounded-pill btn-sm" for="phys_{{ $symptom }}">{{ ucwords(str_replace('_', ' ', $symptom)) }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Emotional State</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['happy', 'sad', 'anxious', 'irritable', 'energetic', 'calm', 'moody'] as $mood)
                                <input type="checkbox" class="btn-check" name="emotional_symptoms[]" id="emo_{{ $mood }}" value="{{ $mood }}">
                                <label class="btn btn-outline-primary shadow-none rounded-pill btn-sm" for="emo_{{ $mood }}">{{ ucfirst($mood) }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sleep Quality: <span id="sleepValue">5</span>/10</label>
                        <input type="range" name="sleep_quality" class="form-range" min="1" max="10" value="5" oninput="document.getElementById('sleepValue').textContent = this.value">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Energy Level: <span id="energyValue">5</span>/10</label>
                        <input type="range" name="energy_level" class="form-range" min="1" max="10" value="5" oninput="document.getElementById('energyValue').textContent = this.value">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Custom Symptoms</label>
                        <input type="text" name="custom_symptoms" class="form-control" placeholder="Add your own symptoms...">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Save Symptoms</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Profile/Settings Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-her-pink fw-bold">⚙️ Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4">
                <form action="{{ route('admin.hercycle.profile.update', $profile->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <h6 class="text-her-pink mb-3 fw-bold">Profile Information</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $profile->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ $profile->dob }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control" value="{{ $profile->weight }}" min="20" max="200" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Height (cm)</label>
                            <input type="number" name="height" class="form-control" value="{{ $profile->height }}" min="100" max="250" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Blood Group</label>
                        <select name="blood_group" class="form-select" required>
                            <option value="">Select</option>
                            <option value="A+" @if($profile->blood_group=="A+") selected @endif>A+</option>
                            <option value="A-" @if($profile->blood_group=="A-") selected @endif>A-</option>
                            <option value="B+" @if($profile->blood_group=="B+") selected @endif>B+</option>
                            <option value="B-" @if($profile->blood_group=="B-") selected @endif>B-</option>
                            <option value="O+" @if($profile->blood_group=="O+") selected @endif>O+</option>
                            <option value="O-" @if($profile->blood_group=="O-") selected @endif>O-</option>
                            <option value="AB+" @if($profile->blood_group=="AB+") selected @endif>AB+</option>
                            <option value="AB-" @if($profile->blood_group=="AB-") selected @endif>AB-</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 rounded-3 py-2 fw-bold">Update Profile</button>
                </form>

                <hr class="my-4">

                <h6 class="text-her-pink mb-3 fw-bold">🔔 Notification Settings</h6>
                <form action="{{ route('admin.hercycle.notifications.update', $profile->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="d-flex justify-content-between align-items-center mb-4 bg-light p-3 rounded-3 border">
                        <span class="fw-semibold text-dark">Period Reminder</span>
                        <div class="form-check form-switch m-0 fs-5">
                            <input class="form-check-input mt-0" type="checkbox" role="switch" name="period_reminder" value="1" {{ $profile->notification->first() && $profile->notification->first()->period_reminder ? 'checked' : '' }}>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold">Save Notifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Day Detail Modal -->
<div class="modal fade" id="dayDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-her-pink fw-bold" id="dayDetailTitle">Day Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4" id="dayDetailContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>

@endsection
