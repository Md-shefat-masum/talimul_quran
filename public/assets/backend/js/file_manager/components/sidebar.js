(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.Sidebar = {
        name: 'FmSidebar',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        computed: {
            currentFolders: function () {
                return this.store.directoryItems.slice(0, 5);
            },
            recentFolders: function () {
                return this.store.recentFolders.slice(0, 5);
            }
        },
        methods: {
            goHome: function () {
                this.store.query = '';
                this.store.load('', 1);
            },
            openPath: function (path) {
                this.store.setMode('browse');
                this.store.query = '';
                this.store.load(path || '', 1);
            },
            openUpload: function () {
                if (!this.store.canUpload) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.setMode('upload');
            },
            refreshCache: function () {
                this.store.loadThumbnailCache();
            },
            clearCache: function () {
                if (!window.confirm('Clear cached thumbnails?')) {
                    return;
                }

                this.store.clearThumbnailCache();
            },
            refreshImports: function () {
                this.store.loadImportHistory();
            },
            importStorage: function () {
                var path = window.prompt('Import storage path into database media rows', this.store.path || 'uploads');

                if (path === null) {
                    return;
                }

                this.store.importMedia({
                    path: path || 'uploads',
                    recursive: true
                });
            }
        },
        template: [
            '<aside class="fm-sidebar">',
            '  <div class="fm-brand">',
            '    <span><i class="mdi mdi-folder-image"></i></span>',
            '    <div><strong>Media Drive</strong><small>Database library</small></div>',
            '  </div>',
            '  <button v-if="store.canUpload" type="button" class="fm-sidebar-action is-primary" @click="openUpload">',
            '    <i class="mdi mdi-cloud-upload-outline"></i><span>Upload photo</span>',
            '  </button>',
            '  <nav class="fm-nav">',
            '    <button type="button" :class="{active: store.mode === \'browse\'}" @click="store.setMode(\'browse\')"><i class="mdi mdi-view-grid-outline"></i><span>My files</span></button>',
            '    <button type="button" @click="goHome"><i class="mdi mdi-home-outline"></i><span>Root folder</span></button>',
            '    <button v-if="store.canUpload" type="button" :class="{active: store.mode === \'upload\'}" @click="openUpload"><i class="mdi mdi-image-edit-outline"></i><span>Photo editor</span></button>',
            '  </nav>',
            '  <div v-if="currentFolders.length" class="fm-sidebar-section">',
            '    <div class="fm-sidebar-label">Folders here</div>',
            '    <button v-for="folder in currentFolders" :key="store.itemKey(folder)" type="button" class="fm-sidebar-folder" @click="openPath(folder.path)">',
            '      <i class="mdi mdi-folder-outline"></i><span>{{ folder.name }}</span>',
            '    </button>',
            '  </div>',
            '  <div v-if="recentFolders.length" class="fm-sidebar-section">',
            '    <div class="fm-sidebar-label">Recent paths</div>',
            '    <button v-for="path in recentFolders" :key="path" type="button" class="fm-sidebar-folder" @click="openPath(path)">',
            '      <i class="mdi mdi-history"></i><span>{{ path }}</span>',
            '    </button>',
            '  </div>',
            '  <div v-if="store.canMaintenance" class="fm-sidebar-section fm-maintenance-box">',
            '    <div class="fm-sidebar-label">Maintenance</div>',
            '    <div class="fm-cache-card">',
            '      <div>',
            '        <strong>{{ store.thumbnailCache.files }} thumbnail(s)</strong>',
            '        <small>{{ store.thumbnailCache.bytes_label }}</small>',
            '      </div>',
            '      <button type="button" class="fm-cache-icon" :disabled="store.thumbnailCacheLoading" title="Refresh thumbnail cache stats" @click="refreshCache">',
            '        <i class="mdi" :class="store.thumbnailCacheLoading ? \'mdi-loading mdi-spin\' : \'mdi-refresh\'"></i>',
            '      </button>',
            '    </div>',
            '    <button type="button" class="fm-sidebar-folder is-danger" :disabled="store.thumbnailCacheLoading || store.thumbnailCache.files < 1" @click="clearCache">',
            '      <i class="mdi mdi-broom"></i><span>Clear thumbnail cache</span>',
            '    </button>',
            '    <button type="button" class="fm-sidebar-folder" :disabled="store.importingMedia" @click="importStorage">',
            '      <i class="mdi" :class="store.importingMedia ? \'mdi-loading mdi-spin\' : \'mdi-database-import-outline\'"></i><span>{{ store.importingMedia ? "Importing..." : "Import storage index" }}</span>',
            '    </button>',
            '    <div v-if="store.importSummary" class="fm-import-summary">',
            '      <strong>#{{ store.importSummary.import_id }} {{ store.importSummary.status }}</strong>',
            '      <small>{{ store.importSummary.created }} created / {{ store.importSummary.updated }} updated / {{ store.importSummary.failed }} failed</small>',
            '    </div>',
            '    <div class="fm-import-history">',
            '      <div class="fm-import-history-head">',
            '        <span>Recent imports</span>',
            '        <button type="button" :disabled="store.importHistoryLoading" title="Refresh import history" @click="refreshImports">',
            '          <i class="mdi" :class="store.importHistoryLoading ? \'mdi-loading mdi-spin\' : \'mdi-refresh\'"></i>',
            '        </button>',
            '      </div>',
            '      <div v-if="!store.importHistory.length" class="fm-import-empty">No import history</div>',
            '      <div v-for="item in store.importHistory" :key="item.id" class="fm-import-row">',
            '        <i class="mdi" :class="item.failed ? \'mdi-alert-circle-outline\' : \'mdi-check-circle-outline\'"></i>',
            '        <div>',
            '          <strong>#{{ item.id }} {{ item.root }}</strong>',
            '          <small>{{ item.created }} new / {{ item.updated }} updated / {{ item.failed }} failed</small>',
            '        </div>',
            '      </div>',
            '    </div>',
            '  </div>',
            '  <div class="fm-storage-note">',
            '    <i class="mdi mdi-server-network"></i>',
            '    <span>DB organization, stable storage</span>',
            '  </div>',
            '</aside>'
        ].join('')
    };
})(window);
