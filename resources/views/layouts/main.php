<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <?php $successFlash = \App\Core\Session::getFlash('success'); ?>
    <?php if ($successFlash): ?>
        <meta name="success-message" content="<?= e($successFlash) ?>">
    <?php endif; ?>
    <title><?= isset($title) ? e($title) . ' | ' : '' ?><?= e(APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- App styles -->
    <link rel="stylesheet" href="<?= asset_url('css/app.css') ?>">
</head>
<body class="bg-light">

    <?php if (!empty($user) && isset($showSidebar) && $showSidebar): ?>
        <div class="d-flex" id="wrapper">
            <?php require ROOT_PATH . '/resources/views/partials/sidebar.php'; ?>

            <div id="page-content-wrapper" class="w-100">
                <?php require ROOT_PATH . '/resources/views/partials/navbar.php'; ?>

                <main class="container-fluid py-4">
                    <?php $success = $successFlash ?? null; require ROOT_PATH . '/resources/views/partials/alert.php'; ?>
                    <?= $content ?? '' ?>
                </main>

                <?php require ROOT_PATH . '/resources/views/partials/footer.php'; ?>
            </div>
        </div>
    <?php else: ?>
        <main class="min-vh-100 d-flex align-items-center justify-content-center">
            <?php $success = $successFlash ?? null; require ROOT_PATH . '/resources/views/partials/alert.php'; ?>
            <?= $content ?? '' ?>
        </main>
    <?php endif; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- App scripts -->
    <script src="<?= asset_url('js/app.js') ?>"></script>
</body>
</html>
