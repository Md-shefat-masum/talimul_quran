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
                sourceDataUrl: '',
                editor: null,
                editorRefreshTimer: null,
                editorAvailable: Boolean(window.FileManagerFilerobot && window.FileManagerFilerobot.Editor),
                editedFile: null,
                presetKey: 'card',
                customWidth: 800,
                customHeight: 600,
                zoom: 1,
                offsetX: 0,
                offsetY: 0,
                outputName: '',
                conflictStrategy: 'error'
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
            },
            isFixedSize: function () {
                return Boolean(
                    this.selectedPreset.width ||
                    (this.store.opener.size && this.store.opener.size.width)
                );
            },
            editorLabel: function () {
                return this.editorAvailable ? 'Filerobot editor' : 'Canvas editor';
            }
        },
        watch: {
            presetKey: 'onOutputChange',
            customWidth: 'onOutputChange',
            customHeight: 'onOutputChange',
            zoom: 'renderCanvas',
            offsetX: 'renderCanvas',
            offsetY: 'renderCanvas',
            outputName: function () {
                this.store.uploadConflict = null;
            }
        },
        mounted: function () {
            if (this.store.opener.size && this.store.opener.size.width && this.store.opener.size.height) {
                this.presetKey = 'free';
                this.customWidth = this.store.opener.size.width;
                this.customHeight = this.store.opener.size.height;
            }
        },
        unmounted: function () {
            this.destroyEditor();

            if (this.imageUrl) {
                URL.revokeObjectURL(this.imageUrl);
            }
        },
        methods: {
            pickFile: function () {
                this.$refs.input.click();
            },
            readFileAsDataUrl: function (file) {
                return new Promise(function (resolve, reject) {
                    var reader = new FileReader();

                    reader.onload = function () {
                        resolve(reader.result);
                    };
                    reader.onerror = function () {
                        reject(new Error('Could not read this image.'));
                    };
                    reader.readAsDataURL(file);
                });
            },
            onFileChange: function (event) {
                var file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                var component = this;

                if (!file) {
                    return;
                }

                this.destroyEditor();
                this.file = file;
                this.editedFile = null;
                this.outputName = file.name.replace(/\.[^.]+$/, '');
                this.store.uploadConflict = null;
                this.store.uploadProgress = 0;

                if (this.imageUrl) {
                    URL.revokeObjectURL(this.imageUrl);
                }

                Promise.all([
                    window.FileManagerCanvasEditor.loadImage(file),
                    this.readFileAsDataUrl(file)
                ]).then(function (results) {
                    component.image = results[0].image;
                    component.imageUrl = results[0].url;
                    component.sourceDataUrl = results[1];
                    component.$nextTick(function () {
                        component.renderCanvas();
                        component.mountEditor();
                    });
                }).catch(function (error) {
                    component.store.error = error.message;
                });
            },
            onOutputChange: function () {
                this.renderCanvas();
                this.queueEditorRefresh();
            },
            queueEditorRefresh: function () {
                var component = this;

                if (!this.editorAvailable || !this.sourceDataUrl) {
                    return;
                }

                clearTimeout(this.editorRefreshTimer);
                this.editorRefreshTimer = setTimeout(function () {
                    component.mountEditor();
                }, 220);
            },
            getCropRatio: function () {
                if (!this.outputWidth || !this.outputHeight) {
                    return 'original';
                }

                return Number(this.outputWidth) / Number(this.outputHeight);
            },
            buildEditorConfig: function () {
                var vendor = window.FileManagerFilerobot;
                var ratio = this.getCropRatio();
                var fixedSize = this.isFixedSize;
                var targetPreset = {
                    titleKey: 'Target ' + this.targetLabel,
                    width: this.outputWidth,
                    height: this.outputHeight,
                    ratio: ratio,
                    disableManualResize: fixedSize
                };

                return {
                    source: this.sourceDataUrl,
                    defaultSavedImageName: this.outputName || 'photo',
                    defaultSavedImageType: 'jpeg',
                    defaultSavedImageQuality: 0.92,
                    closeAfterSave: false,
                    avoidChangesNotSavedAlertOnLeave: true,
                    showBackButton: false,
                    savingPixelRatio: 1,
                    previewPixelRatio: 1,
                    tabsIds: [
                        vendor.TABS.ADJUST,
                        vendor.TABS.FINETUNE,
                        vendor.TABS.FILTERS,
                        vendor.TABS.ANNOTATE,
                        vendor.TABS.RESIZE
                    ],
                    defaultTabId: vendor.TABS.ADJUST,
                    defaultToolId: vendor.TOOLS.CROP,
                    Crop: {
                        ratio: ratio,
                        minWidth: 1,
                        minHeight: 1,
                        autoResize: true,
                        presetsItems: [targetPreset]
                    },
                    onSave: this.applySavedImage
                };
            },
            mountEditor: function () {
                var container = this.$refs.filerobot;

                if (!this.editorAvailable || !this.sourceDataUrl || !container) {
                    return;
                }

                this.destroyEditor();
                container.innerHTML = '';
                this.editor = new window.FileManagerFilerobot.Editor(container, this.buildEditorConfig());
                this.editor.render();
            },
            destroyEditor: function () {
                clearTimeout(this.editorRefreshTimer);
                this.editorRefreshTimer = null;

                if (!this.editor) {
                    return;
                }

                try {
                    this.editor.terminate();
                } catch (error) {
                    window.console.warn('File manager editor cleanup failed.', error);
                }

                this.editor = null;
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
            applySavedImage: function (savedImageData) {
                var component = this;

                return this.savedImageToFile(savedImageData).then(function (file) {
                    component.editedFile = file;
                    return file;
                });
            },
            savedImageToFile: function (savedImageData) {
                var fullName = ((savedImageData && savedImageData.name) || this.outputName || 'photo') + '.jpg';

                if (savedImageData && savedImageData.fullName) {
                    fullName = savedImageData.fullName.replace(/\.(png|webp)$/i, '.jpg');
                }

                if (savedImageData && savedImageData.imageCanvas) {
                    return window.FileManagerCanvasEditor.canvasToFile(savedImageData.imageCanvas, fullName, 'image/jpeg');
                }

                if (savedImageData && savedImageData.imageBase64) {
                    return Promise.resolve(window.FileManagerCanvasEditor.dataUrlToFile(savedImageData.imageBase64, fullName));
                }

                return Promise.reject(new Error('Could not export the edited image.'));
            },
            captureEditorFile: function () {
                var result;
                var savedImageData;

                if (!this.editor) {
                    return Promise.resolve(this.editedFile);
                }

                result = this.editor.getCurrentImgData({
                    name: this.outputName || 'photo',
                    extension: 'jpeg',
                    quality: 0.92,
                    size: {
                        width: this.outputWidth,
                        height: this.outputHeight
                    }
                }, 1, false);

                savedImageData = result && result.imageData ? result.imageData : result;

                if (result && typeof result.hideLoadingSpinner === 'function') {
                    result.hideLoadingSpinner();
                }

                return this.savedImageToFile(savedImageData);
            },
            buildUploadFile: function () {
                if (this.editorAvailable && this.sourceDataUrl) {
                    return this.captureEditorFile().catch(function () {
                        return null;
                    }).then(function (file) {
                        if (file) {
                            return file;
                        }

                        throw new Error('Could not export the edited image.');
                    });
                }

                if (!this.image || !this.$refs.canvas) {
                    return Promise.reject(new Error('Please select a photo first.'));
                }

                return window.FileManagerCanvasEditor.canvasToFile(
                    this.$refs.canvas,
                    (this.outputName || 'photo') + '.jpg',
                    'image/jpeg'
                );
            },
            updateProgress: function (event) {
                if (!event || !event.total) {
                    this.store.uploadProgress = 0;
                    return;
                }

                this.store.uploadProgress = Math.min(99, Math.round((event.loaded / event.total) * 100));
            },
            uploadWithStrategy: function (strategy) {
                var component = this;

                if (!this.store.canUpload) {
                    this.store.setPermissionError();
                    return;
                }

                if (!this.image) {
                    this.pickFile();
                    return;
                }

                this.buildUploadFile().then(function (file) {
                    return component.store.uploadPhoto({
                        file: file,
                        path: component.store.path,
                        folderId: component.store.currentFolderId,
                        name: component.outputName,
                        preset: component.presetKey,
                        width: component.outputWidth,
                        height: component.outputHeight,
                        conflictStrategy: strategy || component.conflictStrategy,
                        onProgress: function (event) {
                            component.updateProgress(event);
                        }
                    });
                }).catch(function (error) {
                    if (!component.store.uploadConflict) {
                        component.store.error = error.message || 'Could not upload this photo.';
                    }
                });
            },
            upload: function () {
                this.uploadWithStrategy(this.conflictStrategy);
            },
            uploadSuggestedName: function () {
                if (this.store.uploadConflict && this.store.uploadConflict.suggested_name) {
                    this.outputName = this.store.uploadConflict.suggested_name;
                }

                this.uploadWithStrategy('rename');
            },
            replaceExisting: function () {
                this.uploadWithStrategy('replace');
            }
        },
        template: [
            '<section class="fm-upload-panel">',
            '  <div v-if="!store.canUpload" class="fm-state is-error"><i class="mdi mdi-lock-outline"></i><strong>Upload permission required</strong><small>You can still browse and select existing files.</small></div>',
            '  <template v-else>',
            '  <div class="fm-upload-copy">',
            '    <span class="fm-chip"><i class="mdi mdi-image-edit-outline"></i> Photo upload</span>',
            '    <h4>Prepare a CMS-ready image before saving.</h4>',
            '    <p>Crop, resize, annotate, tune color and save the final image into the selected media folder.</p>',
            '  </div>',
            '  <div class="fm-editor-layout">',
            '    <div class="fm-editor-stage" :class="{\'is-filerobot\': editorAvailable && image}" @click="!image && pickFile()">',
            '      <div v-if="editorAvailable && image" ref="filerobot" class="fm-filerobot-host"></div>',
            '      <canvas v-show="image && !editorAvailable" ref="canvas"></canvas>',
            '      <div v-if="!image" class="fm-dropzone">',
            '        <i class="mdi mdi-cloud-upload-outline"></i>',
            '        <strong>Select a photo</strong>',
            '        <span>JPEG, PNG, WebP up to 10MB</span>',
            '      </div>',
            '      <input ref="input" type="file" :accept="store.opener.accept || \'image/*\'" hidden @change="onFileChange">',
            '    </div>',
            '    <div class="fm-editor-controls">',
            '      <div class="fm-output-note"><i class="mdi mdi-creation-outline"></i><span>{{ editorLabel }}</span></div>',
            '      <label>Output name<input v-model="outputName" type="text" placeholder="photo-name"></label>',
            '      <label>Duplicate name<select v-model="conflictStrategy"><option value="error">Ask before saving</option><option value="rename">Auto rename copy</option><option value="replace">Replace existing</option></select></label>',
            '      <label>Preset<select v-model="presetKey"><option v-for="preset in presets" :key="preset.key" :value="preset.key">{{ preset.label }}</option></select></label>',
            '      <div class="fm-size-row">',
            '        <label>Width<input v-model.number="customWidth" type="number" min="1" :disabled="Boolean(selectedPreset.width || (store.opener.size && store.opener.size.width))"></label>',
            '        <label>Height<input v-model.number="customHeight" type="number" min="1" :disabled="Boolean(selectedPreset.height || (store.opener.size && store.opener.size.height))"></label>',
            '      </div>',
            '      <template v-if="!editorAvailable">',
            '        <label>Zoom <strong>{{ Number(zoom).toFixed(2) }}x</strong><input v-model.number="zoom" type="range" min="1" max="3" step="0.05"></label>',
            '        <label>Move X<input v-model.number="offsetX" type="range" min="-400" max="400" step="1"></label>',
            '        <label>Move Y<input v-model.number="offsetY" type="range" min="-400" max="400" step="1"></label>',
            '      </template>',
            '      <div class="fm-output-note"><i class="mdi mdi-crop"></i><span>Final image: {{ targetLabel }}</span></div>',
            '      <div v-if="store.uploadConflict" class="fm-conflict-box">',
            '        <strong><i class="mdi mdi-alert-circle-outline"></i> Name already exists</strong>',
            '        <span>{{ store.uploadConflict.name }} already exists in this folder.</span>',
            '        <div class="fm-conflict-actions">',
            '          <button type="button" class="fm-light-btn" @click="uploadSuggestedName"><i class="mdi mdi-content-copy"></i><span>Use {{ store.uploadConflict.suggested_file_name }}</span></button>',
            '          <button type="button" class="fm-danger-btn" @click="replaceExisting"><i class="mdi mdi-file-replace-outline"></i><span>Replace</span></button>',
            '        </div>',
            '      </div>',
            '      <div v-if="store.saving || store.uploadProgress > 0" class="fm-upload-progress" :aria-valuenow="store.uploadProgress" aria-valuemin="0" aria-valuemax="100" role="progressbar">',
            '        <span :style="{width: store.uploadProgress + \'%\'}"></span>',
            '        <strong>{{ store.uploadProgress }}%</strong>',
            '      </div>',
            '      <button type="button" class="fm-primary-btn w-100" :disabled="store.saving" @click="upload"><i class="mdi mdi-cloud-upload-outline"></i><span>{{ store.saving ? "Uploading..." : "Upload edited photo" }}</span></button>',
            '    </div>',
            '  </div>',
            '  </template>',
            '</section>'
        ].join('')
    };
})(window);
