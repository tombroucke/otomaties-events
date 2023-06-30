<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Event;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @subpackage Events/public
 */

class Frontend
{

    /**
     * The ID of this plugin.
     *
     * @var      string    $pluginName    The ID of this plugin.
     */
    private $pluginName;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $pluginName       The name of the plugin.
     */
    public function __construct($pluginName)
    {

        $this->pluginName = $pluginName;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @return void
     */
    public function enqueueStyles() : void
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
        wp_enqueue_style($this->pluginName, Assets::find('css/main.css'), array(), null);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @return void
     */
    public function enqueueScripts() : void
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

        if (is_singular('event')) {
            wp_enqueue_script($this->pluginName, Assets::find('js/main.js'), [], null);
            wp_localize_script($this->pluginName, 'oeVars', [
                'strings' => [
                    'validator' => [
                        'required' => __('Field is required', 'otomaties-events'),
                        'maxValue' => __('Enter a value less than or equal to {0}', 'otomaties-events'),
                        'minValue' => __('Enter a value greater than or equal to {0}', 'otomaties-events'),
                        'email' => __('Please enter a valid e-mailaddress', 'otomaties-events'),
                        'select_tickets' => __('Please select one or more tickets', 'otomaties-events'),
                    ]
                ]
            ]);
        }
    }

    /**
     * Hide events in the past
     *
     * @param \WP_Query $query
     * @return void
     */
    public function hidePastEvents(\WP_Query $query) : void
    {
        ;
        if ($query->get('post_type') != 'event' || is_admin()) {
            return;
        }
        
        $meta_query = array_filter((array)$query->get('meta_query'));
        $scope = $query->get('event_scope') == '' ? 'future' : $query->get('event_scope');

        $query->set('meta_key', 'date');
        $query->set('orderby', array( 'meta_value' => 'ASC' ));

        if (apply_filters('otomaties_events_show_past_events', false)) {
            return;
        }
        if ($scope == 'future') {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'date',
                        'value' => date('Ymd'),
                        'compare' => '>='
                    ),
                    array(
                        'key' => 'date',
                        'compare'=> 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'date',
                        'value'=> '',
                    )
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'date',
                        'value' => date('Ymd'),
                        'compare' => '<='
                    ),
                    array(
                        'key' => 'date_to',
                        'value' => date('Ymd'),
                        'compare' => '>='
                    ),
                )
            );
        } elseif ($scope == 'past') {
            $meta_query[] = array(
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'date',
                        'value' => date('Ymd'),
                        'compare' => '<'
                    )
                )
            );

            $query->set('meta_key', 'date');
            $query->set('orderby', array( 'meta_value' => 'DESC' ));
        }
        $query->set('meta_query', $meta_query);
    }

    /**
     * Render registration form
     *
     * @param string $content
     * @return string
     */
    public function renderRegistrationForm($content) : string
    {
        if (is_singular('event') && apply_filters('otomaties_events_show_registration_form', true)) {
            $event = new Event(get_the_ID());
            ob_start();
            if (!empty($event->ticketTypes())
                && !empty($event->availableTicketTypes())
                && $event->registrationsOpen()
                && $event->freeSpots() > 0
            ) {
                $user = wp_get_current_user();
                include dirname(__FILE__, 2) . '/views/registration-form.php';
            } else {
                include dirname(__FILE__, 2) . '/views/registrations-closed.php';
            }
            $content .= ob_get_clean();
        }
        return $content;
    }

    /**
     * Show error and success messages
     *
     * @param string $content
     * @return string
     */
    public function showMessages(string $content) : string
    {
        $content = $this->errors() . $content;
        $content = $this->successMessage() . $content;
        return $content;
    }

    /**
     * Get success message
     *
     * @return string
     */
    public function successMessage() : string
    {
        if (!isset($_GET['registration_success']) || $_GET['registration_success'] !== 'true') {
            return '';
        }

        $message = apply_filters(
            'otomaties_events_registration_successful',
            __('Registration successful', 'otomaties-events')
        );
        ob_start();
        include apply_filters(
            'otomaties_events_notification_success',
            dirname(__FILE__, 2) . '/views/notifications/success.php',
            $message
        );
        $successMessage = ob_get_clean();
        return $successMessage;
    }

    /**
     * Get error messages
     *
     * @return string
     */
    private function errors() : string
    {
        if (!in_the_loop() || !isset($_SESSION['registration_errors']) || empty($_SESSION['registration_errors'])) {
            return '';
        }

        $message = '<ul class="mb-0">';
        foreach ($_SESSION['registration_errors'] as $error) {
            $message .= '<li>' . esc_html($error) . '</li>';
        }
        $message .= '</ul>';
        ob_start();
        include apply_filters(
            'otomaties_events_notification_error',
            dirname(__FILE__, 2) . '/views/notifications/error.php',
            $message
        );
        $error = ob_get_clean();
        unset($_SESSION['registration_errors']);
        return $error;
    }

    public function startSession() : void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
