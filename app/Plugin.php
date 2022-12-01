<?php

namespace Otomaties\Events;

use Otomaties\Events\Shortcodes;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */

class Plugin
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The current version of the plugin.
     *
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The name of the plugin
     *
     * @var string
     */
    protected $pluginName;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @param array<string> $pluginData
     */
    public function __construct(array $pluginData)
    {
        $this->version = $pluginData['Version'];
        $this->pluginName = $pluginData['pluginName'];
        $this->loader = new Loader();

        $this->setLocale();
        $this->defineAdminHooks();
        $this->defineFrontendHooks();
        $this->definePostTypeHooks();
        $this->addOptionsPage();
        $this->defineMailerHooks();
        $this->addShortcodes();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     */
    private function setLocale() : void
    {

        $plugin_i18n = new I18n();

        $this->loader->addAction('plugins_loaded', $plugin_i18n, 'loadTextdomain');
    }

    /**
     * Register all of the hooks related to the admin-facing functionality
     * of the plugin.
     *
     */
    private function defineAdminHooks() : void
    {
        $admin = new Admin($this->getPluginName(), $this->getVersion());
        // $this->loader->addAction('admin_enqueue_scripts', $admin, 'enqueueStyles');
        // $this->loader->addAction('admin_enqueue_scripts', $admin, 'enqueueScripts');
        $this->loader->addAction('get_post_metadata', $admin, 'formatDateInAdminColumn', 100, 4);
        $this->loader->addAction('admin_post_event_registration', $admin, 'register');
        $this->loader->addAction('admin_post_nopriv_event_registration', $admin, 'register');
        $this->loader->addAction('add_meta_boxes', $admin, 'metaBoxes');
        $this->loader->addAction('ext-cpts/registration/filter-query/event_id', $admin, 'replaceStringHackedPostIds', 10, 3); // phpcs:ignore Generic.Files.LineLength
        $this->loader->addAction('manage_posts_extra_tablenav', $admin, 'exportBtn');
        $this->loader->addAction('admin_init', $admin, 'exportRegistrations');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     */
    private function defineFrontendHooks() : void
    {
        $frontend = new Frontend($this->getPluginName());
        // $this->loader->addAction('wp_enqueue_scripts', $frontend, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $frontend, 'enqueueScripts');
        $this->loader->addAction('pre_get_posts', $frontend, 'hidePastEvents');
        $this->loader->addFilter('the_content', $frontend, 'renderRegistrationForm');
        $this->loader->addFilter('the_content', $frontend, 'showMessages', 1);
        $this->loader->addAction('init', $frontend, 'startSession', 1);
    }

    private function definePostTypeHooks() : void
    {
        $cpts = new CustomPostTypes();
        $this->loader->addAction('init', $cpts, 'addEvent');
        $this->loader->addAction('acf/init', $cpts, 'addEventFields');
        $this->loader->addAction('init', $cpts, 'addLocation');
        $this->loader->addAction('acf/init', $cpts, 'addLocationFields');
        $this->loader->addAction('init', $cpts, 'addRegistration');
    }

    private function addOptionsPage() : void
    {
        $options = new OptionsPage();
        $this->loader->addAction('acf/init', $options, 'addOptionsPage');
        $this->loader->addAction('acf/init', $options, 'addOptionsFields');
    }

    private function defineMailerHooks() : void
    {
        $mailer = new Mailer();
        $this->loader->addAction('otomaties_events_new_registration', $mailer, 'confirmationEmail');
        $this->loader->addAction('otomaties_events_new_registration', $mailer, 'notificationEmail');
    }

    private function addShortcodes() : void
    {
        $shortcodes = new Shortcodes();
        add_shortcode('otomaties-events-registration-form', [$shortcodes, 'registrationForm']);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     */
    public function run() : void
    {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function getLoader() : Loader
    {
        return $this->loader;
    }

    public function getPluginName() : string
    {
        return $this->pluginName;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion() : string
    {
        return $this->version;
    }
}
