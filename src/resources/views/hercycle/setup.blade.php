@extends('me::master')

@section('title', 'HerCycle - Setup')

@push('css')
<style>
    :root {
        --her-pink-light: #FFE4E6;
        --her-pink: #F472B6;
        --her-pink-dark: #DB2777;
        --her-purple-light: #F3E8FF;
        --her-purple: #A855F7;
        --her-purple-dark: #7C3AED;
    }

    .setup-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
    }

    .setup-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(212, 123, 179, 0.15);
    }

    .setup-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .setup-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .setup-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--her-pink-dark);
        margin-bottom: 10px;
    }

    .setup-subtitle {
        color: #6B7280;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--her-pink);
        box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.1);
    }

    .form-hint {
        font-size: 0.8rem;
        color: #9CA3AF;
        margin-top: 5px;
    }

    .btn-her {
        width: 100%;
        padding: 16px 32px;
        border-radius: 14px;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-her-primary {
        background: linear-gradient(135deg, var(--her-pink) 0%, var(--her-pink-dark) 100%);
        color: white;
    }

    .btn-her-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(212, 123, 179, 0.4);
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    .step {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #E5E7EB;
    }

    .step.active {
        background: var(--her-pink);
    }

    .welcome-animation {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush

@section('content')
<div class="setup-container">
    <div class="setup-card">
        <div class="setup-header">
            <div class="welcome-animation">💝</div>
            <h1 class="setup-title">Welcome to HerCycle!</h1>
            <p class="setup-subtitle">
                Let's get to know you better to provide accurate predictions and personalized insights. 
                This information helps us make your tracking experience perfect for you.
            </p>
        </div>

        <form action="{{ route('admin.hercycle.profile.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-input" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Your Age</label>
                <input type="number" name="age" class="form-input" placeholder="Enter your age" min="10" max="100">
                <p class="form-hint">Optional - helps us provide better recommendations</p>
            </div>

            <div class="form-group">
                <label class="form-label">Average Cycle Length</label>
                <input type="number" name="cycle_length" class="form-input" value="28" min="20" max="45" required>
                <p class="form-hint">Number of days between periods (typically 21-35 days)</p>
            </div>

            <div class="form-group">
                <label class="form-label">Average Period Length</label>
                <input type="number" name="period_length" class="form-input" value="5" min="2" max="10" required>
                <p class="form-hint">How many days does your period usually last?</p>
            </div>

            <button type="submit" class="btn-her btn-her-primary">
                Get Started ✨
            </button>
        </form>
    </div>
</div>
@endsection
