<table class="widefat striped">
    <tr>
        <th><?php _e('First name', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($subscription->get('first_name')); ?></td>
    </tr>
    <tr>
        <th><?php _e('Last name', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($subscription->get('last_name')); ?></td>
    </tr>
    <tr>
        <th><?php _e('E-mailaddress', 'otomaties-events'); ?></th>  
        <td><?php echo esc_html($subscription->get('email')); ?></td>
    </tr>
    <tr>
        <th><?php _e('Phone', 'otomaties-events'); ?></th>
        <td><?php echo esc_html($subscription->get('phone')); ?></td>
    </tr>
    <?php foreach ($subscription->get('extra_fields') as $fieldName => $value) : ?>
        <?php $extraField = $subscription->event()->extraFormField($fieldName); ?>
        <tr>
            <th><?php echo $extraField ?  esc_html($extraField->label()) : esc_html($fieldName); ?></th>
            <td><?php echo esc_html($value); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <th><?php _e('Tickets', 'otomaties-events'); ?></th>
        <td>
            <ul>
                <?php foreach ($subscription->tickets() as $ticketName => $ticketCount) : ?>
                    <?php $ticket = $subscription->event()->ticket($ticketName); ?>
                    <li><?php printf('<strong>%d</strong> x %s', esc_html($ticketCount), ($ticket ? $ticket->title() : $ticketName)); ?></li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
