@extends('me::master')

@section('title', 'Drive')

@push('buttons')
    @if($currentFolder)
        <button type="button" class="btn btn-sm btn-encodex-create" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fa fa-upload"></i>
        </button>
    @else
        <button type="button" class="btn btn-sm btn-encodex-create" disabled title="Select a folder to upload files">
            <i class="fa fa-upload"></i>
        </button>
    @endif
    <a href="{{ route('admin.drive.share.history') }}" class="btn btn-sm btn-encodex-create" title="View share visitor history">
        <i class="fa fa-history"></i>
    </a>
    <a href="{{ route('admin.drive') }}" class="btn btn-sm btn-encodex-clear" title="Reset to root folders">
        <i class="fa fa-undo"></i>
    </a>


@endpush

@section('content')

<div class="container-fluid drive-shell">
    <div class="row">
        <!-- Sidebar: Folder Tree -->
        <div class="col-md-3 drive-sidebar py-1" id="driveSidebarTree">
            <!-- Share Link Alerts -->
            @foreach($folders as $folder)
                @if(session('share_folder_link_' . $folder->id))
                    <div class="alert alert-info">
                        <strong>Folder Share Link:</strong>
                        <a href="{{ session('share_folder_link_' . $folder->id) }}" target="_blank">{{ session('share_folder_link_' . $folder->id) }}</a>
                        <br>
                        <strong>OTP:</strong> <span class="fw-bold">{{ session('share_folder_otp_' . $folder->id) }}</span>
                        <span class="js-share-copy-source d-none"
                            data-copy-link="{{ session('share_folder_link_' . $folder->id) }}"
                            data-copy-otp="{{ session('share_folder_otp_' . $folder->id) }}"
                            data-auto-copy="1"></span>
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
                                <span class="js-share-copy-source d-none"
                                    data-copy-link="{{ session('share_folder_link_' . $child->id) }}"
                                    data-copy-otp="{{ session('share_folder_otp_' . $child->id) }}"
                                    data-auto-copy="1"></span>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="drive-folder-title"><i class="bi bi-folder2-open"></i> Folders</span>

            </div> --}}
            <ul class="list-group drive-tree-root mb-3 rounded-3 shadow-sm">
                @php
                    $countDocumentsRecursively = function ($folder) use (&$countDocumentsRecursively) {
                        $count = $folder->documents->count();
                        if ($folder->children && $folder->children->count()) {
                            foreach ($folder->children as $childFolder) {
                                $count += $countDocumentsRecursively($childFolder);
                            }
                        }
                        return $count;
                    };
                @endphp
                @foreach($folders as $folder)
                    @php
                        // Determine if this folder or any child/subchild is active
                        $isActive = isset($currentFolder) && (
                            $currentFolder->id == $folder->id ||
                            ($folder->children && $folder->children->contains('id', $currentFolder->id)) ||
                            ($folder->children && $folder->children->flatMap->children->contains('id', $currentFolder->id))
                        );
                    @endphp
                    <li class="list-group-item drive-tree-node {{ isset($currentFolder) && $currentFolder->id == $folder->id ? 'active' : '' }}" data-drive-name="{{ Str::lower($folder->name) }}">
                        <div class="d-flex justify-content-between align-items-center drive-node-row">
                            <span class="d-flex align-items-center drive-node-main">
                                <i class="bi bi-folder-fill me-1"></i>
                                <a href="{{ route('admin.drive', ['folder' => $folder->id]) }}" class="{{ isset($currentFolder) && $currentFolder->id == $folder->id ? 'text-primary' : '' }}">
                                    {{ $folder->name }}
                                </a>
                                <span class="badge bg-secondary ms-2">{{ $countDocumentsRecursively($folder) }}</span>
                                @if($folder->children && count($folder->children))
                                    <button class="btn btn-link btn-sm p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#folder-{{ $folder->id }}">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                @endif
                            </span>
                            <div class="drive-folder-actions dropdown dropstart">
                                <button type="button" class="btn btn-sm drive-folder-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Folder actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameFolderModal-{{ $folder->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $folder->id }}">
                                            <i class="bi bi-share me-1"></i> Share
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.drive.folder.delete', $folder->id) }}" onsubmit="return confirm('Are you sure you want to delete this folder and all its contents?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Folder Rename Modal -->
                        <div class="modal fade" id="renameFolderModal-{{ $folder->id }}" tabindex="-1" aria-labelledby="renameFolderModalLabel-{{ $folder->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST" action="{{ route('admin.drive.folder.update', $folder->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="renameFolderModalLabel-{{ $folder->id }}">Rename Folder</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Folder name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $folder->name }}" maxlength="255" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                                                    <span class="js-share-copy-source d-none"
                                                        data-copy-link="{{ session('share_folder_link_' . $folder->id) }}"
                                                        data-copy-otp="{{ session('share_folder_otp_' . $folder->id) }}"
                                                        data-auto-copy="1"></span>
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
                            <ul class="collapse drive-children ms-3 mt-2 {{ $isActive ? 'show' : '' }}" id="folder-{{ $folder->id }}">
                                @foreach($folder->children as $child)
                                    @php
                                        $isChildActive = isset($currentFolder) && (
                                            $currentFolder->id == $child->id ||
                                            ($child->children && $child->children->contains('id', $currentFolder->id))
                                        );
                                    @endphp
                                    <li class="drive-tree-node" data-drive-name="{{ Str::lower($child->name) }}">
                                        <div class="d-flex justify-content-between align-items-center drive-node-row">
                                            <span class="d-flex align-items-center drive-node-main">
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
                                            <div class="drive-folder-actions dropdown dropstart">
                                                <button type="button" class="btn btn-sm drive-folder-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Folder actions">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameFolderModal-{{ $child->id }}">
                                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $child->id }}">
                                                            <i class="bi bi-share me-1"></i> Share
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.drive.folder.delete', $child->id) }}" onsubmit="return confirm('Are you sure you want to delete this subfolder and all its contents?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Subfolder Rename Modal -->
                                        <div class="modal fade" id="renameFolderModal-{{ $child->id }}" tabindex="-1" aria-labelledby="renameFolderModalLabel-{{ $child->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form method="POST" action="{{ route('admin.drive.folder.update', $child->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="renameFolderModalLabel-{{ $child->id }}">Rename Folder</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="form-label">Folder name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $child->name }}" maxlength="255" required>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
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
                                                                    <span class="js-share-copy-source d-none"
                                                                        data-copy-link="{{ session('share_folder_link_' . $child->id) }}"
                                                                        data-copy-otp="{{ session('share_folder_otp_' . $child->id) }}"
                                                                        data-auto-copy="1"></span>
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
                                            <ul class="collapse drive-children ms-3 mt-2 {{ $isChildActive ? 'show' : '' }}" id="folder-{{ $child->id }}">
                                                @foreach($child->children as $subchild)
                                                    <li class="drive-tree-node" data-drive-name="{{ Str::lower($subchild->name) }}">
                                                        <div class="d-flex justify-content-between align-items-center drive-node-row">
                                                            <span class="d-flex align-items-center drive-node-main">
                                                                <i class="bi bi-folder me-1"></i>
                                                                <a href="{{ route('admin.drive', ['folder' => $subchild->id]) }}" class="{{ isset($currentFolder) && $currentFolder->id == $subchild->id ? 'fw-bold text-primary' : '' }}">
                                                                    {{ $subchild->name }}
                                                                </a>
                                                                <span class="badge bg-secondary ms-2">{{ $subchild->documents->count() }}</span>
                                                            </span>
                                                            <div class="drive-folder-actions dropdown dropstart">
                                                                <button type="button" class="btn btn-sm drive-folder-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Folder actions">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                                    <li>
                                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameFolderModal-{{ $subchild->id }}">
                                                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFolderModal-{{ $subchild->id }}">
                                                                            <i class="bi bi-share me-1"></i> Share
                                                                        </button>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="POST" action="{{ route('admin.drive.folder.delete', $subchild->id) }}" onsubmit="return confirm('Are you sure you want to delete this sub-subfolder and all its contents?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger">
                                                                                <i class="bi bi-trash me-1"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <!-- Sub-subfolder Rename Modal -->
                                                        <div class="modal fade" id="renameFolderModal-{{ $subchild->id }}" tabindex="-1" aria-labelledby="renameFolderModalLabel-{{ $subchild->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <form method="POST" action="{{ route('admin.drive.folder.update', $subchild->id) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="renameFolderModalLabel-{{ $subchild->id }}">Rename Folder</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <label class="form-label">Folder name</label>
                                                                            <input type="text" name="name" class="form-control" value="{{ $subchild->name }}" maxlength="255" required>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
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
                                                                                    <span class="js-share-copy-source d-none"
                                                                                        data-copy-link="{{ session('share_folder_link_' . $subchild->id) }}"
                                                                                        data-copy-otp="{{ session('share_folder_otp_' . $subchild->id) }}"
                                                                                        data-auto-copy="1"></span>
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
            <!-- Folder Creation Inline Form -->
            <div class="drive-create-inline mt-2">
                @if(isset($currentFolder))
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
                            <div class="input-group input-group-sm">
                                <input type="text" name="name" class="form-control rounded-start-2" placeholder="Subfolder name" required>
                                <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                                <button type="submit" class="btn btn-primary rounded-end-2">
                                    <i class="bi bi-folder-plus me-1"></i> Create
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning mb-0">Creating sub-subfolders is not allowed.</div>
                    @endif
                @else
                    <form method="POST" action="{{ route('admin.drive.folder.create') }}">
                        @csrf
                        <label class="form-label fw-bold text-secondary">Create Root Folder</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="name" class="form-control rounded-start-2" placeholder="Root folder name" required>
                            <input type="hidden" name="parent_id" value="">
                            <button type="submit" class="btn btn-success rounded-end-2">
                                <i class="bi bi-folder-plus me-1"></i> Create
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        <!-- Main: Documents Panel -->
        <div class="col-md-9 drive-main py-1">

            {{-- Show file share link if available --}}
            @foreach($documents as $doc)
                @if(session('share_link_' . $doc->id))
                    <div class="alert alert-info">
                        <strong>File Share Link:</strong>
                        <a href="{{ session('share_link_' . $doc->id) }}" target="_blank">{{ session('share_link_' . $doc->id) }}</a>
                        <br>
                        <strong>Mode:</strong>
                        <span class="fw-bold text-capitalize">{{ session('share_mode_' . $doc->id) }}</span>
                        <span class="js-share-copy-source d-none"
                            data-copy-link="{{ session('share_link_' . $doc->id) }}"
                            data-auto-copy="1"></span>
                    </div>
                @endif
            @endforeach

            <div class="drive-quickbar">
                <div class="drive-crumbs">
                    <i class="bi bi-hdd-network me-1"></i>
                    <span>My Drive</span>
                    @if($currentFolder)
                        <i class="bi bi-chevron-right small mx-1"></i>
                        <span class="fw-semibold text-dark">{{ $currentFolder->name }}</span>
                    @endif
                </div>
                <div class="drive-search-mock">
                    <i class="bi bi-search"></i>
                    <input type="search" id="driveSearchInput" class="drive-search-input" placeholder="Search in Drive" aria-label="Search in Drive">
                </div>
                <div class="drive-view-mock">
                    <button type="button" id="driveGridBtn" class="btn btn-light btn-sm is-active" title="Gallery view"><i class="bi bi-grid"></i></button>
                    <button type="button" id="driveListBtn" class="btn btn-light btn-sm" title="List view"><i class="bi bi-list-ul"></i></button>
                </div>
            </div>

            @if($currentFolder)
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('admin.drive.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadModalLabel">Upload Files</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Choose files (max 10)</label>
                                <input id="driveUploadInput" type="file" name="file[]" class="form-control" required multiple>
                                <small class="text-muted d-block mt-2">Max 10 files, total 100MB.</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @if($currentFolder)
            <div id="driveFilesGrid" class="row mt-3 drive-files-grid">
                @forelse($documents as $doc)
                @php
                    $fileExists = Storage::exists($doc->file_path);
                @endphp
                <div class="col-lg-3 col-md-4 col-6 mb-4 drive-file-col" data-drive-file="{{ Str::lower($doc->name) }}">
                    <div
                        class="drive-file-card h-100 position-relative p-3 text-center {{ $fileExists ? 'js-file-open' : '' }}"
                        @if($fileExists) data-file-url="{{ route('admin.drive.preview', $doc->id) }}" @endif
                    >
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle image-three-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameFileModal-{{ $doc->id }}">
                                            <i class="bi bi-pencil-square"></i> Rename
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal-{{ $doc->id }}">
                                            <i class="bi bi-share"></i> Share
                                        </button>
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
                                <img loading="lazy" src="{{ route('admin.drive.preview', $doc->id) }}" class="drive-file-thumb mb-2 border" />
                            @elseif(Str::startsWith($doc->mime_type, 'application/pdf'))
                                <i class="bi bi-file-earmark-pdf display-5 text-danger"></i>
                            @elseif(Str::endsWith($doc->mime_type, 'zip'))
                                <i class="bi bi-file-earmark-zip display-5 text-warning"></i>
                            @else
                                <i class="bi bi-file-earmark-text display-5 text-secondary"></i>
                            @endif
                        </div>
                        <div class="drive-file-name" title="{{ $doc->name }}">{{ $doc->name }}</div>
                        {{-- @if($fileExists)
                            <div class="drive-file-actions mt-2">
                                <a href="{{ route('admin.drive.download', $doc->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Download"><i class="bi bi-download"></i></a>
                                @if(Str::startsWith($doc->mime_type, 'application/pdf'))
                                    <a href="{{ route('admin.drive.preview', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Preview PDF"><i class="bi bi-eye"></i></a>
                                @endif
                            </div>
                        @endif --}}
                    </div>
                    <!-- Rename File Modal -->
                    <div class="modal fade" id="renameFileModal-{{ $doc->id }}" tabindex="-1" aria-labelledby="renameFileModalLabel-{{ $doc->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="POST" action="{{ route('admin.drive.file.update', $doc->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="renameFileModalLabel-{{ $doc->id }}">Rename File</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">File name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $doc->name }}" maxlength="255" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
                                        <p class="mb-2">Choose sharing mode:</p>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="share_mode" id="shareModeTemp-{{ $doc->id }}" value="temporary" checked>
                                            <label class="form-check-label" for="shareModeTemp-{{ $doc->id }}">
                                                Temporary Share (One-time access)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="share_mode" id="shareModePermanent-{{ $doc->id }}" value="permanent">
                                            <label class="form-check-label" for="shareModePermanent-{{ $doc->id }}">
                                                Permanent Share (Unlimited lifetime access)
                                            </label>
                                        </div>
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
    .drive-shell {
        --drive-surface: #ffffff;
        --drive-soft-surface: #f7f9fc;
        --drive-border: #dce4ef;
        --drive-text: #12263a;
        --drive-muted: #5c6f82;
        --drive-primary: #0b5ed7;
        --drive-primary-soft: #e9f2ff;
        --drive-success-soft: #eafaf3;
        --drive-shadow-sm: 0 4px 14px rgba(19, 42, 76, 0.08);
        --drive-shadow-md: 0 10px 26px rgba(19, 42, 76, 0.12);
        color: var(--drive-text);
        background: radial-gradient(circle at 8% 0%, #f3f8ff 0, #f9fbff 35%, #ffffff 70%);
    }
    .drive-sidebar {
        background: linear-gradient(180deg, #f8fbff 0%, #f1f6ff 100%);
        border-right: 1px solid var(--drive-border);
        min-height: 100vh;
        position: relative;
        z-index: 200;
        overflow: visible;
    }
    .drive-sidebar::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: linear-gradient(rgba(11, 94, 215, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(11, 94, 215, 0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.5;
        z-index: 0;
    }
    .drive-sidebar > * {
        position: relative;
    }
    .drive-sidebar .list-group-item.active {
        background: var(--drive-primary-soft);
        color: #083a84;
        border-color: #c8daf8;
        box-shadow: inset 3px 0 0 var(--drive-primary);
        font-weight: 700;
    }
    .drive-sidebar .list-group-item a {
        text-decoration: none;
        color: #1e3d63;
        transition: color .2s ease;
    }
    .drive-sidebar .list-group-item a:hover {
        color: var(--drive-primary);
    }
    .drive-sidebar .list-group {
        border: 1px solid var(--drive-border);
        background: #ffffffd9;
        backdrop-filter: blur(2px);
        overflow: visible !important;
        z-index: 30;
    }
    .drive-sidebar .list-group-item {
        border-color: #edf2f8;
        padding: .6rem .75rem;
        overflow: visible !important;
    }
    .drive-tree-root .drive-tree-node {
        padding: .42rem .55rem;
        border-radius: 8px;
        margin: 2px 4px;
        transition: background-color .2s ease, transform .2s ease;
        overflow: visible;
    }
    .drive-tree-root .drive-tree-node.dropdown-open {
        position: relative;
        z-index: 1250;
    }
    .drive-tree-root > .drive-tree-node.dropdown-open {
        z-index: 1300;
    }
    .drive-tree-root .drive-tree-node:hover {
        background: #f4f8ff;
    }
    .drive-node-row {
        gap: 8px;
    }
    .drive-node-row > span {
        min-width: 0;
    }
    .drive-node-main i.bi-folder-fill,
    .drive-node-main i.bi-folder {
        color: #2e6fc6;
        background: #eaf2ff;
        border: 1px solid #ccdcf6;
        border-radius: 6px;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        margin-right: .45rem !important;
    }
    .drive-node-row a {
        max-width: 130px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .drive-children {
        position: relative;
        margin-left: .8rem !important;
        padding-left: .55rem;
    }
    .drive-children::before {
        content: '';
        position: absolute;
        left: 0;
        top: 2px;
        bottom: 6px;
        width: 1px;
        background: #d3dded;
    }
    .drive-folder-actions {
        flex-shrink: 0;
    }
    .drive-folder-actions .dropdown-menu {
        min-width: 10rem;
        border-radius: 10px;
        border: 1px solid #d9e4f5;
        z-index: 1260;
    }
    .drive-create-inline .input-group .form-control {
        border-color: #c7d8f1;
    }
    .drive-create-inline .input-group .btn {
        min-width: 102px;
    }
    .drive-sidebar .list-group ul {
        list-style: none;
        padding-left: .65rem;
        margin-bottom: 0;
    }
    .drive-folder-title {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .01em;
        color: #0947a6;
    }
    .drive-main {
        background: transparent;
        min-height: 100vh;
        padding-bottom: 32px;
        position: relative;
        z-index: 1;
    }
    .drive-quickbar {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding: 10px 12px;
        border: 1px solid var(--drive-border);
        border-radius: 12px;
        background: #ffffffc9;
        box-shadow: var(--drive-shadow-sm);
    }
    .drive-crumbs {
        display: inline-flex;
        align-items: center;
        color: var(--drive-muted);
        font-size: .9rem;
    }
    .drive-search-mock {
        border: 1px solid #d8e3f2;
        border-radius: 999px;
        min-height: 36px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        color: #647b96;
        background: #f8fbff;
    }
    .drive-search-input {
        width: 100%;
        border: 0;
        background: transparent;
        outline: 0;
        color: #395678;
        font-size: .92rem;
    }
    .drive-search-input::placeholder {
        color: #7088a3;
    }
    .drive-view-mock {
        display: inline-flex;
        gap: 6px;
    }
    .drive-view-mock .btn.is-active {
        background: #e7f0ff;
        border-color: #bdd2f3;
        color: #1e4f93;
    }
    .drive-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding: 14px 16px;
        border: 1px solid var(--drive-border);
        border-radius: 14px;
        background: linear-gradient(120deg, #ffffff 0%, #f6faff 100%);
        box-shadow: var(--drive-shadow-sm);
    }
    .drive-main-header h5 {
        color: #0f2f54;
    }
    .drive-file-card {
        transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
        border: 1px solid var(--drive-border);
        border-radius: 14px;
        box-shadow: var(--drive-shadow-sm);
        background: var(--drive-surface);
        overflow: visible;
        animation: driveFadeUp .35s ease both;
    }
    .drive-file-col {
        position: relative;
        z-index: 1;
        overflow: visible;
    }
    .drive-file-col.dropdown-open {
        z-index: 30;
    }
    .drive-file-card .dropdown-menu {
        z-index: 1090;
    }
    .drive-file-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--drive-shadow-md);
        border-color: #b9d1f5;
    }
    .drive-file-card.js-file-open {
        cursor: pointer;
    }
    .drive-files-grid.drive-list-mode > .drive-file-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .drive-files-grid.drive-list-mode .drive-file-card {
        display: grid;
        grid-template-columns: 92px 1fr auto;
        align-items: center;
        gap: 12px;
        text-align: left;
        padding: 12px !important;
    }
    .drive-files-grid.drive-list-mode .drive-file-card > div:first-of-type {
        margin-bottom: 0 !important;
    }
    .drive-files-grid.drive-list-mode .drive-file-thumb {
        max-height: 64px;
    }
    .drive-files-grid.drive-list-mode .drive-file-name {
        margin-top: 0;
        max-width: 100%;
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        word-break: break-word;
    }
    .drive-files-grid.drive-list-mode .drive-file-actions {
        margin-top: 0 !important;
        justify-self: end;
        white-space: nowrap;
    }
    .drive-file-thumb {
        max-height: 110px;
        width: 100%;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(23, 40, 79, 0.12);
    }
    .drive-file-name {
        font-size: .95rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 8px;
        color: #163455;
    }
    .drive-file-actions .btn {
        margin: 0 3px;
        border-radius: 8px;
    }
    .drive-sidebar .alert,
    .drive-main .alert {
        border-radius: 10px;
        border: 1px solid #cfe0fb;
        background: #f3f8ff;
    }
    .drive-sidebar .card,
    .drive-main .card {
        border: 1px solid var(--drive-border);
        box-shadow: var(--drive-shadow-sm);
        border-radius: 14px;
    }
    .drive-main .badge.bg-secondary,
    .drive-sidebar .badge.bg-secondary {
        background: #dce8fb !important;
        color: #23456c;
        border: 1px solid #c8d8f1;
    }
    .drive-folder-menu-btn {
        border: 1px solid #d4deec;
        color: #35567d;
        border-radius: 8px;
        padding: .15rem .35rem;
        line-height: 1;
        background: #fff;
    }
    .drive-folder-menu-btn:hover,
    .drive-folder-menu-btn:focus {
        background: var(--drive-primary-soft);
        color: var(--drive-primary);
        border-color: #bfd3f0;
        box-shadow: none;
    }
    .drive-file-card .dropdown .btn {
        border: 1px solid #d4deec;
        color: #35567d;
    }

    .modal {
        z-index: 3500;
    }

    .image-three-dots{
        padding: 5px 9px !important;
    }
    .drive-file-card .dropdown .btn:hover {
        background: var(--drive-primary-soft);
        color: var(--drive-primary);
    }
    @keyframes driveFadeUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (max-width: 767px) {
        .drive-shell {
            background: #f8fbff;
        }
        .drive-sidebar {
            min-height: auto;
            border-right: none;
            border-bottom: 1px solid var(--drive-border);
        }
        .drive-tree-root .drive-tree-node.dropdown-open {
            z-index: 1400;
        }
        .drive-tree-root > .drive-tree-node.dropdown-open {
            z-index: 1450;
        }
        .drive-folder-actions .dropdown-menu {
            z-index: 1410;
        }
        .drive-main-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
        }
        .drive-quickbar {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .drive-view-mock {
            justify-content: flex-start;
        }
        .drive-file-card {
            border-radius: 12px;
        }
        .drive-files-grid.drive-list-mode .drive-file-card {
            grid-template-columns: 64px 1fr;
        }
        .drive-files-grid.drive-list-mode .drive-file-actions {
            justify-self: start;
            grid-column: 1 / -1;
            margin-top: 8px !important;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Keep modals outside scrollable/overflow containers to prevent clipping.
        document.querySelectorAll('.modal').forEach(function (modalEl) {
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var searchInput = document.getElementById('driveSearchInput');
        var fileCols = Array.from(document.querySelectorAll('.drive-file-col'));
        var treeNodes = Array.from(document.querySelectorAll('.drive-tree-node'));
        var grid = document.getElementById('driveFilesGrid');
        var gridBtn = document.getElementById('driveGridBtn');
        var listBtn = document.getElementById('driveListBtn');
        var uploadInput = document.getElementById('driveUploadInput');

        var normalize = function (value) {
            return (value || '').toString().trim().toLowerCase();
        };

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var query = normalize(searchInput.value);

                fileCols.forEach(function (node) {
                    var name = normalize(node.getAttribute('data-drive-file'));
                    node.style.display = !query || name.indexOf(query) !== -1 ? '' : 'none';
                });

                treeNodes.forEach(function (node) {
                    var name = normalize(node.getAttribute('data-drive-name'));
                    node.style.display = !query || name.indexOf(query) !== -1 ? '' : 'none';
                });
            });
        }

        if (grid && gridBtn && listBtn) {
            var setMode = function (mode) {
                var isList = mode === 'list';
                grid.classList.toggle('drive-list-mode', isList);
                listBtn.classList.toggle('is-active', isList);
                gridBtn.classList.toggle('is-active', !isList);
            };

            gridBtn.addEventListener('click', function () {
                setMode('grid');
            });

            listBtn.addEventListener('click', function () {
                setMode('list');
            });
        }

        if (uploadInput) {
            uploadInput.addEventListener('change', function () {
                if (this.files && this.files.length > 10) {
                    alert('Max 10 files allowed');
                    this.value = '';
                }
            });
        }

        var legacyCopy = function (text) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.setAttribute('readonly', '');
            textArea.style.position = 'absolute';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.select();
            var copied = document.execCommand('copy');
            document.body.removeChild(textArea);
            return copied;
        };

        var copyText = function (text) {
            if (!text) {
                return Promise.resolve(false);
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                return navigator.clipboard.writeText(text)
                    .then(function () { return true; })
                    .catch(function () { return legacyCopy(text); });
            }
            return Promise.resolve(legacyCopy(text));
        };

        var buildCopyText = function (source) {
            var link = source.getAttribute('data-copy-link');
            var otp = source.getAttribute('data-copy-otp');
            var lines = [];

            if (link) {
                lines.push('Link: ' + link);
            }
            if (otp) {
                lines.push('OTP: ' + otp);
            }
            return lines.join('\n');
        };

        var showToast = function (message, type) {
            var container = document.getElementById('driveCopyToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'driveCopyToastContainer';
                container.style.position = 'fixed';
                container.style.top = '16px';
                container.style.right = '16px';
                container.style.zIndex = '5000';
                document.body.appendChild(container);
            }

            var toast = document.createElement('div');
            toast.textContent = message;
            toast.style.minWidth = '220px';
            toast.style.maxWidth = '320px';
            toast.style.marginTop = '8px';
            toast.style.padding = '10px 14px';
            toast.style.borderRadius = '10px';
            toast.style.boxShadow = '0 8px 18px rgba(0, 0, 0, 0.18)';
            toast.style.color = '#fff';
            toast.style.fontSize = '14px';
            toast.style.background = type === 'success' ? '#198754' : '#dc3545';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px)';
            toast.style.transition = 'all .2s ease';

            container.appendChild(toast);
            requestAnimationFrame(function () {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            });

            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 220);
            }, 1800);
        };

        var pendingCopyText = null;
        var pendingCopyBound = false;

        var bindDeferredCopyOnUserAction = function () {
            if (pendingCopyBound) {
                return;
            }
            pendingCopyBound = true;

            var tryDeferredCopy = function () {
                if (!pendingCopyText) {
                    return;
                }

                copyText(pendingCopyText).then(function (ok) {
                    if (ok) {
                        showToast('Copied shared link', 'success');
                        pendingCopyText = null;
                        pendingCopyBound = false;
                        document.removeEventListener('click', tryDeferredCopy, true);
                        document.removeEventListener('keydown', tryDeferredCopy, true);
                        document.removeEventListener('touchstart', tryDeferredCopy, true);
                    }
                });
            };

            document.addEventListener('click', tryDeferredCopy, true);
            document.addEventListener('keydown', tryDeferredCopy, true);
            document.addEventListener('touchstart', tryDeferredCopy, true);
        };

        var autoCopyTarget = document.querySelector('.js-share-copy-source[data-auto-copy="1"]');
        if (autoCopyTarget) {
            var shareText = buildCopyText(autoCopyTarget);
            copyText(shareText).then(function (ok) {
                if (ok) {
                    showToast('Copied shared link', 'success');
                    return;
                }

                pendingCopyText = shareText;
                bindDeferredCopyOnUserAction();
                showToast('Auto-copy blocked. Tap anywhere once.', 'error');
            });
        }

        document.querySelectorAll('.drive-folder-actions .dropdown-item[data-bs-toggle="modal"], .dropdown-item[data-bs-toggle="modal"]').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        });

        document.querySelectorAll('#driveSidebarTree .drive-tree-node .dropdown').forEach(function (dropdownEl) {
            var treeNode = dropdownEl.closest('.drive-tree-node');
            var rootTreeNode = dropdownEl.closest('.list-group-item.drive-tree-node');
            if (!treeNode) {
                return;
            }

            dropdownEl.addEventListener('shown.bs.dropdown', function () {
                treeNode.classList.add('dropdown-open');
                if (rootTreeNode) {
                    rootTreeNode.classList.add('dropdown-open');
                }
            });

            dropdownEl.addEventListener('hidden.bs.dropdown', function () {
                treeNode.classList.remove('dropdown-open');
                if (rootTreeNode && rootTreeNode !== treeNode) {
                    var hasOpenDescendant = rootTreeNode.querySelector('.dropdown.show');
                    if (!hasOpenDescendant) {
                        rootTreeNode.classList.remove('dropdown-open');
                    }
                } else if (rootTreeNode) {
                    rootTreeNode.classList.remove('dropdown-open');
                }
            });
        });

        document.querySelectorAll('.drive-file-col .dropdown').forEach(function (dropdownEl) {
            var cardCol = dropdownEl.closest('.drive-file-col');
            if (!cardCol) {
                return;
            }

            dropdownEl.addEventListener('shown.bs.dropdown', function () {
                cardCol.classList.add('dropdown-open');
            });

            dropdownEl.addEventListener('hidden.bs.dropdown', function () {
                cardCol.classList.remove('dropdown-open');
            });
        });

        document.querySelectorAll('.js-file-open').forEach(function (card) {
            card.addEventListener('click', function (event) {
                if (event.target.closest('button, a, input, label, form, .dropdown, .dropdown-menu, .modal')) {
                    return;
                }

                var url = card.getAttribute('data-file-url');
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    });
</script>
@endpush
@endsection
