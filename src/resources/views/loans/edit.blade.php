@extends('me::master')

@section('title', trans('Edit Loan'))

@push('buttons')
  <a href="{{ route('admin.loans.history', $loan->id) }}"
     class="btn btn-sm btn-encodex-payment">
      <i class="fas fa-edit"></i> @lang('History')
  </a>
@endpush

@section('content')
@php
    $redirectTo = route('admin.loans.index') . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
@endphp
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
            @csrf
            {{-- @method('PUT') --}}
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @include('em_core::loans._form')
        </form>
    </div>
</div>
@include('me::components.calculator')
@endsection
