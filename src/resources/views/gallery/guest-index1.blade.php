@extends('components.lte.guest')

@section('title', trans('Gallery'))

@push('buttons')
  @component('me::components.btn.basic-button', [
      'route' => route('admin.messages.readAll'),
      'text' => __('Upload Images'),
      'class' => 'btn-encodex-create',
      'id' => 'openUploadModal',
      'icon' => 'upload'
  ])
  @endcomponent
@endpush

@section('content')
<div class="containerx">
    <div id="imagePreviewContainer" class="row g-3">
        @foreach($images as $img)
        <div class="col-md-3 col-lg-2 col-sm-6 text-center gallery-thumb mb-4" data-id="{{ $img->id }}">
            <div class="card shadow-sm h-100">
                <img loading="lazy" src="{{ route('gallery.preview', $img->id) }}" alt="Preview" class="img-fluid rounded border mt-2" style="max-height: 320px; cursor:pointer;">
                <div class="card-body p-2">
                    <div class="small text-muted text-truncate" title="{{ $img->original_name ?? '' }}">
                        {{ $img->original_name ?? '' }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal for image view and navigation -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class=" modal-dialog  glass-card modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body text-center position-relative">
        <button type="button" class="btn btn-outline-primary btn-lg rounded-circle shadow position-absolute top-50 start-0 translate-middle-y" id="prevBtn" style="width:48px;height:48px;">
          <i class="fas fa-chevron-left"></i>
        </button>
        <img loading="lazy" id="modalImage" src="" class="img-fluid" style="max-height:500px;">
        <button type="button" class="btn btn-outline-primary btn-lg rounded-circle shadow position-absolute top-50 end-0 translate-middle-y" id="nextBtn" style="width:48px;height:48px;">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <div class="modal-footer justify-content-between">
          <span id="modalImageTitle"></span>
          <button type="button" class="btn btn-encodex-clear btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> @lang("Close")
          </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for image upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class=" modal-dialog  glass-card modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang("Upload Images")</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-encodex-print btn-rounded position-relative">
                <label class="form-label text-dark mb-0 px-3" for="imageInput" style="cursor: pointer;">@lang("Choose Images (Max 15)")</label>
                <input type="file" id="imageInput" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" multiple accept="image/*">
            </button>
        </div>
        <div id="modalImagePreviewContainer" class="row g-3"></div>
        <div class="progress mt-3" style="height: 25px; display: none;" id="progressWrapper">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
            </div>
        </div>
        <div id="statusMessage" class="text-center mt-3"></div>
      </div>
      <div class="modal-footer justify-content-center">
        <button id="uploadButton" class="btn btn-encodex" disabled>@lang("Upload Images")</button>
      </div>
    </div>
  </div>
</div>

@push('js')
<script>
const MAX_IMAGE_SIZE_MB = 20;
const MAX_IMAGES = 15;
let selectedFiles = [];
let images = @json($images->pluck('img', 'id'));
let imageNames = @json($images->pluck('original_name', 'id'));
let imageIds = Object.keys(images).reverse(); // reverse for descending order
let currentIndex = 0;

// Open upload modal
$('#openUploadModal').on('click', function() {
    $('#uploadModal').modal('show');
});

// Image preview and validation in modal
$('#imageInput').on('change', function (event) {
    const files = event.target.files;
    selectedFiles = [];
    $('#modalImagePreviewContainer').empty();
    let errorMessage = '';
    if (files.length > MAX_IMAGES) {
        alert(`You can select up to ${MAX_IMAGES} images.`);
        return;
    }
    $.each(files, function (index, file) {
        if (file.size > MAX_IMAGE_SIZE_MB * 1024 * 1024) {
            errorMessage = `Image ${file.name} exceeds the size limit of ${MAX_IMAGE_SIZE_MB} MB.`;
            return false;
        }
        selectedFiles.push(file);
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#modalImagePreviewContainer').append(`
                <div class="col-2 text-center">
                    <div class="card shadow-sm h-100">
                        <img loading="lazy" src="${e.target.result}" alt="Preview" class="img-fluid rounded border mt-2" style="max-height: 120px;">
                        <div class="card-body p-2">
                            <div class="small text-muted text-truncate" title="${file.name}">
                                ${file.name}
                            </div>
                        </div>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    });
    if (errorMessage) {
        alert(errorMessage);
        return;
    }
    $('#uploadButton').prop('disabled', selectedFiles.length === 0);
});

// Image upload with progress
$('#uploadButton').on('click', function () {
    if (selectedFiles.length === 0) {
        alert('No images selected.');
        return;
    }
    const formData = new FormData();
    selectedFiles.forEach((file) => {
        formData.append('images[]', file);
    });
    formData.append('is_public', $('#isPublicCheckbox').is(':checked') ? 1 : 0);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("gallery.upload") }}', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            $('#progressWrapper').show();
            $('#progressBar').css('width', percentComplete + '%').text(percentComplete + '%');
        }
    });
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                $('#progressBar').removeClass('bg-danger').addClass('bg-success').css('width', '100%').text('Upload Successful!');
                $('#statusMessage').removeClass('text-danger').addClass('text-success').text('Images uploaded successfully.');
                setTimeout(function() {
                    location.reload();
                }, 1200);
            } else {
                $('#progressBar').removeClass('bg-success').addClass('bg-danger').css('width', '100%').text('Upload Failed!');
                $('#statusMessage').removeClass('text-success').addClass('text-danger').text(response.message || 'An error occurred.');
            }
        } else {
            $('#progressBar').removeClass('bg-success').addClass('bg-danger').css('width', '100%').text('Upload Failed!');
            $('#statusMessage').removeClass('text-success').addClass('text-danger').text('Image upload failed. Please try again.');
        }
        setTimeout(function() {
            $('#progressBar').removeClass('bg-success bg-danger').css('width', '0%').text('0%');
            $('#statusMessage').text('');
            $('#progressWrapper').hide();
            $('#uploadButton').prop('disabled', true);
        }, 3000);
    };
    xhr.send(formData);
});

// Modal navigation logic
$('.gallery-thumb').on('click', function() {
    let id = $(this).data('id').toString();
    currentIndex = imageIds.indexOf(id);
    showModalImage(currentIndex);
    $('#imageModal').modal('show');
});

function showModalImage(idx) {
    let id = imageIds[idx];
    $('#modalImage').attr('src', '/gallery/preview/' + id);
    $('#modalImageTitle').html('<span class="fw-bold">' + (imageNames[id] ?? '') + '</span>');
}

$('#prevBtn').on('click', function() {
    if (currentIndex > 0) {
        currentIndex--;
        showModalImage(currentIndex);
    }
});
$('#nextBtn').on('click', function() {
    if (currentIndex < imageIds.length - 1) {
        currentIndex++;
        showModalImage(currentIndex);
    }
});

// Accessibility fix for modal focus
$('#uploadModal').on('show.bs.modal', function () {
    $(this).removeAttr('inert');
});
$('#uploadModal').on('hidden.bs.modal', function () {
    $(this).attr('inert', '');
});
</script>
<script>
  // Handle focus management for modals
  document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');

    modals.forEach(modal => {
      // Set focus to the body when modal is about to be hidden
      modal.addEventListener('hide.bs.modal', function() {
        // Move focus to a safe element outside the modal
        document.body.focus();
      });

      // Ensure close buttons don't retain focus
      const closeButtons = modal.querySelectorAll('.btn-close, .btn-encodex-clear');
      closeButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Immediately blur the button to prevent focus issues
          this.blur();
        });
      });
    });
  });
</script>
@endpush
@endsection
