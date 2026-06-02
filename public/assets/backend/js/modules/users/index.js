(function (window, document, $) {
    'use strict';

    var page = document.getElementById('userManagementPage');
    if (!page) {
        return;
    }

    var modalElement = document.getElementById('userFormModal');
    var modalForm = document.getElementById('userModalForm');
    var userTypeFilter = document.getElementById('filterUserType');
    var statusFilter = document.getElementById('filterUserStatus');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function replaceUserId(urlTemplate, userId) {
        return urlTemplate.replace('__USER_ID__', String(userId));
    }

    function updateSummary(summary) {
        Object.keys(summary || {}).forEach(function (key) {
            var element = page.querySelector('[data-summary="' + key + '"]');
            if (element) {
                element.textContent = summary[key];
            }
        });
    }

    window.initAxiosSelect2(userTypeFilter, {
        url: page.dataset.userTypeOptionsUrl,
        placeholder: userTypeFilter.dataset.placeholder,
        allowClear: true
    });

    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 350,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[6, 'desc']],
        ajax: function (requestData, callback) {
            requestData.filters = {
                status: statusFilter.value,
                user_type_id: userTypeFilter.value || ''
            };

            window.appAxios.get(page.dataset.dataUrl, {
                params: requestData
            }).then(function (response) {
                updateSummary(response.data.summary || {});
                callback(response.data);
            }).catch(function (error) {
                window.appAlert.errorFromException(error);
                callback({
                    draw: requestData.draw,
                    recordsTotal: 0,
                    recordsFiltered: 0,
                    data: []
                });
            });
        },
        columns: [
            {data: 'serial', name: 'serial', orderable: false, searchable: false},
            {
                data: 'name',
                name: 'name',
                render: function (data) {
                    var name = escapeHtml(data);
                    var initial = name ? name.charAt(0).toUpperCase() : '?';

                    return '<div class="d-flex align-items-center gap-2">' +
                        '<span class="user-table-avatar">' + initial + '</span>' +
                        '<span class="fw-semibold text-dark">' + name + '</span>' +
                        '</div>';
                }
            },
            {data: 'email', name: 'email', render: escapeHtml},
            {data: 'phone', name: 'phone', render: escapeHtml},
            {data: 'user_type', name: 'user_type', orderable: false, render: escapeHtml},
            {
                data: 'status_label',
                name: 'status',
                render: function (data, type, row) {
                    var cssClass = row.status === 1 ? 'user-status-badge--active' : 'user-status-badge--inactive';
                    return '<span class="user-status-badge ' + cssClass + '">' + escapeHtml(data) + '</span>';
                }
            },
            {data: 'created_at', name: 'created_at', render: escapeHtml},
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function (userId, type, row) {
                    var editUrl = replaceUserId(page.dataset.editUrlTemplate, userId);
                    var safeName = escapeHtml(row.name);

                    return '<div class="btn-group btn-group-sm user-action-group" role="group" aria-label="User actions">' +
                        '<button type="button" class="btn btn-outline-primary js-edit-user-modal" data-user-id="' + userId + '" title="Quick edit">' +
                        '<i class="mdi mdi-pencil-outline"></i></button>' +
                        '<a class="btn btn-outline-secondary" href="' + editUrl + '" title="Open edit page">' +
                        '<i class="mdi mdi-open-in-new"></i></a>' +
                        '<button type="button" class="btn btn-outline-danger js-delete-user" data-user-id="' + userId + '" data-user-name="' + safeName + '" title="Delete user">' +
                        '<i class="mdi mdi-delete-outline"></i></button>' +
                        '</div>';
                }
            }
        ],
        language: {
            emptyTable: 'No users found.',
            processing: 'Loading users...',
            search: 'Search users:',
            lengthMenu: 'Show _MENU_ users'
        }
    });

    function buildColumnMenu() {
        var menu = document.getElementById('userColumnsMenu');
        var labels = ['#', 'User', 'Email', 'Phone', 'User Type', 'Status', 'Created', 'Actions'];

        labels.forEach(function (label, index) {
            var wrapper = document.createElement('label');
            wrapper.className = 'dropdown-item d-flex align-items-center gap-2 px-0';
            wrapper.innerHTML = '<input type="checkbox" class="form-check-input m-0" checked data-column-index="' + index + '">' +
                '<span>' + escapeHtml(label) + '</span>';
            menu.appendChild(wrapper);
        });

        menu.addEventListener('change', function (event) {
            var checkbox = event.target.closest('[data-column-index]');
            if (!checkbox) {
                return;
            }

            table.column(Number(checkbox.dataset.columnIndex)).visible(checkbox.checked);
        });
    }

    function reloadTable(resetPage) {
        table.ajax.reload(null, Boolean(resetPage));
    }

    function openCreateModal() {
        window.UserFormManager.reset(modalForm);
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }

    function openEditModal(userId) {
        window.appAxios.get(replaceUserId(page.dataset.showUrlTemplate, userId))
            .then(function (response) {
                window.UserFormManager.populate(modalForm, response.data.data);
                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            })
            .catch(function (error) {
                window.appAlert.errorFromException(error);
            });
    }

    function deleteUser(userId, name) {
        window.appAlert.confirmDelete(name).then(function (confirmed) {
            if (!confirmed) {
                return;
            }

            window.appAxios.delete(replaceUserId(page.dataset.deleteUrlTemplate, userId))
                .then(function (response) {
                    window.appAlert.success(response.data.message || 'User deleted successfully.');
                    reloadTable(false);
                })
                .catch(function (error) {
                    window.appAlert.errorFromException(error);
                });
        });
    }

    function updateExportLink() {
        var exportLink = document.getElementById('exportUsersCsv');
        var params = new URLSearchParams({
            search: table.search(),
            status: statusFilter.value,
            user_type_id: userTypeFilter.value || ''
        });

        exportLink.href = page.dataset.exportUrl + '?' + params.toString();
    }

    document.getElementById('openCreateUserModal').addEventListener('click', openCreateModal);
    document.getElementById('refreshUsersTable').addEventListener('click', function () {
        reloadTable(false);
    });
    document.getElementById('clearUsersFilters').addEventListener('click', function () {
        statusFilter.value = '';
        $(userTypeFilter).val(null).trigger('change');
        table.search('');
        reloadTable(true);
    });
    document.getElementById('exportUsersCsv').addEventListener('click', updateExportLink);

    $(userTypeFilter).on('change', function () {
        reloadTable(true);
    });
    statusFilter.addEventListener('change', function () {
        reloadTable(true);
    });

    $('#usersTable').on('click', '.js-edit-user-modal', function () {
        openEditModal(this.dataset.userId);
    });
    $('#usersTable').on('click', '.js-delete-user', function () {
        deleteUser(this.dataset.userId, this.dataset.userName);
    });

    document.addEventListener('user:changed', function () {
        reloadTable(false);
    });

    buildColumnMenu();
})(window, document, window.jQuery);
