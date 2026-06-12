(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.ItemActions = {
        name: 'FmItemActions',
        props: {
            item: {
                type: Object,
                required: true
            },
            align: {
                type: String,
                default: 'right'
            }
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore(),
                menuOpen: false
            };
        },
        computed: {
            canOpenUrl: function () {
                return this.item.type === 'file' && Boolean(this.item.url || this.item.preview_url);
            },
            hasManagementActions: function () {
                return this.store.canRename || this.store.canMove || this.store.canDelete;
            },
            copyValue: function () {
                return this.item.url || this.item.path;
            }
        },
        methods: {
            toggleMenu: function () {
                this.menuOpen = !this.menuOpen;
                this.store.activeItem = this.item;
            },
            closeMenu: function () {
                this.menuOpen = false;
            },
            primaryAction: function () {
                this.closeMenu();

                if (this.item.type === 'directory') {
                    this.store.openFolder(this.item);
                    return;
                }

                this.store.toggleItem(this.item);
            },
            openUrl: function () {
                var url = this.item.url || this.item.preview_url;

                this.closeMenu();

                if (url) {
                    window.open(url, '_blank', 'noopener');
                }
            },
            copyText: function (value) {
                var textarea;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    return navigator.clipboard.writeText(value);
                }

                textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.setAttribute('readonly', 'readonly');
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);

                return Promise.resolve();
            },
            copyPath: function () {
                var component = this;

                this.closeMenu();
                this.copyText(this.copyValue).catch(function () {
                    component.store.error = 'Could not copy the item path.';
                });
            },
            renameItem: function () {
                var name = window.prompt('Rename item', this.item.name);

                this.closeMenu();

                if (!this.store.canRename) {
                    this.store.setPermissionError();
                    return;
                }

                if (name && name !== this.item.name) {
                    this.store.renameItem(this.item, name);
                }
            },
            moveItem: function () {
                this.closeMenu();

                if (!this.store.canMove) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.openMoveDialog(this.item);
            },
            deleteItem: function () {
                this.closeMenu();

                if (!this.store.canDelete) {
                    this.store.setPermissionError();
                    return;
                }

                if (window.confirm('Delete "' + this.item.name + '"?')) {
                    this.store.destroy(this.item);
                }
            }
        },
        template: [
            '<div class="fm-item-actions" :class="\'is-\' + align" @click.stop @dblclick.stop>',
            '  <button type="button" class="fm-action-trigger" :aria-expanded="menuOpen ? \'true\' : \'false\'" title="Item actions" @click="toggleMenu">',
            '    <i class="mdi mdi-dots-horizontal"></i>',
            '  </button>',
            '  <div v-if="menuOpen" class="fm-action-menu">',
            '    <button type="button" @click="primaryAction">',
            '      <i class="mdi" :class="item.type === \'directory\' ? \'mdi-folder-open-outline\' : \'mdi-check-circle-outline\'"></i>',
            '      <span>{{ item.type === "directory" ? "Open folder" : "Select file" }}</span>',
            '    </button>',
            '    <button v-if="canOpenUrl" type="button" @click="openUrl">',
            '      <i class="mdi mdi-open-in-new"></i>',
            '      <span>Open URL</span>',
            '    </button>',
            '    <button type="button" @click="copyPath">',
            '      <i class="mdi mdi-content-copy"></i>',
            '      <span>{{ item.url ? "Copy URL" : "Copy path" }}</span>',
            '    </button>',
            '    <div v-if="hasManagementActions" class="fm-action-separator"></div>',
            '    <button v-if="store.canRename" type="button" @click="renameItem">',
            '      <i class="mdi mdi-pencil-outline"></i>',
            '      <span>Rename</span>',
            '    </button>',
            '    <button v-if="store.canMove" type="button" @click="moveItem">',
            '      <i class="mdi mdi-folder-move-outline"></i>',
            '      <span>Move</span>',
            '    </button>',
            '    <button v-if="store.canDelete" type="button" class="is-danger" @click="deleteItem">',
            '      <i class="mdi mdi-delete-outline"></i>',
            '      <span>Delete</span>',
            '    </button>',
            '  </div>',
            '</div>'
        ].join('')
    };
})(window);
