<div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content user-form-modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <span class="badge user-form-mode-badge mb-2">Reusable Form</span>
                    <h5 class="modal-title" id="userFormModalLabel">Create User</h5>
                    <p class="text-muted small mb-0">The same form component is used for page and modal workflows.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body user-form-modal-body">
                @include('backend.pages.users.partials.form', [
                    'formId' => 'userModalForm',
                    'mode' => 'create',
                    'context' => 'modal',
                    'user' => null,
                    'submitText' => 'Save User',
                ])
            </div>
        </div>
    </div>
</div>
