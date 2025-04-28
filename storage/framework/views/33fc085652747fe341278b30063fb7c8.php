<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>List products</h1>
    <table border="1" >
        <thead>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                <tr>
                    <td><?php echo e($product['name']); ?></td>
                    <td><?php echo e($product['quantity']); ?></td>
                    <td><?php echo e($product['price']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    
</body>
</html><?php /**PATH C:\Users\Ny Antsa\Documents\Fianarana\semestre6\Evaluation\template_laravel\resources\views/products/list.blade.php ENDPATH**/ ?>