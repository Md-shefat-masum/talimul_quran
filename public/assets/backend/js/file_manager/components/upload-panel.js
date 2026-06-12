(function (window) {
    'use strict';

    window.FileManagerComponents = window.FileManagerComponents || {};

    window.FileManagerComponents.UploadPanel = {
        name: 'FmUploadPanel',
        data: function () {
            return {
                store: window.FileManagerStores.useFileManagerStore(),
                presets: window.FileManagerConfig.presets,
                file: null,
                image: null,
                imageUrl: '',
                presetKey: 'card',
                customWidth: 800,
                customHeight: 600,
                zoom: 1,
                offsetX: 0,
                offsetY: 0,
                outputName: ''
            };
        },
        computed: {
            selectedPreset: function () {
                var preset = this.presets.find(function (item) {
                    return item.key === this.presetKey;
                }, this);

                return preset || this.presets[0];
            },
            outputWidth: function () {
                if (this.store.opener.size && this.store.opener.size.width) {
                    return Number(this.store.opener.size.width);
                }

                return this.selectedPreset.width || Number(this.customWidth) || (this.image ? this.image.width : 800);
            },
            outputHeight: function () {
                if (this.store.opener.size && this.store.opener.size.height) {
                    return Number(this.store.opener.size.height);
                }

                return this.selectedPreset.height || Number(this.customHeight) || (this.image ? this.image.height : 600);
            },
            targetLabel: function () {
                return this.outputWidth + ' x ' + this.outputHeight;
            }
        },
        watch: {
            presetKey: 'renderCanvas',
            customWidth: 'renderCanvas',
            customHeight: 'renderCanvas',
            zoom: 'renderCanvas',
            offsetX: 'renderCanvas',
            offsetY: 'renderCanvas'
        },
        mounted: function () {
            if (this.store.opener.size && this.store.opener.size.width && this.store.opener.size.height) {
                this.presetKey = 'free';
                this.customWidth = this.store.opener.size.width;
                this.customHeight = this.store.opener.size.height;
            }
        },
        methods: {
            pickFile: function () {
                this.$refs.input.click();
            },
            onFileChange: function (event) {
                var file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                var component = this;

                if (!file) {
                    return;
                }

                this.file = file;
                this.outputName = file.name.replace(/\.[^.]+$/, '');

                if (this.imageUrl) {
                    URL.revokeObjectURL(this.imageUrl);
                }

                window.FileManagerCanvasEditor.loadImage(file).then(function (result) {
                    component.image = result.image;
                    component.imageUrl = result.url;
                    component.$nextTick(component.renderCanvas);
                }).catch(function (error) {
                    component.store.error = error.message;
                });
            },
            renderCanvas: function () {
                if (!this.image || !this.$refs.canvas) {
                    return;
                }

                window.FileManagerCanvasEditor.renderToCanvas({
                    canvas: this.$refs.canvas,
                    image: this.image,
                    width: this.outputWidth,
                    height: this.outputHeight,
                    zoom: this.zoom,
                    offsetX: this.offsetX,
                    offsetY: this.offsetY
                });
            },
            upload: function () {
                var component = this;

                if (!this.image || !this.$refs.canvas) {
                    this.pickFile();
                    return;
                }

                window.FileManagerCanvasEditor.canvasToFile(
                    this.$refs.canvas,
                    (this.outputName || 'photo') + '.jpg',
                    'image/jpeg'
                ).then(function (file) {
                    return component.store.uploadPhoto({
                        file: file,
                        path: component.store.path,
                        name: component.outputName,
                        preset: component.presetKey,
                        width: component.outputWidth,
                        height: component.outputHeight
                    });
                });
            }
        },
        template: [
            '<section class="fm-upload-panel">',
            '  <div class="fm-upload-copy">',
            '    <span class="fm-chip"><i class="mdi mdi-image-edit-outline"></i> Photo upload</span>',
            '    <h4>Prepare a CMS-ready image before it reaches FTP.</h4>',
            '    <p>Choose a preset or custom target. The canvas keeps the final upload at the exact size, so forms can request avatar, card, banner, or custom dimensions.</p>',
            '  </div>',
            '  <div class="fm-editor-layout">',
            '    <div class="fm-editor-stage" @click="!image && pickFile()">',
            '      <canvas v-show="image" ref="canvas"></canvas>',
            '      <div v-if="!image" class="fm-dropzone">',
            '        <i class="mdi mdi-cloud-upload-outline"></i>',
            '        <strong>Select a photo</strong>',
            '        <span>JPEG, PNG, WebP up to 10MB</span>',
            '      </div>',
            '      <input ref="input" type="file" accept="image/*" hidden @change="onFileChange">',
            '    </div>',
            '    <div class="fm-editor-controls">',
            '      <label>Output name<input v-model="outputName" type="text" placeholder="photo-name"></label>',
            '      <label>Preset<select v-model="presetKey"><option v-for="preset in presets" :key="preset.key" :value="preset.key">{{ preset.label }}</option></select></label>',
            '      <div class="fm-size-row">',
            '        <label>Width<input v-model.number="customWidth" type="number" min="1" :disabled="Boolean(selectedPreset.width || (store.opener.size && store.opener.size.width))"></label>',
            '        <label>Height<input v-model.number="customHeight" type="number" min="1" :disabled="Boolean(selectedPreset.height || (store.opener.size && store.opener.size.height))"></label>',
            '      </div>',
            '      <label>Zoom <strong>{{ Number(zoom).toFixed(2) }}x</strong><input v-model.number="zoom" type="range" min="1" max="3" step="0.05"></label>',
            '      <label>Move X<input v-model.number="offsetX" type="range" min="-400" max="400" step="1"></label>',
            '      <label>Move Y<input v-model.number="offsetY" type="range" min="-400" max="400" step="1"></label>',
            '      <div class="fm-output-note"><i class="mdi mdi-crop"></i><span>Final canvas: {{ targetLabel }}</span></div>',
            '      <button type="button" class="fm-primary-btn w-100" :disabled="store.saving" @click="upload"><i class="mdi mdi-cloud-upload-outline"></i><span>{{ store.saving ? "Uploading..." : "Upload edited photo" }}</span></button>',
            '    </div>',
            '  </div>',
            '</section>'
        ].join('')
    };
})(window);
