@extends('backend.layout.app')

@section('title', 'File Manager')

@section('content')
<div class="file-manager-page-shell">
    <x-backend.page-header
        variant="hero"
        kicker="Media Library"
        title="File Manager"
        subtitle="Browse, upload, organize, and maintain media from the dashboard workspace."
        icon="mdi mdi-folder-multiple-image"
    />

    <file-manager-page></file-manager-page>
</div>
@endsection
