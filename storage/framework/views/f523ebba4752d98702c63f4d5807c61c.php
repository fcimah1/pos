<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>POS System</title>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="bg-gray-900 text-white antialiased">
    <div id="app"></div>
</body>

</html><?php /**PATH F:\WAMP\www\POS_SYSTEM\resources\views/welcome.blade.php ENDPATH**/ ?>