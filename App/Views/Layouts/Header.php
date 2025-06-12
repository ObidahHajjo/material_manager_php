<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="styleSheet" href="<?= base_url('assets/css/NavBar.css') ?>" />
    <script src="<?= base_url("assets/js/main.js") ?>">
    </script>
    <link href="<?= base_url("assets/css/shared.css") ?>" rel="stylesheet" />
    <link href="<?= base_url("assets/css/main.css") ?>" rel="stylesheet" />
    <?php if (isset($styles) && !empty($styles) && is_array($styles)) : ?>
        <?php foreach ($styles as $styleSheet) : ?>
            <link rel="styleSheet" href="<?= htmlspecialchars('/assets/css/' . $styleSheet . ".css") ?>" />
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>