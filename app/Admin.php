<?php

namespace Otomaties\Events;

use DateTime;
use Otomaties\Events\Models\Subscription;

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
        if (!is_admin()) {
            return $metadata;
        }
        $currentScreen = get_current_screen();
        if ($meta_key == 'date' && $currentScreen->parent_file == 'edit.php?post_type=event') {
            remove_filter('get_post_metadata', [$this, 'formatDateInAdminColumn'], 100);
            $date = get_post_meta($object_id, 'date', true);
            add_filter('get_post_metadata', [$this, 'formatDateInAdminColumn'], 100, 4);

            if (!$date) {
                return '—';
            }

            $dateTime = DateTime::createFromFormat('Ymd', $date);
            return [$dateTime->format('d/m/Y')];
        }
        return $metadata;
    }

    public function register() {
        
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = $_POST['phone'];
        $tickets = $_POST['ticket'];
        $extraFields = $_POST['extra_fields'] ?? [];
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
        $registrationNonce = sanitize_title($_POST['registration_nonce']);
        $redirect = $_SERVER['HTTP_REFERER'];

        if (!wp_verify_nonce($registrationNonce, 'register_for_' . $eventId)) {
            $redirect = add_query_arg(
                ['success' => 'false', 'error-message' => 'suspected-bot-activity'],
                $redirect,
            );
            wp_safe_redirect($redirect);
        }

        $subscriptionId = wp_insert_post([
            'post_title' => $firstName . ' ' . $lastName,
            'post_status' => 'publish',
            'post_type' => 'subscription',
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

        if ($subscriptionId) {
            $redirect = add_query_arg(
                ['success' => 'true', 'subscription_id' => $subscriptionId],
                $redirect,
            );
        } else {
            $redirect = add_query_arg(
                ['success' => 'false', 'error-message' => 'generic-error'],
                $redirect,
            );
        }
        wp_safe_redirect($redirect);
    }

    public function metaBoxes() {
        add_meta_box('subscription_details', __('Details', 'otomaties-events'), [$this, 'subscriptionDetails'], 'subscription', 'normal', 'high');
    }

    public function subscriptionDetails() {
        $subscription = new Subscription(get_the_ID());
        include dirname(__FILE__, 2) . '/views/subscription-details.php';
    }
}
