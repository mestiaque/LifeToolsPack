@extends('me::guest')

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
<div class="container-fluid">
    <div id="imagePreviewContainer" class="row g-3">
        @foreach($images as $img)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 gallery-thumb mb-3" data-id="{{ $img->id }}">
            <div class="card h-100 shadow-sm border-0 rounded overflow-hidden">
                <div class="ratio ratio-1x1">
                    <img loading="lazy" src="{{ route('gallery.preview', $img->id) }}"
                         alt="{{ $img->original_name }}" class="card-img-top object-fit-cover" style="cursor:pointer;">
                </div>
                <div class="card-body p-2 text-center">
                    <p class="small text-truncate mb-0" title="{{ $img->original_name ?? '' }}">
                        {{ $img->original_name ?? '' }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal for image view -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class=" modal-dialog  glass-card modal-dialog-centered modal-lg">
    <div class="modal-content border-0">
      <div class="modal-body text-center position-relative p-0">
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
  <div class=" modal-dialog   modal-dialog-centered modal-lg">
    <div class="modal-content glass-card">
      <div class="modal-header">
        <h5 class="modal-title">@lang("Upload Images")</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-encodex-print btn-rounded position-relative">
                <label class="form-label text-dark mb-0 px-3" for="imageInput" style="cursor: pointer;">@lang("Choose Images (Max 10)")</label>
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/heic2any/dist/heic2any.min.js"></script>
<script>
const MAX_IMAGE_SIZE_MB = 20;
const MAX_IMAGES = 10;
let selectedFiles = [];
let images = @json($images->pluck('img','id'));
let imageNames = @json($images->pluck('original_name','id'));
let imageIds = Object.keys(images).reverse();
let currentIndex = 0;

// Open upload modal
$('#openUploadModal').on('click', function() {
    $('#uploadModal').modal('show');
});

// Image preview in upload modal
// $('#imageInput').on('change', function(e){
//     const files = e.target.files;
//     selectedFiles = [];
//     $('#modalImagePreviewContainer').empty();
//     if(files.length > MAX_IMAGES){
//         alert(`You can select up to ${MAX_IMAGES} images.`);
//         return;
//     }
//     $.each(files, function(i, file){
//         if(file.size > MAX_IMAGE_SIZE_MB*1024*1024){
//             alert(`Image ${file.name} exceeds the size limit of ${MAX_IMAGE_SIZE_MB}MB.`);
//             return false;
//         }
//         selectedFiles.push(file);
//         const reader = new FileReader();
//         reader.onload = function(e){
//             $('#modalImagePreviewContainer').append(`
//                 <div class="col-6 col-sm-4 col-md-3 col-lg-2">
//                     <div class="card h-100 shadow-sm border-0 rounded overflow-hidden">
//                         <div class="ratio ratio-1x1">
//                             <img src="${e.target.result}" class="card-img-top object-fit-cover" alt="${file.name}">
//                         </div>
//                         <div class="card-body p-2 text-center">
//                             <p class="small text-truncate mb-0" title="${file.name}">${file.name}</p>
//                         </div>
//                     </div>
//                 </div>
//             `);
//         };
//         reader.readAsDataURL(file);
//     });
//     $('#uploadButton').prop('disabled', selectedFiles.length===0);
// });


$('#imageInput').on('change', async function(e){
    const files = Array.from(e.target.files);
    selectedFiles = [];
    $('#modalImagePreviewContainer').empty();

    if(files.length > MAX_IMAGES){
        alert(`You can select up to ${MAX_IMAGES} images.`);
        return;
    }

    for (let file of files) {

        if(file.size > MAX_IMAGE_SIZE_MB * 1024 * 1024){
            alert(`Image ${file.name} exceeds ${MAX_IMAGE_SIZE_MB} MB`);
            return;
        }

        let finalFile = file;

        // 🔥 HEIC → JPG convert
        if (
            file.type === 'image/heic' ||
            file.name.toLowerCase().endsWith('.heic')
        ) {
            try {
                const blob = await heic2any({
                    blob: file,
                    toType: 'image/jpeg',
                    quality: 1
                });

                finalFile = new File(
                    [blob],
                    file.name.replace(/\.heic$/i, '.jpg'),
                    { type: 'image/jpeg' }
                );
            } catch (err) {
                alert(`HEIC convert failed: ${file.name}`);
                continue;
            }
        }

        selectedFiles.push(finalFile);

        // Preview (now always JPG/PNG)
        const reader = new FileReader();
        reader.onload = function(ev){
            $('#modalImagePreviewContainer').append(`
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="card h-100 shadow-sm border-0 rounded overflow-hidden">
                        <div class="ratio ratio-1x1">
                            <img src="${ev.target.result}" class="card-img-top object-fit-cover">
                        </div>
                        <div class="card-body p-2 text-center">
                            <p class="small text-truncate mb-0">${finalFile.name}</p>
                        </div>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(finalFile);
    }

    $('#uploadButton').prop('disabled', selectedFiles.length === 0);
});

// Upload images
$('#uploadButton').on('click', function(){
    if(selectedFiles.length===0) return;
    const formData = new FormData();
    selectedFiles.forEach(f => formData.append('images[]', f));
    const xhr = new XMLHttpRequest();
    xhr.open('POST','{{ route("gallery.upload") }}',true);
    xhr.setRequestHeader('X-CSRF-TOKEN','{{ csrf_token() }}');
    xhr.upload.addEventListener('progress', function(e){
        if(e.lengthComputable){
            const percent = Math.round((e.loaded/e.total)*100);
            $('#progressWrapper').show();
            $('#progressBar').css('width',percent+'%').text(percent+'%');
        }
    });
    xhr.onload = function(){
        const res = JSON.parse(xhr.responseText);
        if(xhr.status===200 && res.success){
            $('#progressBar').removeClass('bg-danger').addClass('bg-success').css('width','100%').text('Upload Successful!');
            $('#statusMessage').removeClass('text-danger').addClass('text-success').text('Images uploaded successfully.');
            setTimeout(()=>location.reload(),1200);
        } else {
            $('#progressBar').removeClass('bg-success').addClass('bg-danger').css('width','100%').text('Upload Failed!');
            $('#statusMessage').removeClass('text-success').addClass('text-danger').text(res.message||'Error occurred.');
        }
        setTimeout(()=>{
            $('#progressWrapper').hide();
            $('#progressBar').removeClass('bg-success bg-danger').css('width','0%').text('0%');
            $('#uploadButton').prop('disabled',true);
        },3000);
    };
    xhr.send(formData);
});

// Modal image view navigation
$('.gallery-thumb').on('click', function(){
    currentIndex = imageIds.indexOf($(this).data('id').toString());
    showModalImage(currentIndex);
    $('#imageModal').modal('show');
});

function showModalImage(idx){
    const id = imageIds[idx];
    $('#modalImage').attr('src','/gallery/preview/'+id);
    $('#modalImageTitle').html('<span class="fw-bold">'+(imageNames[id]??'')+'</span>');
}

$('#prevBtn').on('click', function(){ if(currentIndex>0){ currentIndex--; showModalImage(currentIndex); }});
$('#nextBtn').on('click', function(){ if(currentIndex<imageIds.length-1){ currentIndex++; showModalImage(currentIndex); }});
</script>
@endpush
