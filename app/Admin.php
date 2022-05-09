<?php

namespace Otomaties\Events;

use DateTime;
use Otomaties\Events\Models\Event;
use Otomaties\Events\Models\Registration;
use Otomaties\Events\Models\TicketType;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @subpackage Events/admin
 */

class Admin
{

    /**
     * The ID of this plugin.
     *
     * @var      string    $pluginName    The ID of this plugin.
     */
    private $pluginName;

    /**
     * The version of this plugin.
     *
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $pluginName       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($pluginName, $version)
    {

        $this->pluginName = $pluginName;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     */
    public function enqueueStyles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->pluginName, Assets::find('css/admin.css'), array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     */
    public function enqueueScripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->pluginName, Assets::find('js/admin.js'), array( 'jquery' ), $this->version, false);
    }

    public function formatDateInAdminColumn($metadata, $object_id, $meta_key, $single) {
        if (!is_admin() || !function_exists('get_current_screen') || !get_current_screen()) {
            return $metadata;
        }
        if ($meta_key == 'date' && isset($_GET['post_type']) && $_GET['post_type'] == 'event') {
            remove_filter('get_post_metadata', [$this, 'formatDateInAdminColumn'], 100);
            $date = get_post_meta($object_id, 'date', true);
            add_filter('get_post_metadata', [$this, 'formatDateInAdminColumn'], 100, 4);

            if (!$date) {
                return $metadata;
            }

            $dateTime = DateTime::createFromFormat('Ymd', $date);
            return [$dateTime->format('d/m/Y')];
        }
        return $metadata;
    }

    public function register() {
        
        $firstName = sanitize_text_field($_POST['first_name']);
        $lastName = sanitize_text_field($_POST['last_name']);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = sanitize_text_field($_POST['phone']);
        $tickets = $_POST['ticket'];
        foreach ($tickets as $key => $ticket) {
            $tickets[esc_attr($key)] = filter_var((int)$ticket, FILTER_SANITIZE_NUMBER_INT);
        }
        $extraFields = [];
        if (isset($_POST['extra_fields'])) {
            foreach ($_POST['extra_fields'] as $key => $value) {
                $extraFields[esc_attr($key)] = sanitize_text_field($value);
            }
        }
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
        $registrationNonce = sanitize_title($_POST['registration_nonce']);
        $redirect = strtok($_SERVER['HTTP_REFERER'], '?');


        if (!wp_verify_nonce($registrationNonce, 'register_for_' . $eventId)) {
            $redirect = add_query_arg(
                ['registration_success' => 'false', 'error-message' => 'suspected-bot-activity'],
                $redirect,
            );
            wp_safe_redirect($redirect);
            die();
        }

        $errors = [];

        $event = new Event($eventId);

        if (!$event->registrationsOpen()) {
            $errors[] = __('We\'re sorry, registrations are closed.', 'otomaties-events');
        }

        $totalTicketCount = 0;
        foreach ($tickets as $ticketName => $ticketCount) {
            $totalTicketCount += $ticketCount;
        }
        
        if ($event->freeSpots() < $totalTicketCount) {
            $errors[] = __('We\'re sorry, we don\'t have enough tickets available.', 'otomaties-events');
        }
        
        if ($totalTicketCount < 1) {
            $errors[] = __('Please select at least one ticket.', 'otomaties-events');
        }

        foreach ($tickets as $ticketName => $ticketCount) {
            $ticketType = $event->ticketType($ticketName);
            $availableTickets = $ticketType->availableTickets();
            if ($availableTickets < $ticketCount) {
                $errors[] = sprintf(__('The maximum number of tickets for %s is %s', 'otomaties-events'), $ticketType->title(), $availableTickets);
                // TODO: fill fields
            }
        }

        if (!empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['registration_errors'] = $errors;

            $redirect = add_query_arg(
                ['registration_success' => 'false'],
                $redirect,
            );
            wp_safe_redirect($redirect);
            die();
        }

        $registration = Registration::insert([
            'post_title' => $firstName . ' ' . $lastName,
            'meta_input' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'event_id' => $eventId,
                'tickets' => array_filter($tickets),
                'extra_fields' => $extraFields,
            ]
        ]);

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $registration->meta()->set('user_id', get_current_user_id());
        }


        if (!is_wp_error($registration) && $registration) {
            do_action('otomaties_events_new_registration', $registration);

            $redirect = add_query_arg(
                ['registration_success' => 'true', 'registration_id' => $registration->getId()],
                $redirect,
            );
        } else {
            $redirect = add_query_arg(
                ['registration_success' => 'false', 'error-message' => 'generic-error'],
                $redirect,
            );
        }
        wp_safe_redirect($redirect);
        die();
    }

    public function metaBoxes() {
        add_meta_box('registration_details', __('Details', 'otomaties-events'), [$this, 'registrationDetails'], 'registration', 'normal', 'high');
    }

    public function registrationDetails() {
        $registration = new Registration(get_the_ID());
        include dirname(__FILE__, 2) . '/views/registration-details.php';
    }

    public function replaceStringHackedPostIds($return, $query, $filter) {
        $return = [];
        $return['meta_query'][] = [
            'key'   => $filter['meta_key'],
            'value' => str_replace('event_', '', wp_unslash($query['event_id'])),
        ];
        return $return;
    }

    public function exportBtn($which) {
        $postType = $_GET['post_type'] ?? 'post';
        $eventId = $_GET['event_id'] ?? 0;

        if ('registration' == $postType && $eventId) {
            ?>
            <a class="button button-primary" href="<?php echo admin_url('edit.php?post_type=registration&event_id=event_2055&action=export') ?>"><?php _e('Export', 'otomaties-events'); ?></a>
            <?php
        }
    }

    public function exportRegistrations() {
        $action = $_GET['action'] ?? null;
        $eventId = $_GET['event_id'] ?? null;
        if (!current_user_can('edit_posts') || 'export' != $action || !$eventId) {
            return;
        }

        $event = new Event($eventId);
        $export = new RegistrationExport($event);
        $export->execute();
        die();
    }
}
