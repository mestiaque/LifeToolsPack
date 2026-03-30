@extends('me::master')

@section('title', __('Events'))

@push('buttons')
  <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#eventModal">@lang('Add Event')</button>
@endpush

@section('content')
  <div class="row">
    <div class="col-lg-12 mb-4">
      <div class="card border-left-primary shadow h-100 w-100 py-2">
        <div class="card-body">
          <form method="GET" action="{{ route('admin.events.index') }}" class="mb-3">
            <div class="row">
              <div class="col-md">
                <input type="text" name="title" class="form-control form-control-sm" placeholder="@lang('Enter Title')" value="{{ request('title') }}">
              </div>
              <div class="col-md">
                <input type="date" name="start" class="form-control form-control-sm" placeholder="@lang('Start Date')" value="{{ request('start') }}">
              </div>
              <div class="col-md">
                <input type="date" name="end" class="form-control form-control-sm" placeholder="@lang('End Date')" value="{{ request('end') }}">
              </div>
              <div class="col-md">
                <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                  <i class="fas fa-search"></i> @lang('Search')
                </button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                  <i class="fas fa-eraser"></i> @lang('Reset')
                </a>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-striped table-encodex">
              <thead class="text-center">
                <tr class="text-center">
                  <th>{{ trans('#') }}</th>
                  <th>@lang('Title')</th>
                  <th>@lang('Start')</th>
                  <th>@lang('End')</th>
                  <th>@lang('All Day')</th>
                  <th>@lang('Action')</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($events as $event)
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $event->title }}</td>
                    <td class="text-center">{{ formatDateTime($event->start) }}</td>
                    <td class="text-center">{{ formatDateTime($event->end) }}</td>
                    <td class="text-center">
                      @if($event->all_day)
                        <span class="badge bg-success">@lang('Yes')</span>
                      @else
                        <span class="badge bg-secondary">@lang('No')</span>
                      @endif
                    </td>
                    <td class="d-flex justify-content-center">
                      <a href="{{ route('admin.events.expenses.index', $event->id) }}" class="btn btn-sm btn-encodex-payment me-1" title="@lang('Expenses')">
                        <i class="fas fa-money-bill-wave"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-encodex-edit   me-1" data-bs-toggle="modal" data-bs-target="#eventModal{{ $event->id }}" title="@lang('Edit')">
                        <i class="fas fa-edit  "></i>
                      </button>
                      <form action="{{ route('admin.events.delete', $event->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')" type="submit" class="btn btn-sm btn-encodex-delete   me-1">
                          <i class="fas fa-trash  "></i>
                        </button>
                      </form>
                    </td>
                    
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= Modals ================= -->
  <!-- Create Modal -->
  <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.events.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="eventModalLabel">@lang('Add Event')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="title" class="form-label">@lang('Title')</label>
              <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
              <label for="start" class="form-label">@lang('Start')</label>
              <input type="datetime-local" class="form-control" name="start" required>
            </div>
            <div class="mb-3">
              <label for="end" class="form-label">@lang('End')</label>
              <input type="datetime-local" class="form-control" name="end">
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="all_day" value="1" id="all_day">
              <label class="form-check-label" for="all_day">@lang('All Day')</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
            <button type="submit" class="btn btn-encodex-create btn-sm">@lang('Save')</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Edit Modals -->
  @foreach ($events as $event)
    <div class="modal fade" id="eventModal{{ $event->id }}" tabindex="-1" aria-labelledby="eventModalLabel{{ $event->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.events.update', $event->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title" id="eventModalLabel{{ $event->id }}">@lang('Edit Event')</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="title" class="form-label">@lang('Title')</label>
                <input type="text" class="form-control" name="title" value="{{ $event->title }}" required>
              </div>
              <div class="mb-3">
                <label for="start" class="form-label">@lang('Start')</label>
                <input type="datetime-local" class="form-control" name="start" value="{{ date('Y-m-d\TH:i', strtotime($event->start)) }}" required>
              </div>
              <div class="mb-3">
                <label for="end" class="form-label">@lang('End')</label>
                <input type="datetime-local" class="form-control" name="end" value="{{ $event->end ? date('Y-m-d\TH:i', strtotime($event->end)) : '' }}">
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="all_day" value="1" id="all_day{{ $event->id }}" {{ $event->all_day ? 'checked' : '' }}>
                <label class="form-check-label" for="all_day{{ $event->id }}">@lang('All Day')</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-encodex-create btn-sm">@lang('Update')</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
@endsection

@push('css')
  <style>
    .bg-success-light {
      background: rgba(76, 175, 80, 0.1) !important;
    }
    .bg-danger-light {
      background: rgba(244, 67, 54, 0.1) !important;
    }
  </style>
@endpush
