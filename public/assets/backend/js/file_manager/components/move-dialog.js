(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.MoveDialog = {
        name: 'FmMoveDialog',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        computed: {
            item: function () {
                return this.store.moveDialog.item;
            },
            destination: {
                get: function () {
                    return this.store.moveDialog.destination;
                },
                set: function (value) {
                    this.store.moveDialog.destination = this.normalizePath(value);
                }
            },
            parentPath: function () {
                var path = this.store.path || '';
                var parts = path.split('/').filter(Boolean);

                parts.pop();

                return parts.join('/');
            },
            currentFolderLabel: function () {
                return this.store.path || 'Root';
            },
            destinationLabel: function () {
                return this.destination || 'Root';
            },
            isSelfDestination: function () {
                if (!this.item || this.item.type !== 'directory') {
                    return false;
                }

                return this.destination === this.item.path || this.destination.indexOf(this.item.path + '/') === 0;
            },
            shortcuts: function () {
                var component = this;
                var shortcuts = [
                    {label: 'Root', path: '', icon: 'mdi-home-outline'},
                    {label: 'Current folder', path: this.store.path || '', icon: 'mdi-folder-outline'}
                ];

                if (this.parentPath !== this.store.path) {
                    shortcuts.push({label: 'Parent folder', path: this.parentPath, icon: 'mdi-subdirectory-arrow-left'});
                }

                this.store.directoryItems.forEach(function (folder) {
                    if (!component.item || folder.path !== component.item.path) {
                        shortcuts.push({label: folder.name, path: folder.path, icon: 'mdi-folder'});
                    }
                });

                this.store.recentFolders.forEach(function (path) {
                    if (!shortcuts.some(function (shortcut) {
                        return shortcut.path === path;
                    })) {
                        shortcuts.push({label: path, path: path, icon: 'mdi-history'});
                    }
                });

                return shortcuts.slice(0, 12);
            }
        },
        methods: {
            normalizePath: function (value) {
                return String(value || '')
                    .replace(/\\/g, '/')
                    .split('/')
                    .filter(function (segment) {
                        return segment && segment !== '.' && segment !== '..';
                    })
                    .join('/');
            },
            choose: function (path) {
                this.destination = path;
            },
            close: function () {
                this.store.closeMoveDialog();
            },
            submit: function () {
                if (this.isSelfDestination || this.store.saving) {
                    return;
                }

                this.store.confirmMove();
            }
        },
        template: [
            '<div v-if="store.moveDialog.isOpen" class="fm-dialog-layer" @mousedown.self="close">',
            '  <form class="fm-move-dialog" @submit.prevent="submit">',
            '    <div class="fm-dialog-head">',
            '      <div>',
            '        <span class="fm-chip"><i class="mdi mdi-folder-move-outline"></i> Move item</span>',
            '        <h4>{{ item ? item.name : "Selected item" }}</h4>',
            '      </div>',
            '      <button type="button" class="fm-icon-btn" @click="close" aria-label="Close move dialog"><i class="mdi mdi-close"></i></button>',
            '    </div>',
            '    <label class="fm-path-field">Destination media folder<input v-model="destination" type="text" placeholder="Root or folder/path"></label>',
            '    <div class="fm-move-current"><i class="mdi mdi-map-marker-path"></i><span>{{ destinationLabel }}</span></div>',
            '    <div class="fm-storage-safe-note is-compact"><i class="mdi mdi-database-sync-outline"></i><span>Move updates the database folder only. Storage paths and disk files are not moved.</span></div>',
            '    <div class="fm-shortcut-list">',
            '      <button v-for="shortcut in shortcuts" :key="shortcut.icon + shortcut.path + shortcut.label" type="button" :class="{active: destination === shortcut.path}" @click="choose(shortcut.path)">',
            '        <i class="mdi" :class="shortcut.icon"></i>',
            '        <span>{{ shortcut.label }}</span>',
            '      </button>',
            '    </div>',
            '    <div v-if="isSelfDestination" class="fm-dialog-warning"><i class="mdi mdi-alert-circle-outline"></i><span>A folder cannot be moved into itself.</span></div>',
            '    <div class="fm-dialog-actions">',
            '      <button type="button" class="fm-light-btn" @click="close">Cancel</button>',
            '      <button type="submit" class="fm-primary-btn" :disabled="store.saving || isSelfDestination"><i class="mdi mdi-folder-move-outline"></i><span>{{ store.saving ? "Moving..." : "Move here" }}</span></button>',
            '    </div>',
            '  </form>',
            '</div>'
        ].join('')
    };
})(window);
