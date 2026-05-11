@extends('me::master')

@section('title', trans('Notify People'))

@push('buttons')
<button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createNotifyPersonModal">
    <i class="fas fa-plus"></i> {{ __('Add Person') }}
</button>
<a href="{{ route('notify-people.trigger') }}" class="btn btn-sm btn-encodex-trigger">
    <i class="fas fa-bell"></i> {{ __('Trigger Notification') }}
</a>
@endpush

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.notify-people.index') }}" class="mb-3 glass-search-form">
                <div class="row">
                    <div class="col-md">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('Enter Name')" value="{{ request('name') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="@lang('Enter Email')" value="{{ request('email') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="@lang('Enter Phone')" value="{{ request('phone') }}">
                    </div>
                    <div class="col-md">
                        <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                            <i class="fas fa-search"></i> @lang('Search')
                        </button>
                        <a href="{{ route('admin.notify-people.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                            <i class="fas fa-eraser"></i> @lang('Reset')
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-encodex text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('Name')</th>
                        <th>@lang('Email')</th>
                        <th>@lang('Phone')</th>
                        <th>@lang('Types')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($people as $person)
                        <tr>
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td>{{ $person->name }}</td>
                            <td>{{ $person->email }}</td>
                            <td>{{ $person->phone }}</td>
                            <td>{{ implode(', ', $person->types ?? []) }}</td>
                            <td>
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button class="btn btn-sm btn-encodex-edit" data-bs-toggle="modal" data-bs-target="#editNotifyPersonModal{{ $person->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.notify-people.destroy', $person->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-encodex-delete"
                                                onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">@lang('No person found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createNotifyPersonModal" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('admin.notify-people.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Person') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('em_core::notify_people.partials.form', ['person' => null])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

@foreach($people as $person)
<div class="modal fade" id="editNotifyPersonModal{{ $person->id }}" tabindex="-1">
    <div class="modal-dialog glass-card modal-lg">
        <form action="{{ route('admin.notify-people.update', $person->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Person') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('em_core::notify_people.partials.form', ['person' => $person])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
