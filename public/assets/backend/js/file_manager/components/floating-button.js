(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.FloatingButton = {
        name: 'FmFloatingButton',
        props: {
            open: {type: Boolean, default: false}
        },
        template: [
            '<button type="button" class="fm-launcher" :class="{\'is-open\': open}" @click="$emit(\'open\')" aria-label="Open file manager">',
            '  <i class="mdi" :class="open ? \'mdi-close\' : \'mdi-folder-image\'"></i>',
            '</button>'
        ].join('')
    };
})(window);
