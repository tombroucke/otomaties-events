<?php

namespace Otomaties\Events;

/**
 * Plugin Name:       Otomaties Events
 * Description:       Add event functionality to your website
 * Version:           1.10.1
 * Author:            Tom Broucke
 * Author URI:        https://tombroucke.be/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       otomaties-events
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

// Autoload files
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once realpath(__DIR__ . '/vendor/autoload.php');
}

// Setup / teardown
register_activation_hook(__FILE__, '\\Otomaties\\Events\\Activator::activate');
register_deactivation_hook(__FILE__, '\\Otomaties\\Events\\Deactivator::deactivate');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function init()
{
    if (!function_exists('get_field')) {
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error">
                <p><?php _e('<strong>Otomaties events</strong> is inactive. Please install & activate <strong>Advanced Custom Fields Pro</strong>.', 'otomaties-events') ?></p>
            </div>
            <?php
        });
        return;
    }

    if (! function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $pluginData = \get_plugin_data(__FILE__, false, false);
    $pluginData['pluginName'] = basename(__FILE__, '.php');

    $plugin = new Plugin($pluginData);
    $plugin->run();
}
init();
