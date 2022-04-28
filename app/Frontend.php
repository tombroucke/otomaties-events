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
     * The version of this plugin.
     *
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $pluginName       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($pluginName, $version)
    {

        $this->pluginName = $pluginName;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style($this->pluginName, Assets::find('css/main.css'), array(), null);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script($this->pluginName, Assets::find('js/main.js'), array( 'jquery' ), null);
    }

    public function hidePastEvents($query)
    {

        if ($query->get('post_type') != 'event' || is_admin()) {
            return;
        }
        
        
        if (get_field('events_hide_past_events', 'option')) {
            $meta_query = array_filter((array)$query->get('meta_query'));
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'date',
                        'value' => date('Ymd'),
                        'compare' => '>='
                    ),
                    array(
                        'key' => 'date',
                        'compare'=>'NOT EXISTS',
                    )
                )
            );
            $query->set('meta_query', $meta_query);
        }

        $query->set('meta_key', 'date');
        $query->set('orderby', array( 'meta_value' => 'ASC' ));
    }

    public function renderSubscriptionForm($content)
    {
        if (is_singular('event')) {
            $event = new Event(get_the_ID());
            ob_start();
            include dirname(__FILE__, 2) . '/views/registration-form.php';

            $content .= ob_get_clean();
        }
        return $content;
    }
}
