<?php

use Config\Session;

$user = Session::get('user');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold text-gradient fs-4 me-5" href="<?= base_url('dashboard'); ?>">
      <i class="fas fa-boxes-stacked me-2"></i>Material's Manager
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto gap-2 align-items-center">

        <li class="nav-item">
          <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-3 <?= $active && $active === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-line me-1"></i> Dashboard
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url('reservations') ?>" class="btn btn-outline-primary rounded-pill px-3 <?= $active && $active === 'reseravtions' ? 'active' : '' ?>">
            <i class="fas fa-list me-1"></i> <?= $user->getRole() == 'admin' ? 'All Reservations' : 'My Reservations' ?>
          </a>
        </li>

        <?php if ($user->getRole() === 'admin'): ?>
          <li class="nav-item">
            <a href="<?= base_url('admin/materials') ?>" class="btn btn-outline-warning rounded-pill px-3 <?= $active && $active === 'materials' ? 'active' : '' ?>">
              <i class="fas fa-tools me-1"></i> Manage Materials
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-info rounded-pill px-3 <?= $active && $active === 'users' ? 'active' : '' ?>">
              <i class="fas fa-user-cog me-1"></i> Manage Users
            </a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="<?= base_url('logout') ?>" class="btn btn-danger rounded-pill px-3">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Add margin for fixed-top -->
<div style="margin-top: 10.5rem;"></div>