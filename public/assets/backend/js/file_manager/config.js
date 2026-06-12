(function (window) {
    'use strict';

    window.FileManagerConfig = {
        endpoints: {
            list: '/dashboard/file-manager',
            uploadPhoto: '/dashboard/file-manager/photo',
            folder: '/dashboard/file-manager/folder',
            destroy: '/dashboard/file-manager/item'
        },
        presets: [
            {key: 'free', label: 'Free crop', width: null, height: null},
            {key: 'avatar', label: 'Avatar 512 x 512', width: 512, height: 512},
            {key: 'card', label: 'Card 800 x 600', width: 800, height: 600},
            {key: 'banner', label: 'Banner 1600 x 600', width: 1600, height: 600},
            {key: 'wide', label: 'Wide 1920 x 1080', width: 1920, height: 1080},
            {key: 'thumb', label: 'Thumb 320 x 240', width: 320, height: 240}
        ],
        defaults: {
            accept: 'image/*',
            multiple: false,
            path: ''
        }
    };
})(window);
