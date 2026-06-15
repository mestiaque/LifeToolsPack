@extends('me::master')

@section('title', __('Memorable Days'))

@push('buttons')
  <button id="md-add-btn" class="btn btn-sm btn-encodex-create">
    <i class="fas fa-plus me-1"></i> <span class="hide-mobile">@lang('Add Memory')</span>
  </button>
@endpush

@section('content')

<meta name="storage-url" content="{{ asset('storage') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="md-list-url" content="{{ route('admin.memorable-days.list') }}">
<meta name="md-store-url" content="{{ route('admin.memorable-days.store') }}">
<meta name="md-update-base" content="{{ url('/admin/memorable-days') }}">
<meta name="md-delete-base" content="{{ url('/admin/memorable-days') }}">

{{-- ── TOP CONTROLS ── --}}
<div class="md-controls">
  <div class="md-search-wrap">
    <i class="fas fa-search md-search-icon"></i>
    <input id="md-search" type="text" class="md-search" placeholder="@lang('Search by title, category or tag…')">
  </div>
  <div class="md-filter-chips" id="md-chips">
    <button class="md-chip active" data-cat="">@lang('All')</button>
  </div>
</div>

{{-- ── CARD GRID ── --}}
<div id="md-grid" class="md-grid"></div>

{{-- ── ADD / EDIT MODAL ── --}}
<div class="modal fade" id="md-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content md-modal-content">
      <div class="modal-header md-modal-header">
        <h5 class="modal-title" id="md-modal-title">@lang('New Memory')</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="md-form" enctype="multipart/form-data">
        <input type="hidden" id="md-id">
        <input type="hidden" id="md-remove-image" name="remove_image" value="0">

        <div class="modal-body md-modal-body">
          {{-- Image --}}
          <div class="md-img-drop" id="md-img-drop">
            <div id="md-img-placeholder">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>@lang('Drop image here or click to browse')</span>
            </div>
            <img id="md-img-preview" style="display:none;" alt="preview">
            <button type="button" id="md-img-remove" style="display:none;" title="Remove image">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <input type="file" id="md-img-input" name="image" accept="image/*" style="display:none;">

          {{-- Row 1: Title + Date --}}
          <div class="row g-3 mt-1">
            <div class="col-md-8">
              <label class="md-label">@lang('Title') <span class="text-danger">*</span></label>
              <input type="text" id="md-title" name="title" class="form-control md-input" placeholder="@lang('What happened?')" required>
            </div>
            <div class="col-md-4">
              <label class="md-label">@lang('Date') <span class="text-danger">*</span></label>
              <input type="date" id="md-date" name="event_date" class="form-control md-input" required>
            </div>
          </div>

          {{-- Description --}}
          <div class="mt-3">
            <label class="md-label">@lang('Description')</label>
            <textarea id="md-desc" name="description" class="form-control md-input" rows="3"
              placeholder="@lang('Describe this memory…')"></textarea>
          </div>

          {{-- Row 2: Category + Location --}}
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="md-label">@lang('Category')</label>
              <select id="md-category" name="category" class="form-select md-input">
                <option value="">— @lang('choose') —</option>
                <option value="birthday">🎂 @lang('Birthday')</option>
                <option value="love">💕 @lang('Love')</option>
                <option value="family">👨‍👩‍👧 @lang('Family')</option>
                <option value="friendship">🤝 @lang('Friendship')</option>
                <option value="career">💼 @lang('Career')</option>
                <option value="travel">✈️ @lang('Travel')</option>
                <option value="achievement">🏆 @lang('Achievement')</option>
                <option value="health">💪 @lang('Health')</option>
                <option value="other">⭐ @lang('Other')</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="md-label">@lang('Location')</label>
              <input type="text" id="md-location" name="location" class="form-control md-input"
                placeholder="@lang('Where was it?')">
            </div>
          </div>

          {{-- Row 3: Color + Importance --}}
          <div class="row g-3 mt-1">
            <div class="col-md-4">
              <label class="md-label">@lang('Accent Color')</label>
              <input type="color" id="md-color" name="color" class="md-color-pick" value="#667eea">
            </div>
            <div class="col-md-8">
              <label class="md-label">@lang('Importance')</label>
              <div class="md-star-rating" id="md-importance-stars">
                @for($i = 1; $i <= 5; $i++)
                  <span class="star" data-val="{{ $i }}">★</span>
                @endfor
              </div>
              <input type="hidden" id="md-importance" name="importance_level">
            </div>
          </div>

          {{-- Tags --}}
          <div class="mt-3">
            <label class="md-label">@lang('Tags') <small class="text-muted">(@lang('press Enter to add'))</small></label>
            <div class="md-tag-input-wrap" id="md-tag-wrap">
              <input type="text" id="md-tag-input" placeholder="@lang('add a tag…')">
            </div>
            <input type="hidden" id="md-tags" name="tags">
          </div>

          {{-- Toggles --}}
          <div class="md-toggles mt-3">
            <label class="md-toggle-label">
              <input type="checkbox" id="md-private" name="is_private" class="md-toggle-check">
              <span class="md-toggle-slider"></span>
              <span>@lang('Private')</span>
            </label>
            <label class="md-toggle-label">
              <input type="checkbox" id="md-repeat" name="repeat_yearly" class="md-toggle-check">
              <span class="md-toggle-slider"></span>
              <span>@lang('Repeat yearly')</span>
            </label>
            <label class="md-toggle-label">
              <input type="checkbox" id="md-reminder" name="reminder_enabled" class="md-toggle-check">
              <span class="md-toggle-slider"></span>
              <span>@lang('Reminder')</span>
            </label>
          </div>
          <div id="md-reminder-days-wrap" class="mt-2" style="display:none;">
            <label class="md-label">@lang('Remind me') <span class="text-muted">@lang('days before')</span></label>
            <input type="number" id="md-reminder-days" name="reminder_days_before"
              class="form-control md-input" min="1" max="365" style="max-width:140px;">
          </div>
        </div>

        <div class="modal-footer md-modal-footer">
          <button type="button" class="btn btn-sm btn-encodex-cancel" id="md-delete-btn" style="display:none;">
            <i class="fas fa-trash me-1"></i>@lang('Delete')
          </button>
          <button type="button" class="btn btn-sm btn-encodex-cancel ms-auto" data-bs-dismiss="modal">@lang('Cancel')</button>
          <button type="submit" class="btn btn-sm btn-encodex-save" id="md-save-btn">
            <i class="fas fa-save me-1"></i>@lang('Save Memory')
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('css')
  @include('em_core::memorable-days.css')
@endpush

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @include('em_core::memorable-days.js')
@endpush
