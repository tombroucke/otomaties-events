<?php //phpcs:ignore
namespace Otomaties\Events;

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Add ACF options pages
 */
class OptionsPage
{

    /**
     * Create pages
     */
    public function addOptionsPage()
    {
        
        acf_add_options_page(
            array(
                'page_title'    => __('Events settings', 'otomaties-events'),
                'menu_title'    => __('Events settings', 'otomaties-events'),
                'menu_slug'     => 'events-settings',
                'capability'    => 'edit_posts',
                'redirect'      => false,
                'parent_slug'   => 'edit.php?post_type=event'
            )
        );
    }

    /**
     * Add options fields
     *
     * @return void
     */
    public function addOptionsFields() : void
    {

        $eventsSettings = new FieldsBuilder('events-settings', [
            'title' => __('Settings', 'otomaties-events')
        ]);
        
        $eventsSettings
            ->addPostObject('event_default_location', [
                'label' => __('Default location', 'otomaties-events'),
                'post_type' => 'location',
                'allow_null' => true,
            ])
            ->addTrueFalse('events_hide_past_events', [
                'label' => __('Hide past events', 'otomaties-events'),
                'instructions' => __('Events will still be visible from the backend', 'otomaties-events'),
                'default_value' => false,
                'message' => __('Hide past events from visitors', 'otomaties-events'),
            ])
            ->setLocation('options_page', '==', 'events-settings');
        acf_add_local_field_group($eventsSettings->build());
    }
}
