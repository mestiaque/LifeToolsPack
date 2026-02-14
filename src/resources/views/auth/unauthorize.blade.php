@extends('me::master')

@section('title', trans('403'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6">
            <div class="card shadow mb-4 w-100 text-center">
                <div class="card-header py-4 bg-gradient-danger">
                    <h3 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-exclamation-triangle fa-1x mr-2"></i> @lang('Unauthorized Access')
                    </h3>
                </div>
                <div class="card-body">
                    <p class="lead mb-4">
                        @lang("Sorry, you don't have permission to access this page.")
                    </p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-encodex">
                        <i class="fas fa-home me-1"></i> @lang('Back to Dashboard')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

@endpush

@push('css')

@endpush
