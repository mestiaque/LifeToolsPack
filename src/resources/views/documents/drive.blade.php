@extends('me::master')

@section('title', 'Drive')

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar: Folder Tree -->
        <div class="col-md-3 drive-sidebar py-4">
            <!-- Share Link Alerts -->
            @foreach($folders as $folder)
                @if(session('share_folder_link_' . $folder->id))
                    <div class="alert alert-info">
                        <strong>Folder Share Link:</strong>
                        <a href="{{ session('share_folder_link_' . $folder->id) }}" target="_blank">{{ session('share_folder_link_' . $folder->id) }}</a>
                        <br>
                        <strong>OTP:</strong> <span class="fw-bold">{{ session('share_folder_otp_' . $folder->id) }}</span>
                    </div>
                @endif
                @if($folder->children && count($folder->children))
                    @foreach($folder->children as $child)
                        @if(session('share_folder_link_' . $child->id))
                            <div class="alert alert-info ms-3">
                                <strong>Subfolder Share Link:</strong>
                                <a href="{{ session('share_folder_link_' . $child->id) }}" target="_blank">{{ session('share_folder_link_' . $child->id) }}</a>
                                <br>
                                <strong>OTP:</strong> <span class="fw-bold">{{ session('share_folder_otp_' . $child->id) }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="drive-folder-title"><i class="bi bi-folder2-open"></i> Folders</span>
                <a href="{{ route('admin.drive') }}" class="btn btn-outline-secondary btn-sm" title="Reset to root folders">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
            <ul class="list-group mb-3 rounded-3 shadow-sm">
                @foreach($folders as $folder)
                    @php
                        // Determine if this folder or any child/subchild is active
                        $isActive = isset($currentFolder) && (
                            $currentFolder->id == $folder->id ||
                            ($folder->children && $folder->children->contains('id', $currentFolder->id)) ||
                            ($folder->children && $folder->children->flatMap->children->contains('id', $currentFolder->id))
                        );
                    @endphp
                    <li class="list-group-item {{ isset($currentFolder) && $currentFolder->id == $folder->id ? 'active' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center">
                                <i class="bi bi-folder-fill me-1"></i>
                                <a href="{{ route('admin.drive', ['folder' => $folder->id]) }}" class="{{ isset($currentFolder) && $currentFolder->id == $folder->id ? 'text-primary' : '' }}">
                                    {{ $folder->name }}
                                </a>
                                <span class="badge bg-secondary ms-2">{{ $folder->documents->count() }}</span>
                                @if($folder->children && count($folder->children))
                                    <button class="btn btn-link btn-sm p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#folder-{{ $folder->id }}">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                @endif
                            </span>
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $folder->id }}" title="Share Folder">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                        <!-- Folder Share Modal -->
                        <div class="modal fade" id="shareFolderModal-{{ $folder->id }}" tabindex="-1" aria-labelledby="shareFolderModalLabel-{{ $folder->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.drive.folder.share', $folder->id) }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shareFolderModalLabel-{{ $folder->id }}">Share Folder</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Click "Generate Link" to create a shareable link with OTP protection for this folder.</p>
                                            @if(session('share_folder_link_' . $folder->id))
                                                <div class="alert alert-success mt-2">
                                                    Share Link: <a href="{{ session('share_folder_link_' . $folder->id) }}" target="_blank">{{ session('share_folder_link_' . $folder->id) }}</a>
                                                    <br>
                                                    OTP: <span class="fw-bold">{{ session('share_folder_otp_' . $folder->id) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Generate Link</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if($folder->children && count($folder->children))
                            <ul class="collapse ms-3 mt-2 {{ $isActive ? 'show' : '' }}" id="folder-{{ $folder->id }}">
                                @foreach($folder->children as $child)
                                    @php
                                        $isChildActive = isset($currentFolder) && (
                                            $currentFolder->id == $child->id ||
                                            ($child->children && $child->children->contains('id', $currentFolder->id))
                                        );
                                    @endphp
                                    <li>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-folder me-1"></i>
                                                <a href="{{ route('admin.drive', ['folder' => $child->id]) }}" class="{{ isset($currentFolder) && $currentFolder->id == $child->id ? 'fw-bold text-primary' : '' }}">
                                                    {{ $child->name }}
                                                </a>
                                                <span class="badge bg-secondary ms-2">{{ $child->documents->count() }}</span>
                                                @if($child->children && count($child->children))
                                                    <button class="btn btn-link btn-sm p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#folder-{{ $child->id }}">
                                                        <i class="bi bi-chevron-down"></i>
                                                    </button>
                                                @endif
                                            </span>
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $child->id }}" title="Share Folder">
                                                <i class="bi bi-share"></i>
                                            </button>
                                        </div>
                                        <!-- Subfolder Share Modal -->
                                        <div class="modal fade" id="shareFolderModal-{{ $child->id }}" tabindex="-1" aria-labelledby="shareFolderModalLabel-{{ $child->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="POST" action="{{ route('admin.drive.folder.share', $child->id) }}">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="shareFolderModalLabel-{{ $child->id }}">Share Folder</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Click "Generate Link" to create a shareable link with OTP protection for this folder.</p>
                                                            @if(session('share_folder_link_' . $child->id))
                                                                <div class="alert alert-success mt-2">
                                                                    Share Link: <a href="{{ session('share_folder_link_' . $child->id) }}" target="_blank">{{ session('share_folder_link_' . $child->id) }}</a>
                                                                    <br>
                                                                    OTP: <span class="fw-bold">{{ session('share_folder_otp_' . $child->id) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Generate Link</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @if($child->children && count($child->children))
                                            <ul class="collapse ms-3 mt-2 {{ $isChildActive ? 'show' : '' }}" id="folder-{{ $child->id }}">
                                                @foreach($child->children as $subchild)
                                                    <li>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="d-flex align-items-center">
                                                                <i class="bi bi-folder me-1"></i>
                                                                <a href="{{ route('admin.drive', ['folder' => $subchild->id]) }}" class="{{ isset($currentFolder) && $currentFolder->id == $subchild->id ? 'fw-bold text-primary' : '' }}">
                                                                    {{ $subchild->name }}
                                                                </a>
                                                                <span class="badge bg-secondary ms-2">{{ $subchild->documents->count() }}</span>
                                                            </span>
                                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $subchild->id }}" title="Share Folder">
                                                                <i class="bi bi-share"></i>
                                                            </button>
                                                        </div>
                                                        <!-- Sub-subfolder Share Modal -->
                                                        <div class="modal fade" id="shareFolderModal-{{ $subchild->id }}" tabindex="-1" aria-labelledby="shareFolderModalLabel-{{ $subchild->id }}" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <form method="POST" action="{{ route('admin.drive.folder.share', $subchild->id) }}">
                                                                    @csrf
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="shareFolderModalLabel-{{ $subchild->id }}">Share Folder</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Click "Generate Link" to create a shareable link with OTP protection for this folder.</p>
                                                                            @if(session('share_folder_link_' . $subchild->id))
                                                                                <div class="alert alert-success mt-2">
                                                                                    Share Link: <a href="{{ session('share_folder_link_' . $subchild->id) }}" target="_blank">{{ session('share_folder_link_' . $subchild->id) }}</a>
                                                                                    <br>
                                                                                    OTP: <span class="fw-bold">{{ session('share_folder_otp_' . $subchild->id) }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Generate Link</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
            <!-- Folder Creation Form -->
            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-body p-3">
                    @if(isset($currentFolder))
                        {{-- Allow subfolder creation if depth < 2 --}}
                        @php
                            $depth = 0;
                            $folderPtr = $currentFolder;
                            while ($folderPtr && $folderPtr->parent_id) {
                                $depth++;
                                $folderPtr = $folderPtr->parent;
                            }
                        @endphp
                        @if($depth < 2)
                            <form method="POST" action="{{ route('admin.drive.folder.create') }}">
                                @csrf
                                <label class="form-label fw-bold text-secondary">
                                    Create Subfolder in <span class="text-primary">{{ $currentFolder->name }}</span>
                                </label>
                                <input type="text" name="name" class="form-control mb-2 rounded-2" placeholder="Subfolder name" required>
                                <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-2">
                                    <i class="bi bi-folder-plus"></i> New Subfolder
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">Creating sub-subfolders is not allowed.</div>
                        @endif
                    @else
                        <form method="POST" action="{{ route('admin.drive.folder.create') }}">
                            @csrf
                            <label class="form-label fw-bold text-secondary">Create Root Folder</label>
                            <input type="text" name="name" class="form-control mb-2 rounded-2" placeholder="Root folder name" required>
                            <input type="hidden" name="parent_id" value="">
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-2"><i class="bi bi-folder-plus"></i> New Root Folder</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <!-- Main: Documents Panel -->
        <div class="col-md-9 drive-main py-4">
            @if(session('success'))
                <div class="alert alert-success rounded-2 shadow-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger rounded-2 shadow-sm">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Show file share link and OTP if available --}}
            @foreach($documents as $doc)
                @if(session('share_link_' . $doc->id))
                    <div class="alert alert-info">
                        <strong>File Share Link:</strong>
                        <a href="{{ session('share_link_' . $doc->id) }}" target="_blank">{{ session('share_link_' . $doc->id) }}</a>
                        <br>
                        <strong>OTP:</strong> <span class="fw-bold">{{ session('share_otp_' . $doc->id) }}</span>
                    </div>
                @endif
            @endforeach

            <div class="drive-main-header">
                <div>
                    <h5 class="mb-0 fw-semibold text-dark d-inline-flex align-items-center">
                        <i class="bi bi-files"></i> {{ $currentFolder->name ?? 'Files' }}
                        @if($currentFolder)
                            <span class="badge bg-secondary ms-2">{{ $documents->count() }} files</span>
                        @endif
                    </h5>
                </div>
                @if($currentFolder)
                <form method="POST" action="{{ route('admin.drive.upload') }}" enctype="multipart/form-data" class="mb-0">
                    @csrf
                    <div class="input-group rounded-2 shadow-sm" style="max-width:330px;">
                        <input type="file" name="file[]" class="form-control rounded-start-2" required multiple accept="image/*" onchange="if(this.files.length>10){alert('Max 10 files allowed');this.value='';}">
                        <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                        <button type="submit" class="btn btn-success btn-sm rounded-end-2"><i class="bi bi-upload"></i> Upload</button>
                    </div>
                    <small class="text-muted">Max 10 files, total 100MB.</small>
                </form>
                @endif
            </div>
            @if($currentFolder)
            <div class="row mt-3">
                @forelse($documents as $doc)
                @php
                    $fileExists = Storage::exists($doc->file_path);
                @endphp
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="drive-file-card h-100 position-relative p-3 text-center">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal-{{ $doc->id }}">
                                            <i class="bi bi-share"></i> Share
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.drive.download', $doc->id) }}" class="dropdown-item">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.drive.delete', $doc->id) }}" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            @if(!$fileExists)
                                <div class="text-danger mb-2">
                                    <i class="bi bi-exclamation-triangle display-5"></i>
                                    <div>File missing</div>
                                </div>
                            @elseif(Str::startsWith($doc->mime_type, 'image'))
                                <img loading="lazy" src="{{ route('admin.drive.preview', $doc->id) }}" loading="lazy" class="drive-file-thumb mb-2 border" />
                            @elseif(Str::startsWith($doc->mime_type, 'application/pdf'))
                                <i class="bi bi-file-earmark-pdf display-5 text-danger"></i>
                            @elseif(Str::endsWith($doc->mime_type, 'zip'))
                                <i class="bi bi-file-earmark-zip display-5 text-warning"></i>
                            @else
                                <i class="bi bi-file-earmark-text display-5 text-secondary"></i>
                            @endif
                        </div>
                        <div class="drive-file-name" title="{{ $doc->name }}">{{ Str::limit($doc->name, 22) }}</div>
                        @if($fileExists)
                            <div class="drive-file-actions mt-2">
                                <a href="{{ route('admin.drive.download', $doc->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Download"><i class="bi bi-download"></i></a>
                                @if(Str::startsWith($doc->mime_type, 'application/pdf'))
                                    <a href="{{ route('admin.drive.preview', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Preview PDF"><i class="bi bi-eye"></i></a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <!-- Share Modal -->
                    <div class="modal fade" id="shareModal-{{ $doc->id }}" tabindex="-1" aria-labelledby="shareModalLabel-{{ $doc->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.drive.share', $doc->id) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="shareModalLabel-{{ $doc->id }}">Share File</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Click "Generate Link" to create a shareable link with OTP protection.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Generate Link</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 text-secondary">
                    <i class="bi bi-folder-x display-4"></i> <br>
                    <span>No files found in this folder.</span>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </div>
</div>
<!-- Bootstrap tooltips -->

@push('css')
<style>
    .drive-sidebar {
        background: #f5f7fa;
        border-right: 1px solid #e9ecef;
        min-height: 100vh;
    }
    .drive-sidebar .list-group-item.active {
        background: none;
        color: #000000;
        font-weight: 600;
    }
    .drive-sidebar .list-group-item a {
        text-decoration: none;
        color: inherit;
    }
    .drive-folder-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #0b5ed7;
    }
    .drive-main {
        background: #fff;
        min-height: 100vh;
        padding-bottom: 32px;
    }
    .drive-file-card {
        transition: box-shadow .2s;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        box-shadow: 0 2px 8px #e9ecef;
        overflow: hidden;
    }
    .drive-file-card:hover {
        box-shadow: 0 4px 16px #b4e9fc;
        border-color: #0b5ed7;
    }
    .drive-file-thumb {
        max-height: 110px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 1px 4px #e9ecef;
    }
    .drive-file-name {
        font-size: 1rem;
        font-weight: 500;
        text-truncate: ellipsis;
        margin-top: 8px;
        color: #222;
    }
    .drive-file-actions .btn {
        margin: 0 3px;
    }
    .drive-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    @media (max-width: 767px) {
        .drive-sidebar {
            min-height: auto;
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }
        .drive-main-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection
