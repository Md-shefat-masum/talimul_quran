(function (window, document) {
    'use strict';

    if (!window.Vue || !window.Pinia) {
        throw new Error('Vue and Pinia must be loaded before file_manager/app.js');
    }

    var root = document.querySelector('file-manager');

    if (!root) {
        return;
    }

    var pinia = window.Pinia.createPinia();
    var app = window.Vue.createApp({
        components: {
            FileManager: window.FileManagerComponents.FileManager
        },
        template: '<file-manager></file-manager>'
    });

    app.use(pinia);
    app.mount(root);

    window.FileManagerBridge.bindStore(window.FileManagerStores.useFileManagerStore());
    window.FileManagerBridge.bindTriggers();
})(window, document);
