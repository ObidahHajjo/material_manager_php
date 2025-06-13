<div class="container mt-5">
    <h2 class="mb-4 text-info">User Management</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal()">
        <i class="fas fa-plus me-2"></i>Add User
    </button>


    <div class="card p-4 shadow-sm rounded-4">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->getId() ?></td>
                        <td><?= htmlspecialchars($user->getUserName()) ?></td>
                        <td><?= htmlspecialchars($user->getEmail()) ?></td>
                        <td><?= ucfirst($user->getRole()) ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#userModal"
                                onclick='openUserModal(<?= json_encode([
                                                            "id" => $user->getId(),
                                                            "name" => $user->getUsername(),
                                                            "email" => $user->getEmail(),
                                                            "role" => $user->getRole(),
                                                        ]) ?>)'>
                                Edit
                            </a>
                            <form method="POST" class="d-inline">
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user->getId() ?>); return false;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- User modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <form id="userForm" method="POST">
            <input type="hidden" name="id" id="user-id">
            <div class="modal-content rounded-4 mx-auto">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert d-none" role="alert" id="errorMessage"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="user-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="user-role" class="form-label">Role</label>
                        <select name="role" id="user-role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="submitModal">Save User</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelModal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Add margin for fixed-top -->
<div style="margin-top: 32.6rem;"></div>


<script>
    const $form = $('#userForm');
    const $modalLabel = $('#userModalLabel');
    const $submit = $('#submitModal');
    const $cancel = $('#cancelModal');

    function openUserModal(data = null) {

        // Reset form
        $form.trigger('reset');
        $form.attr('action', '/admin/users/store');
        $submit.text('Create');
        $modalLabel.text('Add User');
        $('#user-id').val('');

        if (data) {
            $modalLabel.text('Edit Material');
            $form.attr('action', '/admin/users/update/' + data.id);
            $submit.text('Update');
            $submit.removeClass('btn-success');
            $submit.addClass('btn-warning');
            $('#user-id').val(data.id);
            $('#username').val(data.name);
            $('#user-email').val(data.email);
            $('#user-role').val(data.role);
        }
    }

    $form.on('submit', function(e) {
        e.preventDefault();
        const $action = $form.attr('action');
        const $isUpdate = $action.includes('/update/');
        const $name = $('#username').val();
        const $role = $('#user-role').val();
        const $email = $('#user-email').val();
        if ($isUpdate) {
            const $id = $('#user-id').val();
            const data = {
                id: $id,
                username: $name,
                email: $email,
                role: $role
            };
            if (!verifiyFields(data)) return;
            $.ajax({
                contentType: 'application/json',
                url: `<?= base_url('admin/users/update/') ?>${$id}`,
                type: 'post',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function(response) {
                    showMessage("User updated successfully.", false);
                    $submit.attr('disabled', true);
                    $cancel.attr('disabled', true);
                    setTimeout(function() {
                        $('#userModal').modal('hide');
                        location.reload();
                    }, 3000);
                },
                error: function(xhr, error) {
                    let errorText = 'An error occurred.';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json && json.message) {
                            errorText = json.message;
                        }
                    } catch (e) {
                        errorText = xhr.responseText || error.message || 'An error occurred.';
                    }

                    showMessage(errorText, true);
                }

            });
            return;
        }

        const data = {
            username: $name,
            email: $email,
            role: $role
        };

        if (!verifiyFields(data)) return;
        $.ajax({
            contentType: 'application/json',
            url: '<?= base_url('admin/users/create') ?>',
            type: 'post',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function(response) {
                showMessage("User saved successfully.", false);
                $submit.attr('disabled', true);
                $cancel.attr('disabled', true);
                setTimeout(function() {
                    $('#userModal').modal('hide');
                    location.reload();
                }, 3000);
            },
            error: function(xhr, error) {
                let errorText = 'An error occurred.';
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json && json.message) {
                        errorText = json.message;
                    }
                } catch (e) {
                    errorText = xhr.responseText || error.message || 'An error occurred.';
                }

                showMessage(errorText, true);
            }

        });


    });

    function verifiyFields(fields) {
        const username = $('#username').val();
        const email = $('#user-email').val();
        const role = $('#user-role').val();

        let isValid = true;
        let message = '';

        if (!username) {
            isValid = false;
            message = 'Username is required.';
        } else if (!email) {
            isValid = false;
            message = 'Email is required.';
        } else if (!role) {
            isValid = false;
            message = 'Role is required.';
        }

        if (!isValid) {
            showMessage(message, true);
        }

        return isValid;
    }

    function showMessage($message, $isError) {
        const $messageLabel = $('#errorMessage');

        if (typeof $message !== 'string') return;
        if (typeof $isError !== 'boolean') return;

        $messageLabel
            .removeClass('d-none alert-success alert-danger')
            .addClass($isError ? 'alert-danger' : 'alert-success')
            .text($message)
            .addClass('show');

        setTimeout(() => {
            $messageLabel.removeClass('show');
            setTimeout(() => {
                $messageLabel.addClass('d-none');
            }, 300);
        }, 5000);
    }
</script>

<script>
    function deleteUser(id) {
        if (!confirm("Delete this user?")) return;
        $.ajax({
            type: 'POST',
            url: `<?= base_url('admin/users/delete/') ?>${id}`,
            success: function(response) {
                alert("User deleted successfully!");
                setTimeout(() => window.location.reload(), 1000);
            },
            error: function(xhr) {
                alert(xhr.responseText || "Failed to delete user.");
            }
        });
    }
</script>