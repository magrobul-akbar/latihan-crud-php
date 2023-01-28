<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - CRUD<?= date('Y'); ?></title>

    <!-- bootstrap css -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="assets/bootstrap-icons/font/bootstrap-icons.css">

    <!-- custom style -->
    <style>
    .print-header {
        display: none;
    }

    @media print {
        nav {
            display: none !important;
        }

        footer {
            display: none !important;
        }

        .card .card-header {
            display: none;
        }

        .card {
            border: none !important;
        }

        .tools {
            display: none !important;
        }

        .aksi {
            display: none;
        }

        .print-header {
            display: block;
        }

        .alert {
            display: none !important;
        }
    }
    </style>
</head>

<body class="bg-light">

    <!-- header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <h3 class="text-center">CRUD PHP - <?= date('Y'); ?> WITH BOOTSTRAP 4</h3>

            <?php if( is_login() ) : ?>
            <a href="logout.php" class="btn btn-primary btn-sm">Logout <i class="bi bi-box-arrow-right"></i></a>
            <?php endif; ?>

        </div>
    </nav>

    <!-- content -->
    <div class="container mt-4" style="margin-bottom: 100px;">