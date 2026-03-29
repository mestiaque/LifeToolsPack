@extends('me::master')

@section('title', 'HerCycle - Setup')

@push('css')
<style>
    .text-her-pink { color: #DB2777; }
    .bg-gradient-her {
        background: linear-gradient(135deg, #F472B6 0%, #DB2777 100%);
    }
    .welcome-animation {
        animation: float 3s ease-in-out infinite;
        font-size: 4rem;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .form-control:focus, .form-select:focus {
        border-color: #F472B6;
        box-shadow: 0 0 0 0.25rem rgba(244, 114, 182, 0.25);
    }
    .transition-hover {
        transition: all 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(219, 39, 119, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                <div class="text-center">
                    <h1 class="fw-bold text-her-pink ">Welcome to HerCycle!</h1>

                </div>

                <form action="{{ route('admin.hercycle.profile.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Your Name</label>
                        <input type="text" name="name" class="form-control form-control- rounded-3" placeholder="Enter your name" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Date of Birth</label>
                        <input type="date" name="dob" class="form-control form-control- rounded-3" required onchange="updateAge()">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <label class="form-label fw-semibold text-dark">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control form-control- rounded-3" placeholder="Enter your weight" min="20" max="200" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Height (cm)</label>
                            <input type="number" name="height" class="form-control form-control- rounded-3" placeholder="Enter your height" min="100" max="250" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Blood Group</label>
                        <select name="blood_group" class="form-control form-control- rounded-3" required>
                            <option value="">Select</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-encodex-save w-100">
                        Get Started ✨
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
