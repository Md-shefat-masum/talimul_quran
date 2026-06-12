(function (window) {
    'use strict';

    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var image = new Image();

            image.onload = function () {
                resolve({image: image, url: url});
            };
            image.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not read this image.'));
            };
            image.src = url;
        });
    }

    function renderToCanvas(options) {
        var canvas = options.canvas || document.createElement('canvas');
        var image = options.image;
        var width = options.width || image.naturalWidth || image.width;
        var height = options.height || image.naturalHeight || image.height;
        var zoom = Number(options.zoom || 1);
        var offsetX = Number(options.offsetX || 0);
        var offsetY = Number(options.offsetY || 0);
        var context = canvas.getContext('2d');
        var baseScale = Math.max(width / image.width, height / image.height);
        var scale = baseScale * zoom;
        var drawWidth = image.width * scale;
        var drawHeight = image.height * scale;
        var x = (width - drawWidth) / 2 + offsetX;
        var y = (height - drawHeight) / 2 + offsetY;

        canvas.width = width;
        canvas.height = height;
        context.clearRect(0, 0, width, height);
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, width, height);
        context.drawImage(image, x, y, drawWidth, drawHeight);

        return canvas;
    }

    function canvasToFile(canvas, fileName, type) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) {
                resolve(new File([blob], fileName, {type: type || 'image/jpeg'}));
            }, type || 'image/jpeg', 0.92);
        });
    }

    function dataUrlToFile(dataUrl, fileName) {
        var parts = dataUrl.split(',');
        var meta = parts[0].match(/:(.*?);/);
        var mime = meta ? meta[1] : 'image/jpeg';
        var binary = atob(parts[1]);
        var length = binary.length;
        var bytes = new Uint8Array(length);

        while (length--) {
            bytes[length] = binary.charCodeAt(length);
        }

        return new File([bytes], fileName, {type: mime});
    }

    window.FileManagerCanvasEditor = {
        loadImage: loadImage,
        renderToCanvas: renderToCanvas,
        canvasToFile: canvasToFile,
        dataUrlToFile: dataUrlToFile
    };
})(window);
