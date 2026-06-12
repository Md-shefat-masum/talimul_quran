(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.FileGrid = {
        name: 'FmFileGrid',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        methods: {
            isSelected: function (item) {
                return this.store.selected.some(function (selectedItem) {
                    return selectedItem.path === item.path;
                });
            },
            clickItem: function (item) {
                this.store.activeItem = item;

                if (item.type === 'file') {
                    this.store.toggleItem(item);
                }
            },
            openItem: function (item) {
                if (item.type === 'directory') {
                    this.store.openFolder(item);
                    return;
                }

                this.store.toggleItem(item);
            },
            iconClass: function (item) {
                if (item.type === 'directory') {
                    return 'mdi-folder';
                }

                if (item.is_image) {
                    return 'mdi-file-image-outline';
                }

                return 'mdi-file-outline';
            }
        },
        template: [
            '<section class="fm-files">',
            '  <div v-if="store.loading" class="fm-state"><i class="mdi mdi-loading mdi-spin"></i><span>Loading FTP files...</span></div>',
            '  <div v-else-if="store.error" class="fm-state is-error"><i class="mdi mdi-alert-circle-outline"></i><strong>{{ store.error }}</strong><small>Check FTP_HOST, FTP_PORT, FTP_USERNAME, FTP_PASSWORD, and FTP_ROOT.</small></div>',
            '  <div v-else-if="!store.filteredItems.length" class="fm-state"><i class="mdi mdi-folder-open-outline"></i><strong>No files here yet</strong><small>Upload a photo or create a folder to start.</small></div>',
            '  <div v-else class="fm-file-collection" :class="\'is-\' + store.viewMode">',
            '    <button type="button" v-for="item in store.filteredItems" :key="item.path" class="fm-file-card" :class="{selected: isSelected(item), active: store.activeItem && store.activeItem.path === item.path, folder: item.type === \'directory\'}" @click="clickItem(item)" @dblclick="openItem(item)">',
            '      <span class="fm-thumb">',
            '        <img v-if="item.is_image && item.preview_url" :src="item.preview_url" :alt="item.name">',
            '        <i v-else class="mdi" :class="iconClass(item)"></i>',
            '      </span>',
            '      <span class="fm-file-copy">',
            '        <strong>{{ item.name }}</strong>',
            '        <small>{{ item.size_label || item.type }}</small>',
            '      </span>',
            '      <span class="fm-selected-mark"><i class="mdi mdi-check"></i></span>',
            '    </button>',
            '  </div>',
            '</section>'
        ].join('')
    };
})(window);
