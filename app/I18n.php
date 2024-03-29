<?php

namespace Otomaties\Events;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 */

class I18n
{


    /**
     * Load the plugin text domain for translation.
     *
     * @return void
     */
    public function loadTextdomain() : void
    {
        load_plugin_textdomain('otomaties-events', false, dirname(plugin_basename(__FILE__), 2) . '/languages/');
    }
}
