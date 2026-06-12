(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.Toolbar = {
        name: 'FmToolbar',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        methods: {
            createFolder: function () {
                var name = window.prompt('Folder name');

                if (!this.store.canCreateFolder) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.createFolder(name);
            },
            openUpload: function () {
                if (!this.store.canUpload) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.setMode('upload');
            }
        },
        template: [
            '<header class="fm-toolbar">',
            '  <div class="fm-title">',
            '    <h3>File Manager</h3>',
            '    <p>Organize media in the database while physical storage paths stay stable.</p>',
            '  </div>',
            '  <div class="fm-toolbar-actions">',
            '    <label class="fm-search"><i class="mdi mdi-magnify"></i><input v-model="store.query" type="search" placeholder="Search files" @input="store.search"></label>',
            '    <button type="button" class="fm-icon-btn" :class="{active: store.viewMode === \'grid\'}" @click="store.viewMode = \'grid\'" title="Grid view"><i class="mdi mdi-view-grid-outline"></i></button>',
            '    <button type="button" class="fm-icon-btn" :class="{active: store.viewMode === \'list\'}" @click="store.viewMode = \'list\'" title="List view"><i class="mdi mdi-format-list-bulleted"></i></button>',
            '    <button v-if="store.canCreateFolder" type="button" class="fm-light-btn" @click="createFolder"><i class="mdi mdi-folder-plus-outline"></i><span>Folder</span></button>',
            '    <button v-if="store.canUpload" type="button" class="fm-primary-btn" @click="openUpload"><i class="mdi mdi-cloud-upload-outline"></i><span>Upload</span></button>',
            '  </div>',
            '</header>'
        ].join('')
    };
})(window);
