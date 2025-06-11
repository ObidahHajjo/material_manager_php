<?php

use Config\Session;

$user = Session::get('user');
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand text-primary fw-bold fs-2" href="<?= base_url("praticien"); ?>">ProMed</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-2 text-secondary" href="<?= base_url("praticien"); ?>">
            <span>📊</span> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-2 text-secondary" href="<?= base_url("praticien/liste-patient"); ?>">
            <span>👥</span> Patients
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-2 text-secondary" href="<?= base_url("praticien/nouveau-patient"); ?>">
            <span><i class="fa-solid fa-plus"></i></span> Ajouter un patient
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-2 text-secondary" href="<?= base_url("praticien/service/") . $user->getId() ?>">
            <span><i class="fa-solid fa-user"></i></span> Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger ms-4" href="<?= base_url("deconnexion"); ?>">
            Déconnexion
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Extra margin for content below navbar -->
<div style=" margin-top: 7rem;">
</div>