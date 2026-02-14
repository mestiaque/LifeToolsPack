@extends('me::master')

@section('title', trans('Edit Loan'))

@section('content')
<div class="card shadow mb-4 w-100">
    <div class="card-body">
        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
            @csrf
            {{-- @method('PUT') --}}
            @include('em_core::loans._form')
        </form>
    </div>
</div>
@endsection
