<form action="<?php echo admin_url('admin-post.php'); ?>" method="POST" class="form-event-registration js-form-event-registration">
    <h2><?php _e('Register', 'otomaties-events'); ?></h2>
    <h3><?php _e('Personal details', 'otomaties-events'); ?></h3>
    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <label for="first_name"><?php _e('First name', 'otomaties-events'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="first_name" placeholder="<?php _e('First name', 'otomaties-events'); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="last_name"><?php _e('Last name', 'otomaties-events'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="last_name" placeholder="<?php _e('Last name', 'otomaties-events'); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="email"><?php _e('Email address', 'otomaties-events'); ?> <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" placeholder="<?php _e('Email address', 'otomaties-events'); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="phone"><?php _e('Phone number', 'otomaties-events'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="phone" placeholder="<?php _e('Phone number', 'otomaties-events'); ?>" required>
        </div>
    </div>
    <?php if (!empty($event->extraFormFields())) : ?>
        <h3><?php _e('Extra information', 'otomaties-events'); ?></h3>
        <div class="row g-3 mb-5">
            <?php foreach ($event->extraFormFields() as $extraFormField) : ?>
                <div class="col-12">
                    <?php $extraFormField->render(); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <h3><?php _e('Tickets', 'otomaties-events'); ?></h3>
    <?php foreach ($event->ticketTypes() as $ticket) : ?>
        <?php if ($ticket->isAvailable()) : ?>
            <div class="input-group mb-3">
                <span class="input-group-text" id="ticket_<?php $ticket->slug() ?>"><?php echo $ticket->title(); ?></span>
                <input type="number" min="0" max="<?php echo $ticket->availableTickets(); ?>" class="form-control" name="ticket[<?php echo $ticket->slug(); ?>]" placeholder="0" aria-label="<?php echo $ticket->title(); ?>" aria-describedby="ticket_<?php $ticket->slug() ?>">
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <input type="hidden" name="action" value="event_registration" />
    <input type="hidden" name="event_id" value="<?php echo $event->getId(); ?>" />
    <?php wp_nonce_field('register_for_' . get_the_ID(), 'registration_nonce'); ?>
    <button type="submit" class="btn btn-primary"><?php _e('Register', 'otomaties-events'); ?></button>
</form>
