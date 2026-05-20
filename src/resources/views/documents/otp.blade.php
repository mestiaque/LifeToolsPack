@extends('me::guestMaster')

@section('title', 'Enter OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0 fw-semibold text-primary">Enter OTP to Access Files</h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-2 shadow-sm">
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('drive.shared.verify', $doc_id) }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label for="otp" class="form-label fw-semibold">OTP</label>
                            <input type="text" name="otp" id="otp" class="form-control rounded-2" required maxlength="6" autofocus style="background: #ffffff61">
                        </div>
                        <button type="submit" class="btn btn-glass w-100 rounded-2 fw-semibold" style="background: #0019ff1c; color: #1200ffc7;">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
