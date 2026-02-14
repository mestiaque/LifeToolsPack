@extends('components.lte.guest')

@section('title', 'File Preview')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0 fw-semibold text-primary">File Preview</h5>
                </div>
                <div class="card-body text-center p-4">
                    @if(Str::startsWith($document->mime_type, 'image'))
                        <img loading="lazy" src="{{ route('drive.preview', $document->id) }}" class="img-fluid mb-3 rounded border" style="max-height:350px;object-fit:cover;" />
                    @else
                        <i class="bi bi-file-earmark-text display-5 text-secondary"></i>
                        <div class="mb-3 fw-semibold">{{ $document->name }}</div>
                    @endif
                    <a href="{{ route('drive.download', $document->id) }}" class="btn btn-primary rounded-2">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
