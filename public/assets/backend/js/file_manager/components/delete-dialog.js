(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.DeleteDialog = {
        name: 'FmDeleteDialog',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        computed: {
            item: function () {
                return this.store.deleteDialog.item;
            },
            usage: function () {
                return this.store.deleteDialog.usage || (this.item && this.item.usage) || {count: 0, has_usage: false, items: []};
            },
            usageCount: function () {
                return Number(this.usage.count || 0);
            },
            hasUsage: function () {
                return this.usageCount > 0;
            },
            itemLabel: function () {
                return this.item ? this.item.name : 'Selected item';
            },
            itemPath: function () {
                if (!this.item) {
                    return '';
                }

                return this.item.display_path || this.item.storage_path || this.item.path || '';
            },
            primaryCopy: function () {
                if (this.hasUsage) {
                    return this.store.canForceDelete
                        ? 'This item is still attached to content. Force delete can leave broken images or document links in those places.'
                        : 'This item is still attached to content and cannot be deleted by your current permission profile.';
                }

                return 'This removes the item from the media database. Storage-safe organization remains unchanged for other items.';
            }
        },
        methods: {
            close: function () {
                this.store.closeDeleteDialog();
            },
            deleteItem: function () {
                if (this.hasUsage || this.store.saving) {
                    return;
                }

                this.store.confirmDelete(false);
            },
            forceDeleteItem: function () {
                if (!this.hasUsage || !this.store.canForceDelete || this.store.saving) {
                    return;
                }

                this.store.confirmDelete(true);
            }
        },
        template: [
            '<div v-if="store.deleteDialog.isOpen" class="fm-dialog-layer" @mousedown.self="close">',
            '  <section class="fm-delete-dialog" role="alertdialog" aria-modal="true" aria-label="Delete media item">',
            '    <div class="fm-dialog-head">',
            '      <div>',
            '        <span class="fm-chip is-danger"><i class="mdi mdi-delete-alert-outline"></i> Delete item</span>',
            '        <h4>{{ itemLabel }}</h4>',
            '      </div>',
            '      <button type="button" class="fm-icon-btn" @click="close" aria-label="Close delete dialog"><i class="mdi mdi-close"></i></button>',
            '    </div>',
            '    <div class="fm-delete-summary" :class="{\'has-usage\': hasUsage}">',
            '      <i class="mdi" :class="hasUsage ? \'mdi-link-variant-alert\' : \'mdi-alert-outline\'"></i>',
            '      <div>',
            '        <strong>{{ hasUsage ? usageCount + " usage track(s) found" : "Ready to delete" }}</strong>',
            '        <span>{{ primaryCopy }}</span>',
            '      </div>',
            '    </div>',
            '    <dl class="fm-delete-meta">',
            '      <div><dt>Type</dt><dd>{{ item && item.type === "directory" ? "Media folder" : "Media file" }}</dd></div>',
            '      <div v-if="itemPath"><dt>Path</dt><dd>{{ itemPath }}</dd></div>',
            '      <div v-if="item && (item.media_id || item.folder_id)"><dt>Database ID</dt><dd>{{ item.media_id || item.folder_id }}</dd></div>',
            '    </dl>',
            '    <div class="fm-usage-box" :class="{\'has-usage\': hasUsage}">',
            '      <div class="fm-usage-title">',
            '        <i class="mdi" :class="store.deleteDialog.loadingUsage ? \'mdi-loading mdi-spin\' : (hasUsage ? \'mdi-link-variant\' : \'mdi-shield-check-outline\')"></i>',
            '        <span>{{ store.deleteDialog.loadingUsage ? "Checking usage..." : (hasUsage ? "Used in these places" : "No usage tracked") }}</span>',
            '      </div>',
            '      <ul v-if="hasUsage">',
            '        <li v-for="usageItem in usage.items" :key="usageItem.id">',
            '          <strong>{{ usageItem.label }}</strong>',
            '          <small>{{ usageItem.field_name }}<template v-if="usageItem.updated_at"> / {{ usageItem.updated_at }}</template></small>',
            '        </li>',
            '      </ul>',
            '      <small v-if="hasUsage && usage.items && usage.items.length < usageCount" class="fm-usage-more">{{ usageCount - usage.items.length }} more usage track(s) are not shown.</small>',
            '    </div>',
            '    <div class="fm-dialog-actions">',
            '      <button type="button" class="fm-light-btn" @click="close">Cancel</button>',
            '      <button v-if="!hasUsage" type="button" class="fm-danger-btn" :disabled="store.saving || store.deleteDialog.loadingUsage" @click="deleteItem"><i class="mdi mdi-delete-outline"></i><span>{{ store.saving ? "Deleting..." : "Delete" }}</span></button>',
            '      <button v-else type="button" class="fm-danger-btn" :disabled="store.saving || store.deleteDialog.loadingUsage || !store.canForceDelete" @click="forceDeleteItem"><i class="mdi mdi-delete-forever-outline"></i><span>{{ store.canForceDelete ? (store.saving ? "Force deleting..." : "Force delete") : "Force delete unavailable" }}</span></button>',
            '    </div>',
            '  </section>',
            '</div>'
        ].join('')
    };
})(window);
