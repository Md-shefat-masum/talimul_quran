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
            },
            treeRows: function () {
                var filtered = this.filterTreeNodes(this.store.directoryTree || []);

                return this.flattenTreeRows(filtered, 0, []);
            }
        },
        methods: {
            filterTreeNodes: function (nodes) {
                var component = this;
                var query = (this.store.directoryTreeQuery || '').trim().toLowerCase();

                return (nodes || []).map(function (node) {
                    var children = component.filterTreeNodes(node.children || []);
                    var label = ((node.name || '') + ' ' + (node.path || '') + ' ' + (node.display_path || '')).toLowerCase();
                    var matches = query === '' || label.indexOf(query) !== -1;

                    if (!matches && !children.length) {
                        return null;
                    }

                    return Object.assign({}, node, {children: children});
                }).filter(Boolean);
            },
            flattenTreeRows: function (nodes, depth, rows) {
                var component = this;

                (nodes || []).forEach(function (node) {
                    var expanded = component.isTreeExpanded(node);

                    rows.push({
                        node: node,
                        depth: depth,
                        expanded: expanded
                    });

                    if (expanded) {
                        component.flattenTreeRows(node.children || [], depth + 1, rows);
                    }
                });

                return rows;
            },
            hasTreeChildren: function (node) {
                return Boolean(node && (node.has_children || (node.children && node.children.length)));
            },
            isTreeExpanded: function (node) {
                var query = (this.store.directoryTreeQuery || '').trim();
                var path = node && node.path ? node.path : '';

                if (query !== '') {
                    return true;
                }

                if (this.store.treeExpanded[path]) {
                    return true;
                }

                return this.store.path === path || (path !== '' && this.store.path.indexOf(path + '/') === 0);
            },
            isTreeActive: function (node) {
                return Boolean(node && node.path === this.store.path);
            },
            toggleTree: function (node) {
                if (!this.hasTreeChildren(node)) {
                    return;
                }

                this.store.toggleTreeFolder(node.path);
            },
            openTreePath: function (node) {
                this.openPath(node && node.path ? node.path : '');
            },
            updateTreeSearch: function (event) {
                this.store.setDirectoryTreeQuery(event.target.value);
            },
            clearTreeSearch: function () {
                this.store.setDirectoryTreeQuery('');
            },
            refreshTree: function () {
                this.store.refreshDirectoryTree();
            },
            isTreeLoading: function (node) {
                return this.store.isTreeLoading(node && node.path ? node.path : '');
            },
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
            '  <div class="fm-sidebar-section fm-directory-tree-section">',
            '    <div class="fm-sidebar-label">Directory tree</div>',
            '    <label class="fm-tree-search">',
            '      <i class="mdi mdi-magnify"></i>',
            '      <input type="search" :value="store.directoryTreeQuery" placeholder="Search folders" @input="updateTreeSearch">',
            '      <button v-if="store.directoryTreeQuery" type="button" title="Clear folder search" @click="clearTreeSearch"><i class="mdi mdi-close"></i></button>',
            '    </label>',
            '    <div class="fm-tree-panel">',
            '      <div class="fm-tree-root-row">',
            '        <button type="button" class="fm-tree-toggle is-placeholder" tabindex="-1"></button>',
            '        <button type="button" class="fm-tree-node" :class="{active: store.path === \'\'}" @click="goHome">',
            '          <i class="mdi mdi-folder-home-outline"></i><span>Home</span>',
            '        </button>',
            '        <button type="button" class="fm-tree-refresh" :disabled="store.directoryTreeLoading" title="Refresh folder tree" @click="refreshTree">',
            '          <i class="mdi" :class="store.directoryTreeLoading ? \'mdi-loading mdi-spin\' : \'mdi-refresh\'"></i>',
            '        </button>',
            '      </div>',
            '      <div v-if="store.directoryTreeLoading && !store.directoryTree.length" class="fm-tree-empty">Loading folders...</div>',
            '      <div v-else-if="!treeRows.length" class="fm-tree-empty">No folders found</div>',
            '      <div v-for="row in treeRows" :key="row.node.path || row.node.id" class="fm-tree-row" :style="{ paddingLeft: (0.2 + (row.depth * 0.82)) + \'rem\' }">',
            '        <button type="button" class="fm-tree-toggle" :class="{\'is-placeholder\': !hasTreeChildren(row.node)}" :disabled="isTreeLoading(row.node)" @click="toggleTree(row.node)">',
            '          <i v-if="isTreeLoading(row.node)" class="mdi mdi-loading mdi-spin"></i>',
            '          <span v-else-if="hasTreeChildren(row.node)">{{ row.expanded ? "-" : "+" }}</span>',
            '        </button>',
            '        <button type="button" class="fm-tree-node" :class="{active: isTreeActive(row.node)}" @click="openTreePath(row.node)">',
            '          <i class="mdi" :class="row.expanded && hasTreeChildren(row.node) ? \'mdi-folder-open-outline\' : \'mdi-folder-outline\'"></i>',
            '          <span :title="row.node.display_path || row.node.path">{{ row.node.name }}</span>',
            '        </button>',
            '      </div>',
            '    </div>',
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
