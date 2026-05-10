@extends('me::guest')

@section('title', 'File Preview')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0 rounded-3">
            <div class="card-body text-center p-4">
                @if(Str::startsWith($document->mime_type, 'image'))
                    <img loading="lazy" src="{{ route('drive.preview', $document->id, false) }}" class="img-fluid mb-3 rounded border" style="max-height:75vh;object-fit:cover;" />
                @else
                    <i class="bi bi-file-earmark-text display-5 text-secondary"></i>
                    <div class="mb-3 fw-semibold">{{ $document->name }}</div>
                @endif
                @if(($shareMode ?? null) === 'permanent')
                    <a href="{{ route('drive.download', $document->id) }}" class="btn btn-primary rounded-2">
                        <i class="bi bi-download"></i> Download
                    </a>
                @endif
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
                fetch("{{ route('drive.shared.heartbeat', ['id' => $document->id, 'token' => $shareToken], false) }}", {
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

@push('css')
<style>
    #breadcrumb{
        display: none !important;
    }
</style>
@endpush
