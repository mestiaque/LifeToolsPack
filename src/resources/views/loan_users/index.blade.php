@extends('me::master')

@section('title', __('Loan Users'))

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-left-primary shadow h-100 w-100 py-2">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.loan-users.index') }}" class="mb-3 glass-search-form">
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
                            <a href="{{ route('admin.loan-users.index') }}" class="btn btn-sm btn-encodex-clear rounded">
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
                                <th>@lang('Phone')</th>
                                <th>@lang('Address')</th>
                                <th>@lang('Active')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanUsers as $lu)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('admin.loans.user-history', $lu->id) }}"><i class="fas fa-info-circle"></i></a> {{ $lu->name }} </td>
                                    <td>{{ $lu->email }}</td>
                                    <td>{{ $lu->phone }}</td>
                                    <td>{{ $lu->address }}</td>
                                    <td class="text-center">
                                        @if($lu->is_active)
                                            <span class="badge bg-success">@lang('Active')</span>
                                        @else
                                            <span class="badge bg-secondary">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td >
                                        <div class="d-flex justify-content-center">

                                            <!-- Show Button -->
                                            <button type="button"
                                                class="btn btn-sm btn-encodex-show   me-1 d-none"
                                                data-bs-toggle="modal"
                                                data-bs-target="#showLoanUserModal{{ $lu->id }}"
                                                title="@lang('Show')">
                                                <i class="fas fa-eye  "></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <button
                                                title="@lang('Edit')"
                                                class="btn btn-sm btn-encodex-edit  me-1 edit-loan-user-btn"
                                                data-id="{{ $lu->id }}"
                                                data-name="{{ $lu->name }}"
                                                data-email="{{ $lu->email }}"
                                                data-phone="{{ $lu->phone }}"
                                                data-address="{{ $lu->address }}"
                                                data-is_active="{{ $lu->is_active ? '1' : '0' }}">
                                                <i class="fas fa-edit "></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.loan-users.destroy', $lu->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button title="@lang('Delete')" onclick="return confirm('{{ __('Are you sure you want to delete this?') }}')"
                                                    type="submit" class="btn btn-sm btn-encodex-delete me-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Modal for Show Loan User -->
                                <div class="modal modal-lg fade" id="showLoanUserModal{{ $lu->id }}" tabindex="-1" aria-labelledby="showLoanUserModalLabel{{ $lu->id }}" aria-hidden="true">
                                    <div class=" modal-dialog  glass-card modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="showLoanUserModalLabel{{ $lu->id }}">@lang('Loan User Details')</h5>
                                                <button type="button" class="btn-close btn-encodex-cancel" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>@lang('Name'):</strong> {{ $lu->name }}<br>
                                                <strong>@lang('Email'):</strong> {{ $lu->email }}<br>
                                                <strong>@lang('Phone'):</strong> {{ $lu->phone }}<br>
                                                <strong>@lang('Address'):</strong> {{ $lu->address }}<br>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- If using pagination --}}
                    {{-- {{ $loanUsers->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4 w-100">
            <div class="card-body">
                <form id="loan-user-form" action="{{ isset($loanUser) ? route('admin.loan-users.update', $loanUser->id) : route('admin.loan-users.store') }}" method="POST">
                    @csrf
                    @if(isset($loanUser))
                        @method('PUT')
                    @endif
                    <div id="loan-user-form-fields">
                        @include('em_core::loan_users._form')
                    </div>
                    <input type="hidden" id="loan-user-edit-id" name="edit_id" value="">
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script to handle edit button click --}}
@push('js')
<script>
$(document).ready(function () {

    // Handle Edit Button Click
    $('.edit-loan-user-btn').on('click', function (e) {
        e.preventDefault();

        let id = $(this).data('id');
        let name = $(this).data('name');
        let email = $(this).data('email');
        let phone = $(this).data('phone');
        let address = $(this).data('address');
        let isActive = $(this).data('is_active');

        // Set form for update
        let form = $('#loan-user-form');
        form.attr('action', "{{ route('admin.loan-users.index') }}/" + id);

        // Add method spoofing for PUT
        if (form.find('input[name="_method"]').length === 0) {
            form.append('<input type="hidden" name="_method" value="PUT">');
        } else {
            form.find('input[name="_method"]').val('PUT');
        }

        // Set form fields
        console.log('Setting form fields:', { name, email, phone, address, isActive });
        form.find('input[name="name"]').val(name);
        form.find('input[name="email"]').val(email);
        form.find('input[name="phone"]').val(phone);
        form.find('textarea[name="address"]').val(address);
        form.find('input[name="is_active"]').prop('checked', isActive == 1);
        $('.save-btn').addClass('d-none');
        $('.update-btn').removeClass('d-none');

        // Scroll to the form
        $('html, body').animate({
            scrollTop: form.offset().top
        }, 600);
    });

});
</script>
@endpush
@endsection
