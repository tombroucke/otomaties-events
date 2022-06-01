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
                <th><?php echo $extraField ? esc_html($extraField->label()) : esc_html($fieldName); ?></th>
                <td><?php echo esc_html($value); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <tr>
        <th><?php _e('Tickets', 'otomaties-events'); ?></th>
        <td>
            <ul>
                <?php foreach ($registration->tickets() as $ticketName => $ticketCount) : ?>
                    <?php $ticket = $registration->event()->ticketType($ticketName); ?>
                    <li><?php printf('<strong>%d</strong> x %s', esc_html($ticketCount), ($ticket ? esc_html($ticket->title()) : esc_html($ticketName))); ?></li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
