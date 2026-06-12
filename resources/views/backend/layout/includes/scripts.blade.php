<script src="{{ asset('assets/backend/js/vendor.js') }}"></script>
<script src="{{ asset('assets/backend/js/app.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/datatables/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/datatables/js/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/backend/js/common/axios-instance.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/common/alert-helper.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/common/form-error-helper.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/common/select2-axios.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/vendor/vue.global.prod.js') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/vendor/vue-demi.iife.js') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/vendor/pinia.iife.prod.js') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/vendor/filerobot-image-editor.bundle.js') }}"></script>
<script>
    window.FileManagerPermissions = @json(app(\App\Services\FileManager\FileManagerPermissionService::class)->permissions(request()));
</script>
<script src="{{ asset('assets/backend/js/file_manager/config.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/api/file-manager-api.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/utils/canvas-editor.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/store/file-manager-store.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/floating-button.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/sidebar.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/toolbar.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/item-actions.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/move-dialog.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/file-grid.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/details-panel.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/upload-panel.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/components/modal-shell.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/bridge.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/picker.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
<script src="{{ asset('assets/backend/js/file_manager/app.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
