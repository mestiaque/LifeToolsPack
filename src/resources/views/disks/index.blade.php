@extends('me::master')

@section('title', trans('Disks'))

@push('buttons')
    {{-- Create Button --}}
    <button class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#createDiskModal">
        <i class="fas fa-plus"></i> {{ __('Add Disk') }}
    </button>
@endpush

@section('content')
<div class="container-fluids">
    <div class="card shadow mb-4 w-100">

        <div class="card-body">
            <form method="GET" action="{{ route('admin.disks.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md">
                        <input type="text" name="code" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="@lang('Enter Code')" value="{{ request('code') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" name="tag" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="@lang('Enter Tag')" value="{{ request('tag') }}">
                    </div>
                    <div class="col-md">
                        <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                            <i class="fas fa-search"></i> @lang('Search')
                        </button>
                        <a href="{{ route('admin.disks.index') }}" class="btn btn-sm btn-encodex-clear rounded">
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
                        <th>@lang('Code')</th>
                        <th>@lang('Tag')</th>
                        <th>@lang('Capacity') @lang('(GB)')</th>
                        <th>@lang('Used') @lang('(GB)')</th>
                        <th>@lang('Content')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($disks as $disk)
                        <tr>
                            <td>{{ toBanglaNumber($loop->iteration) }}</td>
                            <td>{{ $disk->code }}</td>
                            <td>
                                @if($disk->tag)
                                    @foreach(explode(',', $disk->tag) as $singleTag)
                                        <span class="badge bg-light text-dark border">{{ trim($singleTag) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $disk->capacity }}</td>
                            <td>{{ $disk->used }}</td>
                            <td>{{ $disk->content }}</td>
                            <td>
                                @if($disk->status == 'active')
                                    <span class="badge bg-success">@lang('Active')</span>
                                @else
                                    <span class="badge bg-danger">@lang('Inactive')</span>
                                @endif
                            </td>



                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">

                                    {{-- Show Modal Trigger --}}
                                    <button class="btn btn-sm btn-encodex-show"
                                            data-bs-toggle="modal"
                                            data-bs-target="#showDiskModal{{ $disk->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Edit Modal Trigger --}}
                                    <button class="btn btn-sm btn-encodex-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editDiskModal{{ $disk->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.disks.destroy', $disk->id) }}" method="POST" class="d-inline m-0">
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
                            <td colspan="8" class="text-center">@lang('No disks found')</td>
                        </tr>
                    @endforelse
                    <tr >
                        <td colspan="8" style="background: rgba(255, 255, 0, 0.267) !important">
                            @php
                                $totalCapacity = $disks->sum('capacity');
                                $totalUsed = $disks->sum('used');
                                $freeSpace = $totalCapacity - $totalUsed;
                            @endphp
                            <span class="badge bg-success">
                                {{ toBanglaNumber($totalCapacity) }}
                            </span>
                            <span class="badge bg-danger mx-4">
                                {{ toBanglaNumber($totalUsed) }}
                            </span>
                            <span class="badge bg-info">
                                {{ toBanglaNumber($freeSpace) }}
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
{{-- --------------------------------------------------------- --}}
{{-- CREATE MODAL --}}
{{-- --------------------------------------------------------- --}}
<div class="modal fade" id="createDiskModal" tabindex="-1">
    <div class=" modal-dialog  glass-card  modal-lg">
        <form action="{{ route('admin.disks.store') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Disk') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Code') }}</label>
                        <input type="text" name="code" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Tag') }}</label>
                        <input type="text" name="tag" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Capacity') }}</label>
                        <input type="number" step="0.01" name="capacity" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Used') }}</label>
                        <input type="number" step="0.01" name="used" class="form-control form-control-sm">
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control form-control-sm"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Content') }}</label>
                        <textarea name="content" class="form-control form-control-sm"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="active">@lang('Active')</option>
                            <option value="inactive">@lang('Inactive')</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-encodex-cancel"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-encodex-save">{{ __('Save Disk') }}</button>
            </div>

        </form>
    </div>
</div>

@foreach($disks as $disk)
    {{-- --------------------------------------------------------- --}}
    {{-- EDIT MODAL --}}
    {{-- --------------------------------------------------------- --}}
    <div class="modal fade" id="editDiskModal{{ $disk->id }}" tabindex="-1">
        <div class=" modal-dialog  glass-card modal-lg">
            <form action="{{ route('admin.disks.update', $disk->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Disk') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control form-control-sm" value="{{ $disk->code }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Tag') }}</label>
                            <input type="text" name="tag" class="form-control form-control-sm" value="{{ $disk->tag }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Capacity') }}</label>
                            <input type="number" step="0.01" name="capacity" class="form-control form-control-sm" value="{{ $disk->capacity }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Used') }}</label>
                            <input type="number" step="0.01" name="used" class="form-control form-control-sm" value="{{ $disk->used }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control form-control-sm">{{ $disk->description }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Content') }}</label>
                            <textarea name="content" class="form-control form-control-sm">{{ $disk->content }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="active" {{ $disk->status == 'active' ? 'selected' : '' }}>@lang('Active')</option>
                                <option value="inactive" {{ $disk->status == 'inactive' ? 'selected' : '' }}>@lang('Inactive')</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-encodex-cancel"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-encodex-save">{{ __('Update Disk') }}</button>
                </div>

            </form>
        </div>
    </div>

    {{-- --------------------------------------------------------- --}}
    {{-- SHOW MODAL --}}
    {{-- --------------------------------------------------------- --}}
    <div class="modal fade" id="showDiskModal{{ $disk->id }}" tabindex="-1">
        <div class=" modal-dialog  glass-card modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Disk Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Code') }}:</label>
                            <div>{{ $disk->code }}</div>
                        </div>

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Tag') }}:</label>
                            <div>{{ $disk->tag }}</div>
                        </div>

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Capacity') }}:</label>
                            <div>{{ $disk->capacity }}</div>
                        </div>

                        <div class="col-6">
                            <label class="fw-bold">{{ __('Used') }}:</label>
                            <div>{{ $disk->used }}</div>
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">{{ __('Description') }}:</label>
                            <div>{{ $disk->description }}</div>
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">{{ __('Content') }}:</label>
                            <div>{{ $disk->content }}</div>
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">{{ __('Status') }}:</label>
                            <div>{{ ucfirst($disk->status) }}</div>
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



