<?php if (!empty($extraFields)) : ?>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="attributes_content">
            <ul style="margin-bottom: 0; margin-top: 0;">
            <?php foreach ($extraFields as $fieldName => $value) : ?>
                <?php $extraField = $registration->event()->extraFormField($fieldName); ?>
                <li><?php echo $extraField ?  esc_html($extraField->label()) : esc_html($fieldName); ?>: <?php echo esc_html($value); ?></li>
            <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
<?php endif; ?>

