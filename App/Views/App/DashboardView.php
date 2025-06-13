<div class="container mt-5">
    <h2 class="mb-4 text-white">Welcome, <?= htmlspecialchars(ucfirst($user->getUserName())) ?>!</h2>

    <div class="card p-4 shadow-sm rounded-4">
        <h5 class="mb-3">Quick Actions</h5>
        <a href="<?= base_url('reservations') ?>" class="btn btn-outline-primary me-2"><i class="fas fa-list me-2"></i><?= $user->getRole() == 'admin' ? 'All Reservations' : 'My Reservations' ?></a>
        <?php if ($user->getRole() === 'admin'): ?>
            <a href="<?= base_url('/admin/materials') ?>" class="btn btn-outline-warning"><i class="fas fa-tools me-2"></i>Manage Materials</a>
            <a href="<?= base_url('/admin/materials') ?>" class="btn btn-outline-info"><i class="fas fa-tools me-2"></i>Manage users</a>
        <?php endif; ?>
    </div>
</div>