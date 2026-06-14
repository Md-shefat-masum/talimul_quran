(function (window, document) {
    'use strict';

    if (!window.Vue || !window.Pinia) {
        throw new Error('Vue and Pinia must be loaded before file_manager/app.js');
    }

    var modalRoot = document.querySelector('file-manager');
    var pageRoot = document.querySelector('file-manager-page');

    if (!modalRoot && !pageRoot) {
        return;
    }

    var pinia = window.Pinia.createPinia();
    var store;

    if (modalRoot) {
        var modalApp = window.Vue.createApp({
            components: {
                FileManager: window.FileManagerComponents.FileManager
            },
            template: '<file-manager></file-manager>'
        });

        modalApp.use(pinia);
        modalApp.mount(modalRoot);
    }

    if (pageRoot) {
        var pageApp = window.Vue.createApp({
            components: {
                FileManagerPage: window.FileManagerComponents.FileManagerPage
            },
            template: '<file-manager-page></file-manager-page>'
        });

        pageApp.use(pinia);
        pageApp.mount(pageRoot);
    }

    store = window.FileManagerStores.useFileManagerStore();
    window.FileManagerBridge.bindStore(store);
    window.FileManagerBridge.bindTriggers();
})(window, document);
