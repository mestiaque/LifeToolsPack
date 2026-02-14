@extends('components.lte.guest')

@section('title', 'Shared Folder Files')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0 fw-semibold text-primary">Files in Folder: {{ $folder->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($documents as $doc)
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 text-center border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        @if(Str::startsWith($doc->mime_type, 'image'))
                                            <img loading="lazy" src="{{ route('admin.drive.preview', $doc->id) }}" class="img-fluid mb-2 rounded border" style="max-height:100px;object-fit:cover;" />
                                        @else
                                            <i class="bi bi-file-earmark-text display-5 text-secondary"></i>
                                        @endif
                                        <div class="fw-semibold text-truncate mt-2" title="{{ $doc->name }}">{{ $doc->name }}</div>
                                        <a href="{{ route('drive.download', $doc->id) }}" class="btn btn-sm btn-outline-primary mt-2 rounded-2">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($documents->isEmpty())
                        <div class="text-center text-muted mt-4">No files in this folder.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
