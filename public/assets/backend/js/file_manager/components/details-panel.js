(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.DetailsPanel = {
        name: 'FmDetailsPanel',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        computed: {
            item: function () {
                return this.store.activeItem || this.store.selected[0] || null;
            }
        },
        methods: {
            removeItem: function () {
                if (!this.item) {
                    return;
                }

                if (window.confirm('Delete "' + this.item.name + '"?')) {
                    this.store.destroy(this.item);
                }
            }
        },
        template: [
            '<aside class="fm-details">',
            '  <template v-if="item">',
            '    <div class="fm-details-preview">',
            '      <img v-if="item.is_image && item.preview_url" :src="item.preview_url" :alt="item.name">',
            '      <i v-else class="mdi" :class="item.type === \'directory\' ? \'mdi-folder\' : \'mdi-file-outline\'"></i>',
            '    </div>',
            '    <h4>{{ item.name }}</h4>',
            '    <dl>',
            '      <div><dt>Type</dt><dd>{{ item.type }}</dd></div>',
            '      <div><dt>Size</dt><dd>{{ item.size_label || "Folder" }}</dd></div>',
            '      <div><dt>Path</dt><dd>{{ item.path }}</dd></div>',
            '      <div><dt>Modified</dt><dd>{{ item.modified_at || "Unknown" }}</dd></div>',
            '    </dl>',
            '    <button v-if="item.type === \'file\'" type="button" class="fm-primary-btn w-100" @click="store.toggleItem(item)"><i class="mdi mdi-check-circle-outline"></i><span>Select file</span></button>',
            '    <button type="button" class="fm-danger-btn w-100" @click="removeItem"><i class="mdi mdi-delete-outline"></i><span>Delete</span></button>',
            '  </template>',
            '  <div v-else class="fm-details-empty">',
            '    <i class="mdi mdi-information-outline"></i>',
            '    <strong>Details</strong>',
            '    <span>Select a file to inspect path, size, and preview.</span>',
            '  </div>',
            '</aside>'
        ].join('')
    };
})(window);
