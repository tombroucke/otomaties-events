<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Registration;

class Mailer
{

    public function confirmationEmail(Registration $registration)
    {
        if (!get_field('otomaties_events_enable_confirmation_email', 'option')) {
            return;
        }
        $subject = str_replace(array_keys(self::mergeTags($registration)), array_values(self::mergeTags($registration)), get_field('otomaties_events_confirmation_email_subject', 'option'));
        $message = wpautop(get_field('otomaties_events_confirmation_email', 'option'));

        $message = str_replace(array_keys(self::mergeTags($registration)), array_values(self::mergeTags($registration)), $message);

        return $this->sendMail($registration->meta()->get('email'), $subject, $message);
    }

    public function notificationEmail(Registration $registration)
    {
        if (!get_field('otomaties_events_enable_notification_email', 'option')) {
            return;
        }
        $subject = str_replace(array_keys(self::mergeTags($registration)), array_values(self::mergeTags($registration)), get_field('otomaties_events_notification_email_subject', 'option'));
        $message = wpautop(get_field('otomaties_events_notification_email', 'option'));

        $message = str_replace(array_keys(self::mergeTags($registration)), array_values(self::mergeTags($registration)), $message);

        return $this->sendMail($registration->meta()->get('email'), $subject, $message);
    }

    public function message($template, array $variables = [])
    {
        extract($variables);
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/' . $template  . '.php';
        return ob_get_clean();
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
     * @param array $headers
     * @return bool Whether the email has been sent
     */
    private function sendMail(string $to, string $subject, string $message, array $headers = array())
    {

        $replyToAddress = get_field('otomaties_events_confirmation_from_name');
        $replyToName = get_field('otomaties_events_confirmation_from_email');

        if ($replyToAddress && $replyToName) {
            $headers[] = sprintf('Reply-To: %s<%s>', $replyToAddress, $replyToName);
        }

        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/header.php';
        echo $message;
        include dirname(__FILE__, 2) . '/views/emails/footer.php';
        $body = ob_get_clean();

        add_filter('wp_mail_content_type', array( $this, 'wpHtmlMail' ));
        $mailSent = wp_mail($to, $subject, $body, $headers);
        remove_filter('wp_mail_content_type', array( $this, 'wpHtmlMail' ));

        return $mailSent;
    }

    public static function mergeTags(Registration $registration = null) {
        return [
            '{first_name}' => $registration ? esc_html($registration->meta()->get('first_name')) : '{first_name}',
            '{last_name}' => $registration ? esc_html($registration->meta()->get('last_name')) : '{last_name}',
            '{email}' => $registration ? esc_html($registration->meta()->get('email')) : '{email}',
            '{phone}' => $registration ? esc_html($registration->meta()->get('phone')) : '{phone}',
            '{custom_fields}' => $registration ? self::customFieldsTable($registration->meta()->get('extra_fields'), $registration) : '{custom_fields}',
            '{event}' => $registration ? esc_html($registration->event()->title()) : '{event}',
            '{event_date}' => $registration ? esc_html($registration->event()->eventDate()->format(get_option('date_format'))) : '{event_date}',
            '{event_time}' => $registration ? esc_html($registration->event()->eventTime()) : '{event_time}',
            '{ticket_table}' => $registration ? self::ticketTable($registration->tickets()) : '{ticket_table}',
        ];
    }

    public static function ticketTable(array $tickets) {
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/ticket-table.php';
        return ob_get_clean();
    }

    public static function customFieldsTable(array $extraFields, Registration $registration) {
        ob_start();
        include dirname(__FILE__, 2) . '/views/emails/custom-fields.php';
        return ob_get_clean();
    }
}
