<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Registration;

class Mailer
{

    /**
     * Send confirmation email
     *
     * @param Registration $registration
     * @return boolean
     */
    public function confirmationEmail(Registration $registration) : bool
    {
        if (!get_field('otomaties_events_enable_confirmation_email', 'option')) {
            return false;
        }
        $subject = str_replace(
            array_keys(self::mergeTags($registration)),
            array_values(self::mergeTags($registration)),
            get_field('otomaties_events_confirmation_email_subject', 'option')
        );
        $message = wpautop(get_field('otomaties_events_confirmation_email', 'option'));

        $message = str_replace(
            array_keys(self::mergeTags($registration)),
            array_values(self::mergeTags($registration)),
            $message
        );

        $headers = [];
        $replyToAddress = get_field('otomaties_events_confirmation_from_name');
        $replyToName = get_field('otomaties_events_confirmation_from_email');

        if ($replyToAddress && $replyToName) {
            $headers[] = sprintf('Reply-To: %s<%s>', $replyToAddress, $replyToName);
        }

        return $this->sendMail($registration->meta()->get('email'), html_entity_decode($subject), $message, $headers);
    }

    /**
     * Send notification email
     *
     * @param Registration $registration
     * @return boolean
     */
    public function notificationEmail(Registration $registration) : bool
    {
        if (!get_field('otomaties_events_enable_notification_email', 'option')) {
            return false;
        }
        $subject = str_replace(
            array_keys(self::mergeTags($registration)),
            array_values(self::mergeTags($registration)),
            get_field('otomaties_events_notification_email_subject', 'option')
        );
        $message = wpautop(get_field('otomaties_events_notification_email', 'option'));

        $message = str_replace(
            array_keys(self::mergeTags($registration)),
            array_values(self::mergeTags($registration)),
            $message
        );

        $headers = [];
        $to = get_field('otomaties_events_notification_recipients', 'option');
        $replyToAddress = esc_html($registration->meta()->get('email'));
        $replyToName = esc_html($registration->meta()->get('first_name'));

        if ($replyToAddress && $replyToName) {
            $headers[] = sprintf('Reply-To: %s<%s>', $replyToAddress, $replyToName);
        }

        return $this->sendMail($to, html_entity_decode($subject), $message, $headers);
    }

    /**
     * Allow HTML email
     *
     * @return string
     */
    public function wpHtmlMail()
    {
        return 'text/html';
    }

    /**
     * Send an email
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array<string, string> $headers
     * @return boolean Whether the email has been sent
     */
    private function sendMail(string $to, string $subject, string $message, array $headers = array()) : bool
    {
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/header.php';
        echo wp_kses($message, [
            'p' => [],
            'b' => [],
            'br' => [],
            'em' => [],
            'a' => [
                'href' => [],
                'title' => [],
            ],
            'ol' => [],
            'ul' => [],
            'li' => [],
            'strong' => [],
        ]);
        include dirname(__FILE__, 2) . '/views/emails/footer.php';
        $body = ob_get_clean();

        add_filter('wp_mail_content_type', array( $this, 'wpHtmlMail' ));
        $mailSent = wp_mail($to, $subject, $body, $headers);
        remove_filter('wp_mail_content_type', array( $this, 'wpHtmlMail' ));

        return $mailSent;
    }

    /**
     * Get all merge tags with their value
     *
     * @param Registration|null $registration
     * @return array<string, string>
     */
    public static function mergeTags(Registration $registration = null) : array
    {
        return [
            '{first_name}' => $registration ? esc_html($registration->meta()->get('first_name')) : '{first_name}',
            '{last_name}' => $registration ? esc_html($registration->meta()->get('last_name')) : '{last_name}',
            '{email}' => $registration ? esc_html($registration->meta()->get('email')) : '{email}',
            '{phone}' => $registration ? esc_html($registration->meta()->get('phone')) : '{phone}',
            '{custom_fields}' => $registration ? self::customFieldsTable($registration->meta()->get('extra_fields'), $registration) : '{custom_fields}', // phpcs:ignore Generic.Files.LineLength
            '{event}' => $registration ? esc_html($registration->event()->title()) : '{event}',
            '{event_date}' => $registration ? esc_html($registration->event()->formattedEventDate()) : '{event_date}', // phpcs:ignore Generic.Files.LineLength
            '{event_time}' => $registration ? esc_html($registration->event()->eventTime()) : '{event_time}',
            '{ticket_table}' => $registration ? self::ticketTable($registration->tickets()) : '{ticket_table}',
        ];
    }

    /**
     * Ticket table
     *
     * @param array<string, int> $tickets
     * @return string
     */
    public static function ticketTable(array $tickets) : string
    {
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/ticket-table.php';
        return ob_get_clean();
    }

    /**
     * Extra fields table
     *
     * @param array<string, mixed> $extraFields
     * @param Registration $registration
     * @return string
     */
    public static function customFieldsTable(array $extraFields, Registration $registration) : string
    {
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/custom-fields.php';
        return ob_get_clean();
    }
}
