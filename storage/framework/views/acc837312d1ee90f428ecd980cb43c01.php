
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php echo $__env->make('partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body>
    <?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    
    <?php echo $__env->make('partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Scripts supplémentaires peuvent être chargés via la section scripts -->
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html><?php /**PATH /home/aina-ny-antsa/Documents/FIANARANA/Semestre6/EVALUATION/Ev-02/new-app-laravel/resources/views/layouts/app.blade.php ENDPATH**/ ?>