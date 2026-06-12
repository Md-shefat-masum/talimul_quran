(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.FileGrid = {
        name: 'FmFileGrid',
        components: {
            FmItemActions: window.FileManagerComponents.ItemActions
        },
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore()
            };
        },
        methods: {
            isSelected: function (item) {
                return this.store.selected.some(function (selectedItem) {
                    return this.store.sameItem(selectedItem, item);
                }, this);
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
            },
            cardPreviewUrl: function (item) {
                return item.thumbnail_url || item.preview_url;
            },
            isDragging: function (item) {
                return this.store.dragDrop.item && this.store.sameItem(this.store.dragDrop.item, item);
            },
            isDropTarget: function (item) {
                return item.type === 'directory' &&
                    this.store.dragDrop.overKey === this.store.itemKey(item) &&
                    this.store.canDropOn(item);
            },
            isInvalidDropTarget: function (item) {
                return item.type === 'directory' &&
                    this.store.dragDrop.overKey === this.store.itemKey(item) &&
                    !this.store.canDropOn(item);
            },
            dragStart: function (event, item) {
                if (!this.store.canMove) {
                    if (event.preventDefault) {
                        event.preventDefault();
                    }

                    this.store.setPermissionError();
                    return;
                }

                this.store.startDragging(item);

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', item.path);
                }
            },
            dragEnter: function (event, item) {
                if (item.type !== 'directory') {
                    return;
                }

                event.preventDefault();
                this.store.setDragTarget(item);
            },
            dragOver: function (event, item) {
                if (item.type !== 'directory') {
                    return;
                }

                event.preventDefault();

                if (event.dataTransfer) {
                    event.dataTransfer.dropEffect = this.store.canDropOn(item) ? 'move' : 'none';
                }

                this.store.setDragTarget(item);
            },
            dropItem: function (event, item) {
                if (item.type !== 'directory') {
                    return;
                }

                event.preventDefault();
                this.store.prepareDropMove(item);
            }
        },
        template: [
            '<section class="fm-files">',
            '  <div v-if="store.loading" class="fm-state"><i class="mdi mdi-loading mdi-spin"></i><span>Loading media library...</span></div>',
            '  <div v-else-if="store.error" class="fm-state is-error"><i class="mdi mdi-alert-circle-outline"></i><strong>{{ store.error }}</strong><small>Check media database records and storage disk configuration.</small></div>',
            '  <div v-else-if="!store.filteredItems.length" class="fm-state"><i class="mdi mdi-folder-open-outline"></i><strong>No media in this folder</strong><small>Add a DB media folder or upload a photo. Storage paths stay stable after organization changes.</small></div>',
            '  <div v-else class="fm-file-results">',
            '  <div class="fm-result-meta"><span>{{ store.pagination.shown }} of {{ store.pagination.total }} item(s)</span><small v-if="store.query">Filtered by "{{ store.query }}"</small></div>',
            '  <div class="fm-file-collection" :class="\'is-\' + store.viewMode">',
            '    <article v-for="item in store.filteredItems" :key="store.itemKey(item)" class="fm-file-card" :class="{selected: isSelected(item), active: store.activeItem && store.sameItem(store.activeItem, item), folder: item.type === \'directory\', dragging: isDragging(item), \'drop-target\': isDropTarget(item), \'drop-invalid\': isInvalidDropTarget(item)}" tabindex="0" :draggable="store.canMove" @click="clickItem(item)" @dblclick="openItem(item)" @keydown.enter.prevent="openItem(item)" @dragstart="dragStart($event, item)" @dragend="store.clearDragging" @dragenter="dragEnter($event, item)" @dragover="dragOver($event, item)" @dragleave="store.setDragTarget(null)" @drop="dropItem($event, item)">',
            '      <span class="fm-thumb">',
            '        <img v-if="item.is_image && cardPreviewUrl(item)" :src="cardPreviewUrl(item)" :alt="item.name" loading="lazy" decoding="async">',
            '        <i v-else class="mdi" :class="iconClass(item)"></i>',
            '      </span>',
            '      <span class="fm-file-copy">',
            '        <strong>{{ item.name }}</strong>',
            '        <small>{{ item.type === "directory" ? "DB media folder" : (item.size_label || "Media file") }}</small>',
            '      </span>',
            '      <fm-item-actions :item="item"></fm-item-actions>',
            '      <span v-if="isDropTarget(item)" class="fm-drop-hint"><i class="mdi mdi-folder-move-outline"></i> Move here</span>',
            '      <span v-if="isInvalidDropTarget(item)" class="fm-drop-hint is-invalid"><i class="mdi mdi-alert-circle-outline"></i> Not allowed</span>',
            '      <span class="fm-selected-mark"><i class="mdi mdi-check"></i></span>',
            '    </article>',
            '  </div>',
            '  <div v-if="store.pagination.hasMore" class="fm-load-more">',
            '    <button type="button" class="fm-light-btn" :disabled="store.loadingMore" @click="store.loadMore()"><i class="mdi" :class="store.loadingMore ? \'mdi-loading mdi-spin\' : \'mdi-chevron-down\'"></i><span>{{ store.loadingMore ? "Loading..." : "Load more" }}</span></button>',
            '  </div>',
            '  </div>',
            '</section>'
        ].join('')
    };
})(window);
