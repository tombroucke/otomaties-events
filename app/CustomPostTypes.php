<?php //phpcs:ignore
namespace Otomaties\Events;

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Create custom post types and taxonomies
 */
class CustomPostTypes
{

    /**
     * Register post type event
     */
    public function addEvent()
    {
        $postType = 'event';
        $slug = 'events';
        $postSingularName = __('Event', 'otomaties-events');
        $postPluralName = __('Events', 'otomaties-events');

        register_extended_post_type(
            $postType,
            [
                'show_in_feed' => true,
                'show_in_rest' => true,
                'labels' => $this->postTypeLabels($postSingularName, $postPluralName),
                'dashboard_activity' => true,
                'menu_icon' => 'dashicons-calendar-alt',
                'admin_cols' => [
                    'event_date' => [
                        'title'  => __('Event date', 'otomaties-events'),
                        'meta_key'    => 'date',
                    ],
                ],
            ],
            [
                'singular' => $postSingularName,
                'plural'   => $postPluralName,
                'slug'     => $slug,
            ]
        );
    }

    public function addEventFields()
    {
        $defaultLocation = get_field('event_default_location', 'option');
        $event = new FieldsBuilder('event');
        $event
            ->addDatePicker('date', [
                'label' => __('Date', 'otomaties-events'),
            ])
            ->addPostObject('location', [
                'label' => __('Location', 'otomaties-events'),
                'post_type' => 'location',
                'default_value' => $defaultLocation,
            ])
            ->addText('subscribe_form', [
                'label' => __('Subscribe form', 'otomaties-events'),
                'instructions' => __('Leave empty to use default form. You can find the default form in the options page.', 'otomaties-events'),
                'placeholder' => __('Default form', 'otomaties-events'),
            ])
            ->setLocation('post_type', '==', 'event');
        acf_add_local_field_group($event->build());
    }

    /**
     * Register post type location
     */
    public function addLocation()
    {
        $postType = 'location';
        $slug = 'locations';
        $postSingularName = __('Location', 'otomaties-events');
        $postPluralName = __('Locations', 'otomaties-events');

        register_extended_post_type(
            $postType,
            [
                'show_in_feed' => true,
                'show_in_rest' => true,
                'labels' => $this->postTypeLabels($postSingularName, $postPluralName),
                'dashboard_activity' => true,
                'show_in_menu' => 'edit.php?post_type=event',
            ],
            [
                'singular' => $postSingularName,
                'plural'   => $postPluralName,
                'slug'     => $slug,
            ]
        );
    }

    public function addLocationFields()
    {
        $location = new FieldsBuilder('location');
        $location
            ->addGoogleMap('map', [
                'label' => __('Location on map', 'otomaties-events'),
            ])
            ->addTextarea('location_information', [
                'label' => __('Location information', 'otomaties-events'),
            ])
            ->addLink('location', [
                'link' => __('Location', 'otomaties-events')
            ])
            ->setLocation('post_type', '==', 'location');
        acf_add_local_field_group($location->build());
    }

    /**
     * Translate post type labels
     *
     * @param  string $singular_name The singular name for the post type.
     * @param  string $plural_name   The plural name for the post type.
     * @return array
     */
    private function postTypeLabels($singular_name, $plural_name)
    {
        return [
            'add_new'                  => __('Add New', 'otomaties-events'),
            /* translators: %s: singular post name */
            'add_new_item'             => sprintf(__('Add New %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'edit_item'                => sprintf(__('Edit %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'new_item'                 => sprintf(__('New %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'view_item'                => sprintf(__('View %s', 'otomaties-events'), $singular_name),
            /* translators: %s: plural post name */
            'view_items'               => sprintf(__('View %s', 'otomaties-events'), $plural_name),
            /* translators: %s: singular post name */
            'search_items'             => sprintf(__('Search %s', 'otomaties-events'), $plural_name),
            /* translators: %s: plural post name to lower */
            'not_found'                => sprintf(__('No %s found.', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural post name to lower */
            'not_found_in_trash'       => sprintf(__('No %s found in trash.', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: singular post name */
            'parent_item_colon'        => sprintf(__('Parent %s:', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'all_items'                => sprintf(__('All %s', 'otomaties-events'), $plural_name),
            /* translators: %s: singular post name */
            'archives'                 => sprintf(__('%s Archives', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'attributes'               => sprintf(__('%s Attributes', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name to lower */
            'insert_into_item'         => sprintf(__('Insert into %s', 'otomaties-events'), strtolower($singular_name)),
            /* translators: %s: singular post name to lower */
            'uploaded_to_this_item'    => sprintf(__('Uploaded to this %s', 'otomaties-events'), strtolower($singular_name)),
            /* translators: %s: plural post name to lower */
            'filter_items_list'        => sprintf(__('Filter %s list', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: singular post name */
            'items_list_navigation'    => sprintf(__('%s list navigation', 'otomaties-events'), $plural_name),
            /* translators: %s: singular post name */
            'items_list'               => sprintf(__('%s list', 'otomaties-events'), $plural_name),
            /* translators: %s: singular post name */
            'item_published'           => sprintf(__('%s published.', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'item_published_privately' => sprintf(__('%s published privately.', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'item_reverted_to_draft'   => sprintf(__('%s reverted to draft.', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'item_scheduled'           => sprintf(__('%s scheduled.', 'otomaties-events'), $singular_name),
            /* translators: %s: singular post name */
            'item_updated'             => sprintf(__('%s updated.', 'otomaties-events'), $singular_name),
        ];
    }

    /**
     * Translate taxonomy labels
     *
     * @param  string $singular_name The singular name for the taxonomy.
     * @param  string $plural_name   The plural name for the taxonomy.
     * @return array
     */
    private function taxonomyLabels($singular_name, $plural_name)
    {
        return [
            /* translators: %s: plural taxonomy name */
            'search_items'               => sprintf(__('Search %s', 'otomaties-events'), $plural_name),
            /* translators: %s: plural taxonomy name */
            'popular_items'              => sprintf(__('Popular %s', 'otomaties-events'), $plural_name),
            /* translators: %s: plural taxonomy name */
            'all_items'                  => sprintf(__('All %s', 'otomaties-events'), $plural_name),
            /* translators: %s: singular taxonomy name */
            'parent_item'                => sprintf(__('Parent %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'parent_item_colon'          => sprintf(__('Parent %s:', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'edit_item'                  => sprintf(__('Edit %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'view_item'                  => sprintf(__('View %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'update_item'                => sprintf(__('Update %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'add_new_item'               => sprintf(__('Add New %s', 'otomaties-events'), $singular_name),
            /* translators: %s: singular taxonomy name */
            'new_item_name'              => sprintf(__('New %s Name', 'otomaties-events'), $singular_name),
            /* translators: %s: plural taxonomy name to lower */
            'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural taxonomy name to lower */
            'add_or_remove_items'        => sprintf(__('Add or remove %s', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural taxonomy name to lower */
            'choose_from_most_used'      => sprintf(__('Choose from most used %s', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural taxonomy name to lower */
            'not_found'                  => sprintf(__('No %s found', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural taxonomy name to lower */
            'no_terms'                   => sprintf(__('No %s', 'otomaties-events'), strtolower($plural_name)),
            /* translators: %s: plural taxonomy name */
            'items_list_navigation'      => sprintf(__('%s list navigation', 'otomaties-events'), $plural_name),
            /* translators: %s: plural taxonomy name */
            'items_list'                 => sprintf(__('%s list', 'otomaties-events'), $plural_name),
            'most_used'                  => 'Most Used',
            /* translators: %s: plural taxonomy name */
            'back_to_items'              => sprintf(__('&larr; Back to %s', 'otomaties-events'), $plural_name),
            /* translators: %s: singular taxonomy name to lower */
            'no_item'                    => sprintf(__('No %s', 'otomaties-events'), strtolower($singular_name)),
            /* translators: %s: singular taxonomy name to lower */
            'filter_by'                  => sprintf(__('Filter by %s', 'otomaties-events'), strtolower($singular_name)),
        ];
    }
}
