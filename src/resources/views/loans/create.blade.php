@extends('me::master')

@section('title', trans('Add Loan'))

@section('content')
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('admin.loans.store') }}" method="POST">
            @csrf
            @include('em_core::loans._form')
        </form>
    </div>
</div>
@include('me::components.calculator')
@endsection
