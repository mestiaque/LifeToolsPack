@extends('me::master')

@section('title', trans('Add Loan'))

@section('content')
@php
    $redirectTo = route('admin.loans.index') . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
@endphp
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('admin.loans.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @include('em_core::loans._form')
        </form>
    </div>
</div>
@include('me::components.calculator')
@endsection
