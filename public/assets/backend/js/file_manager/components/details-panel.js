(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.DetailsPanel = {
        name: 'FmDetailsPanel',
        components: {
            FmItemActions: window.FileManagerComponents.ItemActions
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore(),
                permissionDraft: {}
            };
        },
        computed: {
            item: function () {
                return this.store.activeItem || this.store.selected[0] || null;
            },
            usage: function () {
                return this.item && this.item.usage ? this.item.usage : {count: 0, has_usage: false, items: []};
            },
            usageCount: function () {
                return Number(this.usage.count || 0);
            },
            itemKey: function () {
                return this.store.itemKey(this.item);
            },
            organizationNote: function () {
                if (!this.item) {
                    return '';
                }

                return this.item.type === 'directory'
                    ? 'Renaming or moving this folder changes database organization only. Storage keys and files stay unchanged.'
                    : 'Renaming or moving this file changes database metadata only. The storage path stays unchanged.';
            },
            permissionAbilities: function () {
                return [
                    {key: 'read', label: 'Read'},
                    {key: 'upload', label: 'Upload'},
                    {key: 'create_folder', label: 'Create'},
                    {key: 'rename', label: 'Rename'},
                    {key: 'move', label: 'Move'},
                    {key: 'delete', label: 'Delete'}
                ];
            }
        },
        watch: {
            itemKey: function () {
                this.loadUsage();
                this.resetPermissionDraft();
            }
        },
        mounted: function () {
            this.loadUsage();
            this.resetPermissionDraft();
        },
        methods: {
            resetPermissionDraft: function () {
                var overrides = this.item && this.item.permission_overrides ? this.item.permission_overrides : {};
                var draft = {};

                this.permissionAbilities.forEach(function (ability) {
                    if (Object.prototype.hasOwnProperty.call(overrides, ability.key)) {
                        draft[ability.key] = overrides[ability.key] ? 'allow' : 'deny';
                        return;
                    }

                    draft[ability.key] = 'inherit';
                });

                this.permissionDraft = draft;
            },
            savePermissionOverrides: function () {
                var overrides = {};

                this.permissionAbilities.forEach(function (ability) {
                    if (this.permissionDraft[ability.key] === 'allow') {
                        overrides[ability.key] = true;
                    }

                    if (this.permissionDraft[ability.key] === 'deny') {
                        overrides[ability.key] = false;
                    }
                }, this);

                this.store.updateFolderPermissions(this.item, overrides);
            },
            loadUsage: function () {
                if (!this.item || !this.item.path) {
                    return;
                }

                this.store.refreshUsage(this.item).catch(function () {
                    // Details already has the list-provided usage snapshot.
                });
            },
            renameItem: function () {
                var name;

                if (!this.item) {
                    return;
                }

                name = window.prompt('Rename item', this.item.name);

                if (!this.store.canRename) {
                    this.store.setPermissionError();
                    return;
                }

                if (name && name !== this.item.name) {
                    this.store.renameItem(this.item, name);
                }
            },
            moveItem: function () {
                if (!this.item) {
                    return;
                }

                if (!this.store.canMove) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.openMoveDialog(this.item);
            },
            removeItem: function () {
                if (!this.item) {
                    return;
                }

                if (!this.store.canDelete) {
                    this.store.setPermissionError();
                    return;
                }

                this.store.openDeleteDialog(this.item);
            }
        },
        template: [
            '<aside class="fm-details">',
            '  <template v-if="item">',
            '    <div class="fm-details-head">',
            '      <h4>{{ item.name }}</h4>',
            '      <fm-item-actions :item="item" align="left"></fm-item-actions>',
            '    </div>',
            '    <div class="fm-details-preview">',
            '      <img v-if="item.is_image && item.preview_url" :src="item.preview_url" :alt="item.name" loading="lazy" decoding="async">',
            '      <i v-else class="mdi" :class="item.type === \'directory\' ? \'mdi-folder\' : \'mdi-file-outline\'"></i>',
            '    </div>',
            '    <dl>',
            '      <div><dt>Type</dt><dd>{{ item.type === "directory" ? "Media folder" : "Media file" }}</dd></div>',
            '      <div><dt>Size</dt><dd>{{ item.size_label || "Folder" }}</dd></div>',
            '      <div><dt>Display path</dt><dd>{{ item.display_path || item.path }}</dd></div>',
            '      <div v-if="item.storage_path"><dt>Storage path</dt><dd>{{ item.storage_path }}</dd></div>',
            '      <div v-else><dt>Folder key</dt><dd>{{ item.path || "Root" }}</dd></div>',
            '      <div v-if="item.media_id || item.folder_id"><dt>Database ID</dt><dd>{{ item.media_id || item.folder_id }}</dd></div>',
            '      <div><dt>Modified</dt><dd>{{ item.modified_at || "Unknown" }}</dd></div>',
            '    </dl>',
            '    <div class="fm-storage-safe-note">',
            '      <i class="mdi mdi-database-lock-outline"></i>',
            '      <span>{{ organizationNote }}</span>',
            '    </div>',
            '    <div v-if="item.type === \'directory\' && store.canMaintenance" class="fm-permission-box">',
            '      <div class="fm-permission-title">',
            '        <i class="mdi mdi-shield-key-outline"></i>',
            '        <span>Folder permissions</span>',
            '      </div>',
            '      <div class="fm-permission-grid">',
            '        <label v-for="ability in permissionAbilities" :key="ability.key">',
            '          <span>{{ ability.label }}</span>',
            '          <select v-model="permissionDraft[ability.key]">',
            '            <option value="inherit">Inherit</option>',
            '            <option value="allow">Allow</option>',
            '            <option value="deny">Deny</option>',
            '          </select>',
            '        </label>',
            '      </div>',
            '      <button type="button" class="fm-light-btn w-100" :disabled="store.saving" @click="savePermissionOverrides">',
            '        <i class="mdi" :class="store.saving ? \'mdi-loading mdi-spin\' : \'mdi-content-save-outline\'"></i><span>Save overrides</span>',
            '      </button>',
            '    </div>',
            '    <div class="fm-usage-box" :class="{\'has-usage\': usageCount > 0}">',
            '      <div class="fm-usage-title">',
            '        <i class="mdi" :class="usageCount > 0 ? \'mdi-link-variant\' : \'mdi-shield-check-outline\'"></i>',
            '        <span>{{ usageCount > 0 ? usageCount + " usage track(s)" : "No usage tracked" }}</span>',
            '      </div>',
            '      <ul v-if="usageCount > 0">',
            '        <li v-for="usageItem in usage.items" :key="usageItem.id">',
            '          <strong>{{ usageItem.label }}</strong>',
            '          <small>{{ usageItem.field_name }}<template v-if="usageItem.updated_at"> / {{ usageItem.updated_at }}</template></small>',
            '        </li>',
            '      </ul>',
            '    </div>',
            '    <div class="fm-detail-actions">',
            '      <button v-if="item.type === \'file\'" type="button" class="fm-primary-btn w-100" @click="store.toggleItem(item)"><i class="mdi mdi-check-circle-outline"></i><span>Select file</span></button>',
            '      <button v-if="store.canRename" type="button" class="fm-light-btn w-100" @click="renameItem"><i class="mdi mdi-pencil-outline"></i><span>Rename</span></button>',
            '      <button v-if="store.canMove" type="button" class="fm-light-btn w-100" @click="moveItem"><i class="mdi mdi-folder-move-outline"></i><span>Move</span></button>',
            '      <button v-if="store.canDelete" type="button" class="fm-danger-btn w-100" @click="removeItem"><i class="mdi mdi-delete-outline"></i><span>Delete</span></button>',
            '    </div>',
            '  </template>',
            '  <div v-else class="fm-details-empty">',
            '    <i class="mdi mdi-information-outline"></i>',
            '    <strong>Details</strong>',
            '    <span>Select a media item to inspect database location, storage path, and usage.</span>',
            '  </div>',
            '</aside>'
        ].join('')
    };
})(window);
