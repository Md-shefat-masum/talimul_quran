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
    var perPageSelect = document.getElementById('usersPerPage');
    var quickSearchInput = document.getElementById('usersQuickSearch');
    var quickSearchTimer = null;

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function replaceUserId(urlTemplate, userId) {
        return urlTemplate.replace('__USER_ID__', String(userId));
    }

    function buildUserDetailsHtml(user) {
        var statusLabel = Number(user.status) === 1 ? 'Active' : 'Inactive';
        var userType = user.user_type_text || 'Not assigned';
        var phone = user.phone || 'Not provided';
        var avatar = user.avatar_url || 'Not selected';

        return '<div class="user-detail-list">' +
            '<div><span>Name</span><strong>' + escapeHtml(user.name) + '</strong></div>' +
            '<div><span>Email</span><strong>' + escapeHtml(user.email) + '</strong></div>' +
            '<div><span>Phone</span><strong>' + escapeHtml(phone) + '</strong></div>' +
            '<div><span>Avatar</span><strong>' + escapeHtml(avatar) + '</strong></div>' +
            '<div><span>User Type</span><strong>' + escapeHtml(userType) + '</strong></div>' +
            '<div><span>Status</span><strong>' + escapeHtml(statusLabel) + '</strong></div>' +
            '</div>';
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
        dom: 'rt<"user-table-footer"ip>',
        order: [[6, 'desc']],
        columnDefs: [
            {responsivePriority: 1, targets: 7},
            {responsivePriority: 2, targets: 1},
            {responsivePriority: 3, targets: 5},
            {responsivePriority: 4, targets: 2},
            {responsivePriority: 5, targets: 6},
            {responsivePriority: 20, targets: 4},
            {responsivePriority: 30, targets: 3}
        ],
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
                render: function (data, type, row) {
                    var name = escapeHtml(data);
                    var initial = name ? name.charAt(0).toUpperCase() : '?';
                    var avatar = row.avatar_url
                        ? '<img src="' + escapeHtml(row.avatar_url) + '" alt="' + name + '">'
                        : initial;

                    return '<div class="d-flex align-items-center gap-2">' +
                        '<span class="user-table-avatar">' + avatar + '</span>' +
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
                className: 'text-end all',
                render: function (userId, type, row) {
                    var editUrl = replaceUserId(page.dataset.editUrlTemplate, userId);
                    var safeName = escapeHtml(row.name);
                    var exportUrl = page.dataset.exportUrl + '?search=' + encodeURIComponent(row.email || row.name || '');

                    return '<div class="user-row-actions" aria-label="User actions for ' + safeName + '">' +
                        '<button type="button" class="user-action-btn user-action-btn--view js-show-user-detail" data-user-id="' + userId + '">' +
                        '<i class="mdi mdi-eye-outline"></i></button>' +
                        '<button type="button" class="user-action-btn user-action-btn--edit js-edit-user-modal" data-user-id="' + userId + '">' +
                        '<i class="mdi mdi-pencil-outline"></i></button>' +
                        '<button type="button" class="user-action-btn user-action-btn--delete js-delete-user" data-user-id="' + userId + '" data-user-name="' + safeName + '">' +
                        '<i class="mdi mdi-delete-outline"></i></button>' +
                        '<div class="user-more-action">' +
                        '<button type="button" class="user-action-btn user-action-btn--more" aria-haspopup="true" aria-expanded="false">' +
                        '<i class="mdi mdi-format-align-justify"></i><span>More</span></button>' +
                        '<div class="user-action-menu">' +
                        '<ul>' +
                        '<li><button type="button" class="js-show-user-detail" data-user-id="' + userId + '"><i class="mdi mdi-eye-outline"></i><span>View details</span></button></li>' +
                        '<li><button type="button" class="js-edit-user-modal" data-user-id="' + userId + '"><i class="mdi mdi-pencil-outline"></i><span>Quick edit</span></button></li>' +
                        '<li><a href="' + editUrl + '"><i class="mdi mdi-open-in-new"></i><span>Open edit page</span></a></li>' +
                        '<li><a href="' + exportUrl + '"><i class="mdi mdi-file-delimited-outline"></i><span>Export this user</span></a></li>' +
                        '<li><button type="button" class="js-delete-user user-action-menu__danger" data-user-id="' + userId + '" data-user-name="' + safeName + '"><i class="mdi mdi-delete-outline"></i><span>Delete user</span></button></li>' +
                        '</ul>' +
                        '</div>' +
                        '</div>' +
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

    function showUserDetails(userId) {
        window.appAxios.get(replaceUserId(page.dataset.showUrlTemplate, userId))
            .then(function (response) {
                var user = response.data.data || {};

                if (typeof window.Swal === 'undefined') {
                    window.alert([user.name, user.email, user.phone].filter(Boolean).join('\n'));
                    return;
                }

                window.Swal.fire({
                    title: 'User Details',
                    html: buildUserDetailsHtml(user),
                    width: 520,
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#08766f',
                    customClass: {
                        popup: 'user-detail-dialog'
                    }
                });
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
        quickSearchInput.value = '';
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
    perPageSelect.addEventListener('change', function () {
        table.page.len(Number(this.value)).draw();
    });
    quickSearchInput.addEventListener('input', function () {
        window.clearTimeout(quickSearchTimer);
        quickSearchTimer = window.setTimeout(function () {
            table.search(quickSearchInput.value).draw();
        }, 250);
    });

    $('#usersTable').on('click', '.js-edit-user-modal', function () {
        openEditModal(this.dataset.userId);
    });
    $('#usersTable').on('click', '.js-show-user-detail', function () {
        showUserDetails(this.dataset.userId);
    });
    $('#usersTable').on('click', '.js-delete-user', function () {
        deleteUser(this.dataset.userId, this.dataset.userName);
    });

    document.addEventListener('user:changed', function () {
        reloadTable(false);
    });

    buildColumnMenu();
})(window, document, window.jQuery);
