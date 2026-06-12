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
        methods: {
            goHome: function () {
                this.store.load('');
            }
        },
        template: [
            '<aside class="fm-sidebar">',
            '  <div class="fm-brand">',
            '    <span><i class="mdi mdi-folder-image"></i></span>',
            '    <div><strong>Media Drive</strong><small>FTP workspace</small></div>',
            '  </div>',
            '  <button type="button" class="fm-sidebar-action is-primary" @click="store.setMode(\'upload\')">',
            '    <i class="mdi mdi-cloud-upload-outline"></i><span>Upload photo</span>',
            '  </button>',
            '  <nav class="fm-nav">',
            '    <button type="button" :class="{active: store.mode === \'browse\'}" @click="store.setMode(\'browse\')"><i class="mdi mdi-view-grid-outline"></i><span>My files</span></button>',
            '    <button type="button" @click="goHome"><i class="mdi mdi-home-outline"></i><span>Root folder</span></button>',
            '    <button type="button" :class="{active: store.mode === \'upload\'}" @click="store.setMode(\'upload\')"><i class="mdi mdi-image-edit-outline"></i><span>Photo editor</span></button>',
            '  </nav>',
            '  <div class="fm-storage-note">',
            '    <i class="mdi mdi-server-network"></i>',
            '    <span>Disk: ftp</span>',
            '  </div>',
            '</aside>'
        ].join('')
    };
})(window);
