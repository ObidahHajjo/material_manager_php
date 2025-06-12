<div class="container mt-5">
    <h2 class="mb-4 text-warning">Manage Materials</h2>

    <a href="/admin/materials/create" class="btn btn-primary mb-3"><i class="fas fa-plus me-2"></i>Add Material</a>

    <div class="card p-3 shadow-sm rounded-4">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materials as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['name']) ?></td>
                        <td><?= htmlspecialchars($m['category']) ?></td>
                        <td><?= $m['quantity'] ?></td>
                        <td><?= ucfirst($m['status']) ?></td>
                        <td>
                            <a href="/admin/materials/edit/<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="/admin/materials/delete/<?= $m['id'] ?>" method="POST" class="d-inline">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this material?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add margin for fixed-top -->
<div style="margin-top: 30rem;"></div>