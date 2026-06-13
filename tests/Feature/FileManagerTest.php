<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\MediaImport;
use App\Models\MediaInUse;
use App\Models\User;
use App\Services\FileManager\FileManagerService;
use App\Services\FileManager\MediaImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_manager_list_is_paginated_searchable_and_returns_thumbnail_urls(): void
    {
        $this->actingAs(User::factory()->create());

        $uploads = $this->uploadsFolder();
        MediaFolder::query()->create([
            'name' => 'folder-a',
            'saved_name_into_storage' => 'folder-a',
            'parent_id' => $uploads->id,
            'status' => 1,
        ]);
        MediaFolder::query()->create([
            'name' => 'folder-b',
            'saved_name_into_storage' => 'folder-b',
            'parent_id' => $uploads->id,
            'status' => 1,
        ]);
        $this->createMedia('uploads/alpha.jpg', $uploads, 'image/jpeg');
        $this->createMedia('uploads/beta.jpg', $uploads, 'image/jpeg');
        $this->createMedia('uploads/gamma.txt', $uploads, 'text/plain');

        $response = $this->getJson(route('backend.file-manager.index', [
            'path' => 'uploads',
            'page' => 1,
            'per_page' => 3,
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pagination.total', 5)
            ->assertJsonPath('data.pagination.has_more', true)
            ->assertJsonCount(3, 'data.items');

        $searchResponse = $this->getJson(route('backend.file-manager.index', [
            'path' => 'uploads',
            'q' => 'alp',
        ]));

        $searchResponse->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.items.0.name', 'alpha.jpg');

        $this->assertNotNull($searchResponse->json('data.items.0.thumbnail_url'));
    }

    public function test_upload_can_stop_on_duplicate_filename_and_suggest_a_safe_name(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        $this->createMedia('uploads/hero.jpg', $uploads, 'image/jpeg');

        $response = $this->post(route('backend.file-manager.photo.upload'), [
            'photo' => UploadedFile::fake()->image('hero.jpg', 80, 80),
            'path' => 'uploads',
            'name' => 'hero',
            'conflict_strategy' => 'error',
        ]);

        $response->assertConflict()
            ->assertJsonPath('success', false)
            ->assertJsonPath('conflict.name', 'hero.jpg')
            ->assertJsonPath('conflict.suggested_file_name', 'hero-2.jpg');
    }

    public function test_thumbnail_endpoint_generates_a_cached_jpeg_derivative(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        Storage::disk('ftp')->put('uploads/photo.jpg', $this->jpegContents(640, 480));
        $this->createMedia('uploads/photo.jpg', $uploads, 'image/jpeg');

        $response = $this->get(route('backend.file-manager.thumbnail', [
            'path' => 'uploads/photo.jpg',
            'width' => 120,
            'height' => 90,
        ]));

        $response->assertOk();
        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));

        $size = getimagesizefromstring($response->getContent());

        $this->assertSame(120, $size[0]);
        $this->assertSame(90, $size[1]);
    }

    public function test_thumbnail_cache_can_be_inspected_and_cleared_by_maintenance_users(): void
    {
        $admin = User::factory()->create();

        app(FileManagerService::class)->clearThumbnailCache();

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        Storage::disk('ftp')->put('uploads/photo.jpg', $this->jpegContents(640, 480));
        $this->createMedia('uploads/photo.jpg', $uploads, 'image/jpeg');

        $this->actingAs($admin)->get(route('backend.file-manager.thumbnail', [
            'path' => 'uploads/photo.jpg',
            'width' => 120,
            'height' => 90,
        ]))->assertOk();

        $this->actingAs($admin)
            ->getJson(route('backend.file-manager.thumbnail-cache'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.files', 1);

        $this->actingAs($admin)
            ->deleteJson(route('backend.file-manager.thumbnail-cache.clear'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.files', 1);

        $this->actingAs($admin)
            ->getJson(route('backend.file-manager.thumbnail-cache'))
            ->assertOk()
            ->assertJsonPath('data.files', 0);
    }

    public function test_file_manager_routes_require_authenticated_users(): void
    {
        $this->getJson(route('backend.file-manager.index'))->assertUnauthorized();
        $this->getJson(route('backend.file-manager.thumbnail-cache'))->assertUnauthorized();
        $this->deleteJson(route('backend.file-manager.thumbnail-cache.clear'))->assertUnauthorized();
    }

    public function test_folder_permission_overrides_restrict_contextual_actions(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        $uploads->permission_overrides = [
            'upload' => false,
            'create_folder' => false,
            'delete' => false,
        ];
        $uploads->save();

        $child = MediaFolder::query()->create([
            'name' => 'Child',
            'saved_name_into_storage' => 'child',
            'parent_id' => $uploads->id,
            'status' => 1,
        ]);

        $this->getJson(route('backend.file-manager.index', [
            'folder_id' => $uploads->id,
        ]))->assertOk()
            ->assertJsonPath('data.permissions.read', true)
            ->assertJsonPath('data.permissions.upload', false)
            ->assertJsonPath('data.permissions.create_folder', false)
            ->assertJsonPath('data.permissions.delete', false);

        $this->postJson(route('backend.file-manager.photo.upload'), [
            'photo' => UploadedFile::fake()->image('blocked.jpg', 80, 80),
            'folder_id' => $uploads->id,
        ])->assertForbidden()
            ->assertJsonPath('ability', 'upload');

        $this->postJson(route('backend.file-manager.photo.upload'), [
            'photo' => UploadedFile::fake()->image('blocked-nested.jpg', 80, 80),
            'path' => 'uploads/new-folder',
        ])->assertForbidden()
            ->assertJsonPath('ability', 'upload');

        $this->postJson(route('backend.file-manager.folder'), [
            'folder_id' => $uploads->id,
            'name' => 'Blocked Folder',
        ])->assertForbidden()
            ->assertJsonPath('ability', 'create_folder');

        $this->deleteJson(route('backend.file-manager.destroy'), [
            'folder_id' => $child->id,
            'type' => 'directory',
        ])->assertForbidden()
            ->assertJsonPath('ability', 'delete');
    }

    public function test_maintenance_users_can_update_folder_permission_overrides(): void
    {
        $this->actingAs(User::factory()->create());

        $uploads = $this->uploadsFolder();

        $this->patchJson(route('backend.file-manager.folder.permissions'), [
            'folder_id' => $uploads->id,
            'overrides' => [
                'upload' => false,
                'create_folder' => false,
                'rename' => true,
            ],
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.permission_overrides.upload', false)
            ->assertJsonPath('data.permission_overrides.create_folder', false)
            ->assertJsonPath('data.permission_overrides.rename', true);

        $this->assertDatabaseHas('media_folders', [
            'id' => $uploads->id,
        ]);
        $this->assertSame([
            'upload' => false,
            'create_folder' => false,
            'rename' => true,
        ], $uploads->refresh()->permission_overrides);

        $this->getJson(route('backend.file-manager.index', [
            'folder_id' => $uploads->id,
        ]))->assertOk()
            ->assertJsonPath('data.permissions.upload', false)
            ->assertJsonPath('data.permissions.create_folder', false)
            ->assertJsonPath('data.permissions.rename', true);

        $this->patchJson(route('backend.file-manager.folder.permissions'), [
            'folder_id' => $uploads->id,
            'overrides' => [],
        ])->assertOk()
            ->assertJsonPath('data.permission_overrides', []);

        $this->assertSame([], $uploads->refresh()->permission_overrides);
    }

    public function test_media_items_can_be_managed_with_database_ids(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        $media = $this->createMedia('uploads/original.jpg', $uploads, 'image/jpeg');

        $renameResponse = $this->patchJson(route('backend.file-manager.rename'), [
            'media_id' => $media->id,
            'type' => 'file',
            'name' => 'Renamed Cover',
        ]);

        $renameResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.media_id', $media->id)
            ->assertJsonPath('data.name', 'renamed-cover.jpg')
            ->assertJsonPath('data.path', 'uploads/original.jpg')
            ->assertJsonPath('data.storage_path', 'uploads/original.jpg');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'filename' => 'renamed-cover.jpg',
            'path' => 'uploads/original.jpg',
        ]);

        $usageResponse = $this->getJson(route('backend.file-manager.usage', [
            'media_id' => $media->id,
            'type' => 'file',
        ]));

        $usageResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 0);
    }

    public function test_db_organization_actions_do_not_touch_storage_paths(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('ftp');
        $uploads = $this->uploadsFolder();
        $archive = MediaFolder::query()->create([
            'name' => 'Archive',
            'saved_name_into_storage' => 'archive',
            'parent_id' => 0,
            'status' => 1,
        ]);
        $media = $this->createMedia('uploads/original.jpg', $uploads, 'image/jpeg');

        $this->postJson(route('backend.file-manager.folder'), [
            'folder_id' => $uploads->id,
            'name' => 'Virtual Folder',
        ])->assertCreated();

        Storage::disk('ftp')->assertMissing('uploads/virtual-folder');

        $this->patchJson(route('backend.file-manager.rename'), [
            'folder_id' => $uploads->id,
            'type' => 'directory',
            'name' => 'Renamed Uploads',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Renamed Uploads')
            ->assertJsonPath('data.path', 'uploads')
            ->assertJsonPath('data.display_path', 'Renamed Uploads');

        $this->assertDatabaseHas('media_folders', [
            'id' => $uploads->id,
            'name' => 'Renamed Uploads',
            'saved_name_into_storage' => 'uploads',
        ]);

        $this->patchJson(route('backend.file-manager.move'), [
            'media_id' => $media->id,
            'type' => 'file',
            'destination_folder_id' => $archive->id,
        ])->assertOk()
            ->assertJsonPath('data.media_id', $media->id)
            ->assertJsonPath('data.folder_id', $archive->id)
            ->assertJsonPath('data.path', 'uploads/original.jpg')
            ->assertJsonPath('data.storage_path', 'uploads/original.jpg');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'media_folder_id' => $archive->id,
            'path' => 'uploads/original.jpg',
        ]);
    }

    public function test_directory_usage_guard_uses_db_folder_tree_not_storage_path_prefix(): void
    {
        $this->actingAs(User::factory()->create());

        $uploads = $this->uploadsFolder();
        $child = MediaFolder::query()->create([
            'name' => 'Child',
            'saved_name_into_storage' => 'child',
            'parent_id' => $uploads->id,
            'status' => 1,
        ]);
        $media = $this->createMedia('flat-storage/photo.jpg', $child, 'image/jpeg');

        MediaInUse::query()->create([
            'media_id' => $media->id,
            'model' => User::class,
            'model_id' => 1,
            'col_name' => 'avatar_url',
            'status' => 1,
        ]);

        $this->deleteJson(route('backend.file-manager.destroy'), [
            'folder_id' => $uploads->id,
            'type' => 'directory',
        ])->assertConflict()
            ->assertJsonPath('usage.count', 1);
    }

    public function test_storage_import_registers_existing_files_without_request_time_scanning(): void
    {
        Storage::fake('ftp');
        Storage::disk('ftp')->put('legacy/gallery/photo.jpg', $this->jpegContents());
        Storage::disk('ftp')->put('legacy/readme.txt', 'plain');

        $dryRun = app(MediaImportService::class)->import('legacy', true, true);

        $this->assertSame('dry_run', $dryRun['status']);
        $this->assertSame(2, $dryRun['scanned']);
        $this->assertSame(2, $dryRun['created']);
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseHas('media_imports', [
            'id' => $dryRun['import_id'],
            'root' => 'legacy',
            'status' => 'dry_run',
            'dry_run' => true,
            'scanned' => 2,
        ]);

        $summary = app(MediaImportService::class)->import('legacy');

        $this->assertSame('completed', $summary['status']);
        $this->assertSame(2, $summary['scanned']);
        $this->assertSame(2, $summary['created']);
        $this->assertSame(0, $summary['failed']);
        $this->assertDatabaseHas('media_folders', [
            'saved_name_into_storage' => 'legacy',
        ]);
        $this->assertDatabaseHas('media_folders', [
            'saved_name_into_storage' => 'gallery',
        ]);
        $this->assertDatabaseHas('media', [
            'disk' => 'ftp',
            'path' => 'legacy/gallery/photo.jpg',
            'filename' => 'photo.jpg',
        ]);
        $this->assertDatabaseHas('media', [
            'disk' => 'ftp',
            'path' => 'legacy/readme.txt',
            'filename' => 'readme.txt',
        ]);

        $secondRun = app(MediaImportService::class)->import('legacy');

        $this->assertSame(0, $secondRun['created']);
        $this->assertSame(2, $secondRun['updated']);
        $this->assertDatabaseCount('media_imports', 3);
    }

    public function test_maintenance_users_can_trigger_storage_import(): void
    {
        Storage::fake('ftp');
        Storage::disk('ftp')->put('legacy/photo.jpg', $this->jpegContents());
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->postJson(route('backend.file-manager.import'), [
            'path' => 'legacy',
            'recursive' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.scanned', 1)
            ->assertJsonPath('data.created', 1);

        $this->actingAs($admin)
            ->getJson(route('backend.file-manager.imports'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.root', 'legacy')
            ->assertJsonPath('data.0.created', 1);

        $this->assertDatabaseHas('media', [
            'path' => 'legacy/photo.jpg',
            'filename' => 'photo.jpg',
        ]);
        $this->assertDatabaseHas('media_imports', [
            'root' => 'legacy',
            'status' => 'completed',
            'creator' => $admin->id,
        ]);
    }

    public function test_import_audit_rows_can_be_pruned_with_retention_policy(): void
    {
        $old = $this->createImportAudit('old', now()->subDays(120));
        $keptByCount = $this->createImportAudit('kept-by-count', now()->subDays(110));
        $recent = $this->createImportAudit('recent', now()->subDays(5));

        $this->artisan('file-manager:prune-imports', [
            '--days' => 90,
            '--keep' => 2,
            '--dry-run' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('media_imports', ['id' => $old->id]);
        $this->assertDatabaseHas('media_imports', ['id' => $keptByCount->id]);
        $this->assertDatabaseHas('media_imports', ['id' => $recent->id]);

        $this->artisan('file-manager:prune-imports', [
            '--days' => 90,
            '--keep' => 2,
        ])->assertExitCode(0);

        $this->assertDatabaseMissing('media_imports', ['id' => $old->id]);
        $this->assertDatabaseHas('media_imports', ['id' => $keptByCount->id]);
        $this->assertDatabaseHas('media_imports', ['id' => $recent->id]);
    }

    private function jpegContents(int $width = 120, int $height = 90): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 160, 150));

        ob_start();
        imagejpeg($image, null, 90);
        $contents = ob_get_clean();
        imagedestroy($image);

        return (string) $contents;
    }

    private function uploadsFolder(): MediaFolder
    {
        return MediaFolder::query()->firstOrCreate(
            ['saved_name_into_storage' => 'uploads', 'parent_id' => 0],
            ['name' => 'uploads', 'is_default' => 1, 'status' => 1],
        );
    }

    private function createMedia(string $path, MediaFolder $folder, string $mimeType): Media
    {
        return Media::query()->create([
            'disk' => 'ftp',
            'path' => $path,
            'filename' => basename($path),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'mime_type' => $mimeType,
            'size' => 1024,
            'media_folder_id' => $folder->id,
            'folders' => [['id' => $folder->id, 'name' => $folder->name, 'path' => 'uploads']],
            'status' => 1,
        ]);
    }

    private function createImportAudit(string $root, \DateTimeInterface $createdAt): MediaImport
    {
        $import = MediaImport::query()->create([
            'disk' => 'ftp',
            'root' => $root,
            'recursive' => true,
            'dry_run' => false,
            'status' => 'completed',
            'scanned' => 1,
            'created' => 1,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'started_at' => $createdAt,
            'finished_at' => $createdAt,
        ]);
        $import->created_at = $createdAt;
        $import->updated_at = $createdAt;
        $import->save();

        return $import->refresh();
    }
}
