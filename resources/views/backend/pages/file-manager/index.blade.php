@extends('backend.layout.app')

@section('title', 'File Manager')

@section('content')
<div class="file-manager-page-shell">
    <div class="file-manager-page-hero">
        <div>
            <div class="file-manager-page-hero__eyebrow">Media Library</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-folder-multiple-image"></i>
                </span>
                File Manager
            </h3>
            <p class="file-manager-page-hero__subtitle">Browse, upload, organize, and maintain media from the dashboard workspace.</p>
        </div>
    </div>

    <file-manager-page></file-manager-page>
</div>
@endsection
