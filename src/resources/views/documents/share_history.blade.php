@extends('me::master')

@section('title', 'Share Visitor History')

@push('buttons')
    <a href="{{ route('admin.drive') }}" class="btn btn-sm btn-encodex-clear">
        <i class="fa fa-arrow-left"></i> Back To Drive
    </a>
@endpush

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="mb-0 fw-bold">Share Visitor History</h5>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filter share type">
            <a href="{{ route('admin.drive.share.history') }}" class="btn {{ empty($shareType) ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('admin.drive.share.history', ['type' => 'file']) }}" class="btn {{ ($shareType ?? null) === 'file' ? 'btn-primary' : 'btn-outline-primary' }}">Files</a>
            <a href="{{ route('admin.drive.share.history', ['type' => 'folder']) }}" class="btn {{ ($shareType ?? null) === 'folder' ? 'btn-primary' : 'btn-outline-primary' }}">Folders</a>
        </div>
    </div>

    @forelse($visits as $visit)
        @php
            $doc = $visit->document;
            $folder = $visit->folder;
            $isImage = $doc && Str::startsWith((string) $doc->mime_type, 'image') && Storage::exists($doc->file_path);
        @endphp

        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-body p-0">
                <div class="row g-0 align-items-stretch">
                    <div class="col-md-3 border-end bg-light d-flex align-items-center justify-content-center p-3" style="min-height: 140px;">
                        @if($visit->share_type === 'file' && $doc)
                            @if($isImage)
                                <img src="{{ route('admin.drive.preview', $doc->id) }}" alt="{{ $doc->name }}" class="img-fluid rounded border" style="max-height:120px;object-fit:cover;">
                            @else
                                <div class="text-center text-secondary">
                                    <i class="bi bi-file-earmark-text display-5"></i>
                                    <div class="small mt-2">File</div>
                                </div>
                            @endif
                        @elseif($visit->share_type === 'folder' && $folder)
                            <div class="text-center text-warning-emphasis">
                                <i class="bi bi-folder2-open display-5"></i>
                                <div class="small mt-2">Folder</div>
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="bi bi-question-circle display-5"></i>
                                <div class="small mt-2">Missing Resource</div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-9 p-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <div>
                                <div class="fw-bold">
                                    @if($visit->share_type === 'file')
                                        File: {{ $doc->name ?? 'Deleted File' }}
                                    @else
                                        Folder: {{ $folder->name ?? 'Deleted Folder' }}
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    Type: <span class="text-capitalize">{{ $visit->share_type }}</span>
                                    @if($visit->share_token)
                                        | Token: <code>{{ $visit->share_token }}</code>
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-primary">Visits: {{ $visit->visit_count }}</span>
                        </div>

                        <div class="row small g-2">
                            <div class="col-md-6"><strong>IP:</strong> {{ $visit->ip_address }}</div>
                            <div class="col-md-6"><strong>Device:</strong> {{ $visit->device_type ?? 'Unknown' }} | {{ $visit->device_name ?? 'Unknown' }}</div>
                            <div class="col-md-6"><strong>OS:</strong> {{ $visit->os ?? 'Unknown' }}</div>
                            <div class="col-md-6"><strong>Browser:</strong> {{ $visit->browser ?? 'Unknown' }}</div>
                            <div class="col-md-6"><strong>First Visit:</strong> {{ optional($visit->first_visited_at)->format('Y-m-d H:i:s') ?? '-' }}</div>
                            <div class="col-md-6"><strong>Last Visit:</strong> {{ optional($visit->last_visited_at)->format('Y-m-d H:i:s') ?? '-' }}</div>
                            <div class="col-12"><strong>URL:</strong> <span class="text-break">{{ $visit->visited_url ?? '-' }}</span></div>
                            <div class="col-12"><strong>Referer:</strong> <span class="text-break">{{ $visit->referer ?: '-' }}</span></div>
                            <div class="col-12"><strong>User Agent:</strong> <span class="text-break">{{ $visit->user_agent ?: '-' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No visitor history found yet.</div>
    @endforelse

    @if(method_exists($visits, 'links'))
        <div class="mt-3">
            {{ $visits->links() }}
        </div>
    @endif
</div>
@endsection
