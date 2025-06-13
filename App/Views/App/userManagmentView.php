<div class="container mt-5">
    <h2 class="mb-4 text-info">User Management</h2>

    <a href="/admin/users/create" class="btn btn-primary mb-3">
        <i class="fas fa-user-plus me-2"></i>Add User
    </a>

    <div class="card p-4 shadow-sm rounded-4">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
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
                            <span class="badge bg-<?= $user->getRole() == 'admin' ? 'success' : 'secondary' ?>">
                                <?= $user->getRole() ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="/admin/users/delete/<?= $user['id'] ?>" method="POST" class="d-inline">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (isset($editUser)): ?>
    <div class="container mt-5">
        <h2 class="mb-4 text-warning">Edit User</h2>
        <form action="/admin/users/update/<?= $editUser['id'] ?>" method="POST" class="card p-4 shadow-sm rounded-4">
        <?php else: ?>
            <div class="container mt-5">
                <h2 class="mb-4 text-primary">Add User</h2>
                <form action="/admin/users/store" method="POST" class="card p-4 shadow-sm rounded-4">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required value="<?= $editUser['username'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required value="<?= $editUser['email'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="admin" <?= (isset($editUser) && $editUser['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= (isset($editUser) && $editUser['role'] === 'user') ? 'selected' : '' ?>>User</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" <?= isset($editUser) ? '' : 'required' ?>>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i><?= isset($editUser) ? 'Update' : 'Create' ?> User
                </button>
                </form>
            </div>
            <!-- Add margin for fixed-top -->
            <div style="margin-top: 30rem;"></div>