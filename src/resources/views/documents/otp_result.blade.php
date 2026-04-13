@extends('me::guest')

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
                    @if(($shareMode ?? null) === 'temporary')
                        <div class="alert alert-warning text-start">
                            <strong>Temporary Share:</strong> This link is one-time access only.
                        </div>
                    @elseif(($shareMode ?? null) === 'permanent')
                        <div class="alert alert-success text-start">
                            <strong>Permanent Share:</strong> This file can be viewed anytime.
                        </div>
                    @endif

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

@if(($enableAutoRefresh ?? false) && !empty($shareToken ?? null))
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                fetch("{{ route('drive.shared.heartbeat', ['id' => $document->id, 'token' => $shareToken]) }}", {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                }).finally(function () {
                    window.location.reload();
                });
            }, 30000);
        });
    </script>
    @endpush
@endif
