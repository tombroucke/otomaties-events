<?php do_action('otomaties_events_before_registration_form'); ?>
<form action="<?php echo admin_url('admin-post.php'); ?>" method="POST"
    class="form-event-registration js-form-event-registration">
    <?php if (apply_filters('otomaties_events_registration_form_show_title', true)) : ?>
        <h2><?php echo apply_filters('otomaties_events_string_register', __('Register', 'otomaties-events')); ?></h2>
    <?php endif; ?>
    <?php if (apply_filters('otomaties_events_registration_form_show_subtitle', true)) : ?>
        <h3><?php echo apply_filters('otomaties_events_string_personal_details', __('Personal details', 'otomaties-events')); ?></h3><?php // phpcs:ignore Generic.Files.LineLength ?>
    <?php endif; ?>
    <div class="<?php echo apply_filters('otomaties_events_section_class', 'row g-3 mb-5'); ?>">
        <?php if ($event->showField('first_name')) : ?>
            <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
                <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                <label for="first_name"><?php _e('First name', 'otomaties-events'); ?> <span
                        class="text-danger">*</span></label>
                <?php endif; ?>
                <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                    name="first_name" placeholder="<?php _e('First name', 'otomaties-events'); ?>"
                    value="<?php esc_html_e($user->first_name); ?>" required>
            </div>
        <?php endif; ?>
        <?php if ($event->showField('last_name')) : ?>
            <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
                <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                    <label for="last_name"><?php _e('Last name', 'otomaties-events'); ?> <span
                        class="text-danger">*</span></label>
                <?php endif; ?>
                <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                    name="last_name" placeholder="<?php _e('Last name', 'otomaties-events'); ?>"
                    value="<?php esc_html_e($user->last_name); ?>" required>
            </div>
        <?php endif; ?>
        <?php if ($event->showField('email')) : ?>
            <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
                <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                    <label for="email"><?php _e('Email address', 'otomaties-events'); ?> <span
                        class="text-danger">*</span></label>
                <?php endif; ?>
                <input type="email" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                    name="email" placeholder="<?php _e('Email address', 'otomaties-events'); ?>"
                    value="<?php esc_html_e($user->user_email); ?>" required>
            </div>
        <?php endif; ?>
        <?php if ($event->showField('phone')) : ?>
            <div class="<?php echo apply_filters('otomaties_events_input_container_class', 'col-md-6'); ?>">
                <?php if (apply_filters('otomaties_events_display_input_label', true)) : ?>
                    <label for="phone"><?php _e('Phone number', 'otomaties-events'); ?> <span
                        class="text-danger">*</span></label>
                <?php endif; ?>
                <input type="text" class="<?php echo apply_filters('otomaties_events_input_class', 'form-control'); ?>"
                    name="phone" placeholder="<?php _e('Phone number', 'otomaties-events'); ?>"
                    value="<?php esc_html_e($user->user_phone); ?>" required>
            </div>
        <?php endif; ?>
    
    <?php if (!$event->mergeFormFields()) : ?>
        </div>
        <div class="<?php echo apply_filters('otomaties_events_section_class', 'row g-3 mb-5'); ?>">
    <?php endif; ?>
    <?php if (!empty($event->extraFormFields())) : ?>
        <?php if (!$event->mergeFormFields()) : ?>
            <h3><?php echo apply_filters('otomaties_events_string_extra_information', __('Extra information', 'otomaties-events')); ?></h3><?php // phpcs:ignore Generic.Files.LineLength ?>
        <?php endif; ?>
        <?php foreach ($event->extraFormFields() as $extraFormField) : ?>
        <div class="col-12">
            <?php $extraFormField->render(); ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
    <?php if (!apply_filters('otomaties_events_hide_tickets_title', $event->hideTicketsTitle())) : ?>
        <h3><?php echo apply_filters('otomaties_events_string_personal_tickets', __('Tickets', 'otomaties-events')); ?></h3><?php // phpcs:ignore Generic.Files.LineLength ?>
    <?php endif; ?>
    <?php if (count($event->ticketTypes()) > 0) : ?>
        <?php
        $classes = 'row g-3 mb-5 event-tickets';
        if ($event->isUniqueRegistration()) {
            $classes .= ' event-tickets--unique-registration';
        }
        ?>
        <div class="<?php echo apply_filters('otomaties_tickets_section_class', $classes); ?>">
            <?php foreach ($event->ticketTypes() as $ticket) : ?>
                <?php if ($ticket->isAvailable()) : ?>
                    <?php
                        $ticketLabel = apply_filters(
                            'otomaties_events_string_ticket_label',
                            $ticket->title() . ' ' . $ticket->priceHtml('(', ')'),
                            $ticket
                        );
                        $ticketAmountPlaceholder = apply_filters(
                            'otomaties_events_string_ticket_amount_placeholder',
                            0,
                            $ticket
                        );
                    ?>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"
                                id="ticket_<?php echo $ticket->slug() ?>"><?php esc_html_e($ticketLabel); ?></span><?php // phpcs:ignore Generic.Files.LineLength ?>
                            <input type="number" min="0" max="<?php echo min($ticket->registrationLimit(), $ticket->ticketLimitPerRegistration(), $ticket->availableTickets()); ?>"<?php // phpcs:ignore Generic.Files.LineLength ?>
                                class="<?php esc_attr_e(apply_filters('otomaties_events_input_class', 'form-control')); ?>"<?php // phpcs:ignore Generic.Files.LineLength ?>
                                name="ticket[<?php esc_html_e($ticket->slug()); ?>]" 
                                value="<?php echo $event->isUniqueRegistration() ? '1' : esc_html($ticket->defaultValue()); ?>"<?php // phpcs:ignore Generic.Files.LineLength ?>
                                placeholder="<?php echo $ticketAmountPlaceholder; ?>"
                                aria-label="<?php esc_html_e($ticket->title()); ?>"
                                aria-describedby="ticket_<?php esc_html_e($ticket->slug()); ?>">
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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
