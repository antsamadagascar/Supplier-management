<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="mb-0 fs-4">Calendrier</h1>
                </div>
                <div class="card-body">
                    <!-- Élément où FullCalendar sera rendu -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<!-- CSS de FullCalendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- Inclure le JavaScript de FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<!-- Code JavaScript pour initialiser le calendrier -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            locale: 'fr',
            events: [
                {
                    title: 'Réunion',
                    start: '2025-04-25T10:00:00',
                    end: '2025-04-25T12:00:00',
                    backgroundColor: '#0d6efd'
                },
                {
                    title: 'Déjeuner',
                    start: '2025-04-26T12:00:00',
                    backgroundColor: '#198754'
                }
            ]
        });
        calendar.render();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/aina-ny-antsa/Documents/FIANARANA/Semestre6/EVALUATION/Ev-02/new-app-laravel/resources/views/calendar/index.blade.php ENDPATH**/ ?>