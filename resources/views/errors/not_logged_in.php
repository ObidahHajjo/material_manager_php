<?php require 'shared_error_layout.php'; ?>
<?php
$title = "Unauthorized Access";
$heading = "401 Unauthorized";
?>

<script>
    setTimeout(() => {
        window.location.href = '/login';
    }, 5000);
</script>