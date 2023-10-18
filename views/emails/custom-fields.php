<?php if (!empty($extraFields)) : ?>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td class="attributes_content">
            <ul style="margin-bottom: 0; margin-top: 0;">
            <?php foreach ($extraFields as $fieldName => $value) : ?>
                <?php
                    $extraField = $registration->event()->extraFormField($fieldName);
                    $fieldLabel = $extraField ? $extraField->label() : $fieldName;
                    $value = $extraField->optionValue($value) ? $extraField->optionValue($value) : $value;
                ?>
                <li><?php esc_html_e($fieldLabel); ?>: <?php esc_html_e($value); ?></li><?php // phpcs:ignore Generic.Files.LineLength ?>
            <?php endforeach; ?>
            </ul>
        </td>
    </tr>
</table>
<?php endif; ?>
