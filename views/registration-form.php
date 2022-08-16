<?php do_action('otomaties_events_before_registration_form'); ?>
<form action="<?php echo admin_url('admin-post.php'); ?>" method="POST"
    class="form-event-registration js-form-event-registration">
    <h2><?php echo apply_filters('otomaties_events_string_register', __('Register', 'otomaties-events')); ?></h2>
    <h3><?php echo apply_filters('otomaties_events_string_personal_details', __('Personal details', 'otomaties-events')); ?></h3><?php // phpcs:ignore Generic.Files.LineLength ?>
    <div class="<?php echo apply_filters('otomaties_events_section_class', 'row g-3 mb-5'); ?>">
        <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
            <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
            <label for="first_name"><?php _e('First name', 'otomaties-events'); ?> <span
                    class="text-danger">*</span></label>
            <?php endif; ?>
            <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                name="first_name" placeholder="<?php _e('First name', 'otomaties-events'); ?>"
                value="<?php esc_html_e($user->first_name); ?>" required>
        </div>
        <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
            <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                <label for="last_name"><?php _e('Last name', 'otomaties-events'); ?> <span
                    class="text-danger">*</span></label>
            <?php endif; ?>
            <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                name="last_name" placeholder="<?php _e('Last name', 'otomaties-events'); ?>"
                value="<?php esc_html_e($user->last_name); ?>" required>
        </div>
        <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
            <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                <label for="email"><?php _e('Email address', 'otomaties-events'); ?> <span
                    class="text-danger">*</span></label>
            <?php endif; ?>
            <input type="email" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                name="email" placeholder="<?php _e('Email address', 'otomaties-events'); ?>"
                value="<?php esc_html_e($user->user_email); ?>" required>
        </div>
        <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
            <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                <label for="phone"><?php _e('Phone number', 'otomaties-events'); ?> <span
                    class="text-danger">*</span></label>
            <?php endif; ?>
            <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                name="phone" placeholder="<?php _e('Phone number', 'otomaties-events'); ?>"
                value="<?php esc_html_e($user->user_phone); ?>" required>
        </div>
    </div>
    <?php if (!empty($event->extraFormFields())) : ?>
    <h3><?php echo apply_filters('otomaties_events_string_extra_information', __('Extra information', 'otomaties-events')); ?></h3><?php // phpcs:ignore Generic.Files.LineLength ?>
    <div class="<?php echo apply_filters('otomaties_events_section_class', 'row g-3 mb-5'); ?>">
        <?php foreach ($event->extraFormFields() as $extraFormField) : ?>
        <div class="col-12">
            <?php $extraFormField->render(); ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <h3><?php echo apply_filters('otomaties_events_string_personal_tickets', __('Tickets', 'otomaties-events')); ?></h3>
    <?php foreach ($event->ticketTypes() as $ticket) : ?>
        <?php if ($ticket->isAvailable()) : ?>
    <div class="input-group mb-3">
        <span class="input-group-text"
            id="ticket_<?php echo $ticket->slug() ?>"><?php esc_html_e($ticket->title()); ?> <?php echo $ticket->priceHtml('(', ')'); ?></span><?php // phpcs:ignore Generic.Files.LineLength ?>
        <input type="number" min="0" max="<?php echo $ticket->availableTickets(); ?>"
            class="<?php esc_attr_e(apply_filters('otomaties_events_input_class', 'form-control')); ?>"
            name="ticket[<?php esc_html_e($ticket->slug()); ?>]" placeholder="0"
            aria-label="<?php esc_html_e($ticket->title()); ?>"
            aria-describedby="ticket_<?php esc_html_e($ticket->slug()); ?>">
    </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <input type="hidden" name="action" value="event_registration" />
    <input type="hidden" name="event_id" value="<?php esc_attr_e($event->getId()); ?>" />
    <?php wp_nonce_field('register_for_' . get_the_ID(), 'registration_nonce'); ?>
    <?php do_action('otomaties_events_registration_form_before_register_button'); ?>
    <button 
        type="submit" 
        class="<?php esc_attr_e(apply_filters('otomaties_events_submit_class', 'btn btn-primary')); ?>"
    >
        <?php echo apply_filters('otomaties_events_string_register_button', __('Register', 'otomaties-events')); ?>
    </button>
</form>
<?php do_action('otomaties_events_after_registration_form'); ?>
