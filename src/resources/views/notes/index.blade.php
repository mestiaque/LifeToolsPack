<!-- Color Picker Modal -->
<div id="color-modal" class="modal fade" tabindex="-1" aria-labelledby="color-modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass">
            <div class="modal-header">
                <h5 class="modal-title" id="color-modal-title">Choose Note Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="color-modal-swatches" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@extends('me::master')

@section('title', trans('My Notes'))

@push('buttons')
    <button id="btn-add-note" class="fabX btn btn-sm btn-encodex-create" title="Add Note"><i class="fas fa-plus"></i> New</button>
  {{-- <a href="{{ route('admin.notes.readAll') }}" class="btn btn-sm btn-encodex-print">@lang('Mark All Read')</a> --}}
@endpush

@section('content')

<div class="containerX">
    <!-- Header -->
    <div class="controls">
        <input type="text" id="search-input" placeholder="Search notes..." />
        <select id="color-filter">
            <option value="">All Colors</option>
        </select>
    </div>
    <!-- Notes grid -->
    <div id="notes-grid" class="notes-grid"></div>
    <!-- Floating Add Button -->

</div>

<!-- Modal for Add/Edit (Bootstrap) -->
<div class="modal fade" id="note-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog glass-card">
        <div class="modal-content ">
            <form id="note-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">New Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="note-id"/>
                    <div style="">
                        <div class="">
                            <input type="text" id="note-title" name="title" class="form-control input-custom" placeholder="Title" maxlength="255" required />
                        </div>
                        <div class="">
                            <textarea id="note-desc" name="description" class="form-control" placeholder="Description" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-encodex-cancel" id="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-encodex-save" id="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 will be used for delete confirmation -->

@endsection
@push('css')
    @include('em_core::notes.css')
@endpush
@push('js')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('em_core::notes.js')
@endpush
