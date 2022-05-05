<?php if (!get_field('otomaties_events_hide_registrations_closed_notice', 'option')) : ?>
    <div class="alert alert-primary">
        <?php _e('Registrations are closed', 'otomaties-events'); ?>
    </div>
<?php else: ?>
    <!-- Registrations are closed -->
<?php endif; ?>
