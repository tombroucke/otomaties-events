<?php if (!empty($tickets)) : ?>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="attributes_content">
            <ul style="margin-bottom: 0; margin-top: 0;">
            <?php foreach ($tickets as $ticketName => $ticketCount) : ?>
                <li><?php printf(apply_filters('otomaties_events_ticket_table_ticket_pattern', '<strong>%s</strong> x %d'), esc_html($ticketName), esc_html($ticketCount)); ?></li>
            <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
<?php endif; ?>
