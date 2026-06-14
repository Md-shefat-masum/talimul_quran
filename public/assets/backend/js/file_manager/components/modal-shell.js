(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.ModalShell = {
        name: 'FmModalShell',
        components: {
            FmSidebar: window.FileManagerComponents.Sidebar,
            FmToolbar: window.FileManagerComponents.Toolbar,
            FmFileGrid: window.FileManagerComponents.FileGrid,
            FmDetailsPanel: window.FileManagerComponents.DetailsPanel,
            FmUploadPanel: window.FileManagerComponents.UploadPanel,
            FmMoveDialog: window.FileManagerComponents.MoveDialog,
            FmDeleteDialog: window.FileManagerComponents.DeleteDialog
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        methods: {
            crumb: function (path) {
                this.store.load(path);
            }
        },
        template: [
            '<div v-if="store.isOpen" class="fm-overlay" @mousedown.self="store.close()">',
            '  <div class="fm-modal" role="dialog" aria-modal="true" aria-label="File manager">',
            '    <fm-sidebar></fm-sidebar>',
            '    <main class="fm-main">',
            '      <fm-toolbar></fm-toolbar>',
            '      <div class="fm-breadcrumb-row">',
            '        <nav class="fm-breadcrumbs" aria-label="File path">',
            '          <button v-for="item in store.breadcrumbs" :key="item.path || \'root\'" type="button" @click="crumb(item.path)">{{ item.label }}</button>',
            '        </nav>',
            '        <div class="fm-selection-count">{{ store.selected.length }} selected</div>',
            '      </div>',
            '      <fm-upload-panel v-if="store.mode === \'upload\'"></fm-upload-panel>',
            '      <div v-else class="fm-browser-layout">',
            '        <fm-file-grid></fm-file-grid>',
            '        <fm-details-panel></fm-details-panel>',
            '      </div>',
            '      <footer class="fm-footer">',
            '        <button type="button" class="fm-light-btn" @click="store.close">Cancel</button>',
            '        <button type="button" class="fm-primary-btn" :disabled="!store.selected.length" @click="store.useSelected"><i class="mdi mdi-check-circle-outline"></i><span>Use selected</span></button>',
            '      </footer>',
            '      <fm-move-dialog></fm-move-dialog>',
            '      <fm-delete-dialog></fm-delete-dialog>',
            '    </main>',
            '    <button type="button" class="fm-close" @click="store.close" aria-label="Close file manager"><i class="mdi mdi-close"></i></button>',
            '  </div>',
            '</div>'
        ].join('')
    };

    window.FileManagerComponents.PageShell = {
        name: 'FmPageShell',
        components: {
            FmSidebar: window.FileManagerComponents.Sidebar,
            FmToolbar: window.FileManagerComponents.Toolbar,
            FmFileGrid: window.FileManagerComponents.FileGrid,
            FmDetailsPanel: window.FileManagerComponents.DetailsPanel,
            FmUploadPanel: window.FileManagerComponents.UploadPanel,
            FmMoveDialog: window.FileManagerComponents.MoveDialog,
            FmDeleteDialog: window.FileManagerComponents.DeleteDialog
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        mounted: function () {
            var store = this.store;

            store.selected = [];
            store.activeItem = null;
            store.error = '';
            store.mode = 'browse';
            store.loadDirectoryTree('', true);
            store.load(window.FileManagerConfig.defaults.path || '', 1).then(function () {
                if (!store.canMaintenance) {
                    return null;
                }

                return Promise.all([
                    store.loadThumbnailCache(),
                    store.loadImportHistory()
                ]);
            });
        },
        methods: {
            crumb: function (path) {
                this.store.load(path);
            },
            clearSelection: function () {
                this.store.selected = [];
                this.store.activeItem = null;
            }
        },
        template: [
            '<div class="fm-page-frame">',
            '  <fm-sidebar></fm-sidebar>',
            '  <main class="fm-main">',
            '    <fm-toolbar></fm-toolbar>',
            '    <div class="fm-breadcrumb-row">',
            '      <nav class="fm-breadcrumbs" aria-label="File path">',
            '        <button v-for="item in store.breadcrumbs" :key="item.path || \'root\'" type="button" @click="crumb(item.path)">{{ item.label }}</button>',
            '      </nav>',
            '      <div class="fm-selection-count">',
            '        <span>{{ store.selected.length }} selected</span>',
            '        <button v-if="store.selected.length" type="button" @click="clearSelection">Clear</button>',
            '      </div>',
            '    </div>',
            '    <fm-upload-panel v-if="store.mode === \'upload\'"></fm-upload-panel>',
            '    <div v-else class="fm-browser-layout">',
            '      <fm-file-grid></fm-file-grid>',
            '      <fm-details-panel></fm-details-panel>',
            '    </div>',
            '    <fm-move-dialog></fm-move-dialog>',
            '    <fm-delete-dialog></fm-delete-dialog>',
            '  </main>',
            '</div>'
        ].join('')
    };

    window.FileManagerComponents.FileManager = {
        name: 'FileManager',
        components: {
            FmFloatingButton: window.FileManagerComponents.FloatingButton,
            FmModalShell: window.FileManagerComponents.ModalShell
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        template: [
            '<div class="fm-root">',
            '  <fm-floating-button :open="store.isOpen" @open="store.open({})"></fm-floating-button>',
            '  <fm-modal-shell></fm-modal-shell>',
            '</div>'
        ].join('')
    };

    window.FileManagerComponents.FileManagerPage = {
        name: 'FileManagerPage',
        components: {
            FmPageShell: window.FileManagerComponents.PageShell
        },
        template: '<fm-page-shell></fm-page-shell>'
    };
})(window);
