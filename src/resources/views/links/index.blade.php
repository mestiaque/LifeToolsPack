@extends('me::master')

@section('title', trans('Links'))

@push('buttons')
    {{-- Create Button --}}
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createLinkModal">
        <i class="fas fa-plus"></i> {{ __('Add Link') }}
    </button>
@endpush

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">

        <div class="card-body">
            <form method="GET" action="{{ route('admin.links.index') }}" class="mb-3 glass-search-form">
                <div class="row">
                    <div class="col-md">
                        <input type="text" name="title" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="@lang('Enter Title')" value="{{ request('title') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" name="link" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="@lang('Enter Link')" value="{{ request('link') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" name="description" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="@lang('Enter Description')" value="{{ request('description') }}">
                    </div>
                    <div class="col-md">
                        <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                            <i class="fas fa-search"></i> @lang('Search')
                        </button>
                        <a href="{{ route('admin.links.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                            <i class="fas fa-eraser"></i> @lang('Reset')
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped table-encodex table-sm text-center">
                    <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>@lang('Title')</th>
                        <th>@lang('Link')</th>
                        <th>@lang('Description')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td>{{ $link->title }}</td>
                            <td>{{ $link->link }} @if($link->link) &nbsp;&nbsp;<a href="{{ $link->link }}" target="_blank"><i class="fas fa-external-link-alt"></i></a> @endif</td>
                            <td>{{ $link->description }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">

                                    {{-- Show Modal Trigger --}}
                                    <button class="btn btn-sm btn-encodex-show"
                                            data-bs-toggle="modal"
                                            data-bs-target="#showLinkModal{{ $link->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Edit Modal Trigger --}}
                                    <button class="btn btn-sm btn-encodex-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editLinkModal{{ $link->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.links.destroy', $link->id) }}" method="POST" class="d-inline m-0">
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
                            <td colspan="8" class="text-center">@lang('No links found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
{{-- --------------------------------------------------------- --}}
{{-- CREATE MODAL --}}
{{-- --------------------------------------------------------- --}}
<div class="modal fade" id="createLinkModal" tabindex="-1">
    <div class=" modal-dialog  glass-card  modal-lg">
        <form action="{{ route('admin.links.store') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Link') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Link') }}</label>
                        <input type="text" name="link" class="form-control form-control-sm">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control form-control-sm"></textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Save Link') }}</button>
            </div>

        </form>
    </div>
</div>

@foreach($links as $link)
    {{-- --------------------------------------------------------- --}}
    {{-- EDIT MODAL --}}
    {{-- --------------------------------------------------------- --}}
    <div class="modal fade" id="editLinkModal{{ $link->id }}" tabindex="-1">
        <div class=" modal-dialog  glass-card modal-lg">
            <form action="{{ route('admin.links.update', $link->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Link') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="{{ $link->title }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Link') }}</label>
                            <input type="text" name="link" class="form-control form-control-sm" value="{{ $link->link }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control form-control-sm">{{ $link->description }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-encodex-cancel"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-encodex-save">{{ __('Update Link') }}</button>
                </div>

            </form>
        </div>
    </div>

    {{-- --------------------------------------------------------- --}}
    {{-- SHOW MODAL --}}
    {{-- --------------------------------------------------------- --}}
    <div class="modal fade" id="showLinkModal{{ $link->id }}" tabindex="-1">
        <div class=" modal-dialog  glass-card modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Link Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Title') }}:</label>
                            <div>{{ $link->title }}</div>
                        </div>

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Link') }}:</label>
                            <div>{{ $link->link }}</div>
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">{{ __('Description') }}:</label>
                            <div>{{ $link->description }}</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endforeach


@push('css')
<style>
    .disabled-link {
        pointer-events: none;
        color: gray;
        text-decoration: none;
    }
</style>
@endpush
@endsection



