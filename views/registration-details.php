<?php do_action('otomaties_events_before_registration_details'); ?>
<table class="widefat striped">
    <tr>
        <th><?php _e('First name', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($registration->meta()->get('first_name')); ?></td>
    </tr>
    <tr>
        <th><?php _e('Last name', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($registration->meta()->get('last_name')); ?></td>
    </tr>
    <tr>
        <th><?php _e('E-mailaddress', 'otomaties-events'); ?></th>  
        <td><?php echo esc_html($registration->meta()->get('email')); ?></td>
    </tr>
    <tr>
        <th><?php _e('Phone', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($registration->meta()->get('phone')); ?></td>
    </tr>
    <?php if (!empty($registration->meta()->get('extra_fields'))) : ?>
        <?php foreach ($registration->meta()->get('extra_fields') as $fieldName => $value) : ?>
            <?php $extraField = $registration->event()->extraFormField($fieldName); ?>
            <tr>
                <?php
                    $fieldLabel = $extraField ? $extraField->label() : $fieldName;
                    $value = $extraField->optionValue($value) ? $extraField->optionValue($value) : $value;
                ?>
                <th><?php esc_html_e($fieldLabel); ?></th>
                <td><?php esc_html_e($value); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <tr>
        <th><?php _e('Tickets', 'otomaties-events'); ?></th>
        <td>
            <ul>
                <?php foreach ($registration->tickets() as $ticketName => $ticketCount) : ?>
                    <?php $ticket = $registration->event()->ticketType($ticketName); ?>
                    <li><?php printf('<strong>%d</strong> x %s', esc_html($ticketCount), ($ticket ? esc_html($ticket->title()) : esc_html($ticketName))); ?></li><?php // phpcs:ignore Generic.Files.LineLength ?>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
<?php do_action('otomaties_events_after_registration_details'); ?>
