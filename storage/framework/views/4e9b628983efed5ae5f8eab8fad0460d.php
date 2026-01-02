<?php $__env->startSection('title', '404 - الصفحة غير موجودة'); ?>

<?php $__env->startSection('content'); ?>
<div>
    <div class="error-icon">
        <i class="fas fa-search"></i>
    </div>
    <div class="error-code">404</div>
    <div class="error-title">الصفحة غير موجودة</div>
    <div class="error-message">
        عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى مكان آخر.
    </div>
    <div class="error-actions">
        <a href="<?php echo e(url('/')); ?>" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            <span>العودة للرئيسية</span>
        </a>
        <button onclick="window.history.back()" class="error-btn error-btn-secondary">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للخلف</span>
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\A-Tech\Downloads\archiveGzNa7\resources\views/errors/404.blade.php ENDPATH**/ ?>