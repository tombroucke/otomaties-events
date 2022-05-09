<?php

namespace Otomaties\Events;

use Otomaties\Events\Models\Event;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 */

class Shortcodes
{
    public function registrationForm($atts) {
        $atts = shortcode_atts( array(
            'event' => get_the_ID(),
        ), $atts, 'otomaties-events-registration-form');


        $event = new Event($atts['event']);
        ob_start();
        if (!empty($event->ticketTypes()) && $event->registrationsOpen() && $event->freeSpots() > 0) {
            $user = wp_get_current_user();
            include dirname(__FILE__, 2) . '/views/registration-form.php';
        } else {
            include dirname(__FILE__, 2) . '/views/registrations-closed.php';
        }
        return ob_get_clean();
    }
}
