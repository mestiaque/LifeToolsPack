@extends('me::master')

@section('title', trans('Messages'))

@push('buttons')

  <a href="{{ route('admin.messages.readAll') }}" class="btn btn-sm btn-encodex-print">@lang('Mark All Read')</a>
@endpush

@section('content')
  <div class="card border-left-primary shadow h-100 w-100 py-2">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.messages.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('Enter Name')" value="{{ request('name') }}">
                </div>
                <div class="col-md">
                    <input type="text" name="email" class="form-control form-control-sm" placeholder="@lang('Enter Email')" value="{{ request('email') }}">
                </div>
                <div class="col-md">
                    <input type="text" name="subject" class="form-control form-control-sm" placeholder="@lang('Enter Subject')" value="{{ request('subject') }}">
                </div>
                <div class="col-md">
                    <button type="submit" class="btn btn-sm btn-encodex-search rounded">
                        <i class="fas fa-search"></i> @lang('Search')
                    </button>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-encodex-clear rounded">
                        <i class="fas fa-eraser"></i> @lang('Reset')
                    </a>
                </div>
            </div>
        </form>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover table-striped table-encodex">
          <thead class="text-center">
            <tr>
              <th>{{ trans('#') }}</th>
              <th>@lang('Name')</th>
              <th>@lang('Email')</th>
              <th>@lang('Subject')</th>
              <th>@lang('Message')</th>
              <th>@lang('Read')</th>
              <th>@lang('Action')</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($messages as $message)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $message->name }}</td>
                <td>{{ $message->email }}</td>
                <td>{{ $message->subject }}</td>
                <td>{{ Str::limit($message->message, 70) }}</td>
                <td class="text-center">
                  @if($message->is_read)
                    <span class="badge bg-success">@lang('Read')</span>
                  @else
                    <span class="badge bg-warning">@lang('Unread')</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center">
                    <!-- Show Button -->
                    <button type="button"
                            class="btn btn-sm btn-encodex-show   me-1"
                            data-bs-toggle="modal"
                            data-bs-target="#showMessageModal{{ $message->id }}"
                            title="@lang('Show')">
                        <i class="fas fa-eye  "></i>
                    </button>
                    <!-- Edit Button -->
                    {{-- <a title="@lang('Edit')" class="btn btn-sm btn-encodex-edit   me-1"
                        href="{{ route('admin.messages.edit', $message->id) }}">
                        <i class="fas fa-edit  "></i>
                    </a> --}}
                    <!-- Delete Button -->
                    <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                          <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')"
                              type="submit" class="btn btn-sm btn-encodex-delete   me-1">
                              <i class="fas fa-trash  "></i>
                          </button>
                    </form>
                    <!-- Mark as Read Button -->
                    @if(!$message->is_read)
                    <form action="{{ route('admin.messages.read', $message->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <button title="@lang('Mark as Read')" type="submit" class="btn btn-sm btn-encodex-active ">
                          <i class="fas fa-check  "></i>
                      </button>
                    </form>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="">
            {{ $messages->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 All Modals (outside table) -->
  @foreach ($messages as $message)
    <div class="modal fade" id="showMessageModal{{ $message->id }}" tabindex="-1" aria-labelledby="showMessageModalLabel{{ $message->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 glass-card">

            <!-- Header -->
            <div class="modal-header bg-light border-0 rounded-top-4">
                <h5 class="modal-title fw-semibold" id="showMessageModalLabel{{ $message->id }}">
                <i class="bi bi-envelope-open me-2"></i> @lang('Message Details')
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3 bg-light">

                <div class="mb-3">
                <span class="text-muted small">@lang('Name')</span>
                <div class="fw-medium">{{ $message->name }}</div>
                </div>

                <div class="mb-3">
                <span class="text-muted small">@lang('Email')</span>
                <div class="fw-medium">{{ $message->email }}</div>
                </div>

                <div class="mb-3">
                <span class="text-muted small">@lang('Subject')</span>
                <div class="fw-medium">{{ $message->subject }}</div>
                </div>

                <hr>

                <div class="mb-2">
                <span class="text-muted small">@lang('Message')</span>

                <div class="p-3 mt-2 bg-light rounded-3 border">
                    {{ $message->message }}
                </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-light rounded-bottom-4">

                <button type="button"
                        class="btn btn-encodex-cancel btn-sm px-4"
                        data-bs-dismiss="modal">
                @lang('Close')
                </button>

            </div>

            </div>
        </div>
    </div>

  @endforeach
@endsection

@push('scripts')
<script>
  // Handle focus management for modals
  document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');

    modals.forEach(modal => {
      modal.addEventListener('hide.bs.modal', function() {
        document.body.focus();
      });

      const closeButtons = modal.querySelectorAll('.btn-close, .btn-encodex-clear');
      closeButtons.forEach(button => {
        button.addEventListener('click', function() {
          this.blur();
        });
      });
    });
  });
</script>
@endpush
