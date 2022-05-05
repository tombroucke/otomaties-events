<?php if (!empty($tickets)) : ?>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="attributes_content">
            <ul style="margin-bottom: 0; margin-top: 0;">
            <?php foreach ($tickets as $ticketName => $ticketCount) : ?>
                <li><?php echo esc_html($ticketName); ?> x <?php echo esc_html($ticketCount); ?></li>
            <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
<?php endif; ?>
