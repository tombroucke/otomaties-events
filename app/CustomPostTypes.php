<?php //phpcs:ignore
namespace Otomaties\Events;

use Otomaties\Events\Models\Event;
use Otomaties\Events\Models\Registration;
use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Create custom post types and taxonomies
 */
class CustomPostTypes
{

    /**
     * Register post type event
     *
     * @return void
     */
    public function addEvent() : void
    {
        $postType = 'event';
        $slug = 'events';
        $postSingularName = __('Event', 'otomaties-events');
        $postPluralName = __('Events', 'otomaties-events');

        register_extended_post_type(
            $postType,
            [
                'show_in_feed' => apply_filters('otomaties_events_show_in_feed', true),
                'show_in_rest' => apply_filters('otomaties_events_show_in_rest', true),
                'labels' => $this->postTypeLabels($postSingularName, $postPluralName),
                'dashboard_activity' => true,
                'has_archive' => apply_filters('otomaties_events_has_archive', true),
                'menu_icon' => 'dashicons-calendar-alt',
                'supports' => ['title', 'editor', 'author', 'thumbnail', 'revision', 'excerpt'],
                'map_meta_cap' => true,
                'capabilities' => apply_filters('otomaties_events_event_capabilities', [
                    'edit_post' => 'edit_post',
                    'read_post' => 'read_post',
                    'delete_post' => 'delete_post',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'publish_posts' => 'publish_posts',
                    'read_private_posts' => 'read_private_posts',
                    'delete_private_posts' => 'delete_private_posts',
                    'delete_published_posts' => 'delete_published_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'edit_private_posts' => 'edit_private_posts',
                    'edit_published_posts' => 'edit_published_posts',
                ]),
                'admin_cols' => [
                    'event_date' => [
                        'title'  => __('Event date', 'otomaties-events'),
                        'meta_key'    => 'date',
                        'default'     => 'DESC'
                    ],
                    'registrations' => [
                        'title'  => __('Registrations', 'otomaties-events'),
                        'function'    => function () {
                            $event = new Event(get_the_ID());
                            $registrationCount = count($event->registrations());
                            $color = $event->registrationsOpen() ? '#7ad03a' : '#dc3232';
                            echo sprintf(
                                '<span style="color: %s;">&#11044;</span> <a href="%s">%s</a>',
                                $color,
                                admin_url('edit.php?post_type=registration&event_id=event_' . get_the_ID()),
                                sprintf(
                                    _n(
                                        'View %s registration',
                                        'View %s registrations',
                                        $registrationCount,
                                        'otomaties-events'
                                    ),
                                    $registrationCount
                                )
                            );
                        }
                    ],
                    'tickets' => [
                        'title'  => __('# Tickets sold', 'otomaties-events'),
                        'function'    => function () {
                            $event = new Event(get_the_ID());
                            echo $event->soldTickets();
                        }
                    ]
                ],
                'rewrite' => [
                    'slug' => get_field('otomaties_events_events_archive_slug', 'option') ?: 'events',
                    'with_front' => false,
                ]
            ],
            [
                'singular' => $postSingularName,
                'plural'   => $postPluralName,
                'slug'     => $slug,
            ]
        );
    }

    /**
     * Add acf fields to event
     *
     * @return void
     */
    public function addEventFields() : void
    {
        $defaultLocation = get_field('otomaties_events_event_default_location', 'option');
        $event = new FieldsBuilder(
            'event',
            [
                'position' => 'acf_after_title',
                'title' => __('Details', 'otomaties-events')
            ]
        );
        $event
            ->addTab('general', [
                'label' => __('General', 'otomaties-events'),
            ])
            ->addDatePicker('date', [
                'label' => __('Date', 'otomaties-events'),
                'return_format' => 'Ymd',
            ])
            ->addTimePicker('time', [
                'label' => __('Time', 'otomaties-events'),
                'display_format' => get_option('time_format'),
            ])
            ->addDatePicker('date_to', [
                'label' => __('Date to', 'otomaties-events'),
                'return_format' => 'Ymd',
            ])
            ->addTimePicker('time_to', [
                'label' => __('Time to', 'otomaties-events'),
                'display_format' => get_option('time_format'),
            ])
            ->addPostObject('location', [
                'label' => __('Location', 'otomaties-events'),
                'post_type' => 'location',
                'default_value' => $defaultLocation,
            ])
            ->addTab('registration', [
                'label' => __('Registration', 'otomaties-events'),
            ])
            ->addMessage('registation_message', __('Registration will only be active if tickets have been added', 'otomaties-events'), [ // phpcs:ignore Generic.Files.LineLength
                'label' => __('Registration information', 'otomaties-events')
            ])
            ->addNumber('registration_limit', [
                'label' => __('Maximum number of registrations', 'otomaties-events'),
                'instructions' => __('For all tickets, leave empty to disable', 'otomaties-events'),
            ])
            ->addDateTimePicker('registration_start', [
                'label' => __('Tickets available from', 'otomaties-events'),
            ])
            ->addDateTimePicker('registration_end', [
                'label' => __('Tickets available untill', 'otomaties-events'),
            ])
            ->addRepeater('ticket_types', [
                    'label' => __('Ticket types', 'otomaties-events'),
                    'instructions' => __('Be careful when deleting ticket types. Existing registrations with deleted ticket types won\'t count for maximum number of registrations.', 'otomaties-events') // phpcs:ignore Generic.Files.LineLength
                ])
                ->addText('title', [
                    'label' => __('Title', 'otomaties-events'),
                    'required' => true,
                    'placeholder' => __('Personal registration, adult, child, ...', 'otomaties-events'),
                ])
                ->addNumber('price', [
                    'label' => __('Price', 'otomaties-events'),
                ])
                ->addNumber('ticket_limit_per_registration', [
                    'label' => __('Limit number of tickets per registration', 'otomaties-events'),
                    'instructions' => __('Leave empty to disable', 'otomaties-events'),
                ])
                ->addNumber('registration_limit', [
                    'label' => __('Total sales limit for this ticket', 'otomaties-events'),
                    'instructions' => __('Leave empty to disable', 'otomaties-events'),
                ])
                ->addNumber('default_value', [
                    'label' => __('Default value', 'otomaties-events'),
                    'instructions' => __('Leave empty to disable', 'otomaties-events'),
                ])
            ->endRepeater()
            ->addTab('form', [
                'label' => __('Registration form', 'otomaties-events')
            ])
            ->addRepeater('extra_fields', [
                'label' => __('Extra form fields', 'otomaties-events'),
                'layout' => 'block',
            ])
                ->addSelect('field_type', [
                    'label' => __('Field type', 'otomaties-events'),
                    'choices' => [
                        'text' => __('Text', 'otomaties-events'),
                        'textarea' => __('Textarea', 'otomaties-events'),
                        'number' => __('Number', 'otomaties-events'),
                        'select' => __('Select', 'otomaties-events'),
                    ],
                    'required' => true
                ])
                ->addText('label', [
                    'label' => __('Label', 'otomaties-events'),
                    'required' => true
                ])
                ->addRepeater('options', [
                    'label' => __('Options', 'otomaties-events'),
                    'conditional_logic' => [
                        'field' => 'field_type',
                        'operator' => '==',
                        'value' => 'select'
                    ]
                ])
                    ->addText('key', [
                        'label' => __('key', 'otomaties-events'),
                        'required' => true
                    ])
                    ->addText('label', [
                        'label' => __('Value', 'otomaties-events'),
                        'required' => true
                    ])
                ->endRepeater()
                ->addTrueFalse('required', [
                    'label' => __('Required', 'otomaties-events'),
                ])
            ->endRepeater();

        $event->addCheckbox('disable_default_fields', [
            'label' => __('Disable form fields', 'otomaties-events'),
            'choices' => Event::defaultFields(),
            'default_value' => get_field('otomaties_events_disable_default_fields', 'option'),
        ]);

        $event
            ->addTrueFalse('merge_extra_form_fields', [
                'label' => __('Merge extra registration form fields', 'otomaties-events'),
                'default_value' => get_field('otomaties_events_default_merge_extra_form_fields', 'option'),
                'message' => __(
                    'Merge extra registration form fields with default registration form fields',
                    'otomaties-events'
                ),
                'instructions' => __('Can also be set in general settings', 'otomaties-events'),
            ])
            ->addTrueFalse('hide_tickets_title', [
                'label' => __('Hide tickets title', 'otomaties-events'),
                'default_value' => get_field('otomaties_events_default_hide_tickets_title', 'option'),
                'message' => __('Hide tickets title on registration form', 'otomaties-events'),
                'instructions' => __('Can also be set in general settings', 'otomaties-events')
            ])
            ->setLocation('post_type', '==', 'event');
        acf_add_local_field_group($event->build());
    }

    /**
     * Register post type registration
     *
     * @return void
     */
    public function addRegistration() : void
    {
        $postType = 'registration';
        $slug = 'registrations';
        $postSingularName = __('Registration', 'otomaties-events');
        $postPluralName = __('Registrations', 'otomaties-events');

        register_extended_post_type(
            $postType,
            [
                'show_in_feed' => false,
                'show_in_rest' => false,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'supports' => ['title', 'author'],
                'labels' => $this->postTypeLabels($postSingularName, $postPluralName),
                'dashboard_activity' => true,
                'has_archive' => false,
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => 'edit.php?post_type=event',
                'map_meta_cap' => true,
                'capabilities' => apply_filters('otomaties_events_registration_capabilities', [
                    'edit_post' => 'edit_post',
                    'read_post' => 'read_post',
                    'delete_post' => 'delete_post',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'publish_posts' => 'publish_posts',
                    'read_private_posts' => 'read_private_posts',
                    'delete_private_posts' => 'delete_private_posts',
                    'delete_published_posts' => 'delete_published_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'edit_private_posts' => 'edit_private_posts',
                    'edit_published_posts' => 'edit_published_posts',
                ]),
                'admin_cols' => [
                    'title' => [
                        'title'       => __('Name', 'otomaties-events'),
                    ],
                    'event' => [
                        'title'       => __('Event', 'otomaties-events'),
                        'function' => function () {
                            $registration = new Registration(get_the_ID());
                            $event = $registration->event();
                            if ($event) {
                                if ($event->eventDate()) {
                                    echo sprintf(
                                        '<a href="%s">%s %s</a>',
                                        get_edit_post_link($event->getId()),
                                        esc_html($event->title()),
                                        $event->eventDate()->format(get_option('date_format'))
                                    );
                                } else {
                                    echo sprintf(
                                        '<a href="%s">%s</a>',
                                        get_edit_post_link($event->getId()),
                                        esc_html($event->title())
                                    );
                                }
                            } else {
                                echo __('Deleted event', 'otomaties-events');
                            }
                        }
                    ],
                    'tickets' => [
                        'title'  => __('# Tickets', 'otomaties-events'),
                        'function' => function () {
                            $registration = new Registration(get_the_ID());
                            echo $registration->ticketCount();
                        }
                    ],
                    'published' => array(
                        'title'       => __('Registration date', 'otomaties-events'),
                        'post_field'  => 'post_date',
                        'default'     => 'DESC'
                    ),
                ],
                'admin_filters' => [
                    'event_id' => [
                        'meta_key' => 'event_id',
                        'title' => __('Event', 'otomaties-events'),
                        'options' => function () {
                            $events = Event::find();
                            $return = array();
                            foreach ($events as $key => $event) {
                                $return['event_' . $event->getId()] = $event->title();
                            }
                            return $return;
                        },
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

    /**
     * Register post type location
     *
     * @return void
     */
    public function addLocation() : void
    {
        $postType = 'location';
        $slug = 'locations';
        $postSingularName = __('Location', 'otomaties-events');
        $postPluralName = __('Locations', 'otomaties-events');

        register_extended_post_type(
            $postType,
            [
                'show_in_feed' => false,
                'show_in_rest' => true,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'labels' => $this->postTypeLabels($postSingularName, $postPluralName),
                'dashboard_activity' => true,
                'show_in_menu' => 'edit.php?post_type=event',
                'has_archive' => false,
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'map_meta_cap' => true,
                'capabilities' => apply_filters('otomaties_events_location_capabilities', [
                    'edit_post' => 'edit_post',
                    'read_post' => 'read_post',
                    'delete_post' => 'delete_post',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'publish_posts' => 'publish_posts',
                    'read_private_posts' => 'read_private_posts',
                    'delete_private_posts' => 'delete_private_posts',
                    'delete_published_posts' => 'delete_published_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'edit_private_posts' => 'edit_private_posts',
                    'edit_published_posts' => 'edit_published_posts',
                ]),
            ],
            [
                'singular' => $postSingularName,
                'plural'   => $postPluralName,
                'slug'     => $slug,
            ]
        );
    }

    /**
     * Add acf fields to location
     *
     * @return void
     */
    public function addLocationFields() : void
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
     * @return array<string, string>
     */
    private function postTypeLabels(string $singular_name, string $plural_name) : array
    {
        return [
            'add_new' => __('Add New', 'otomaties-events'),
            /* translators: %s: singular post name */
            'add_new_item' => sprintf(
                __('Add New %s', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'edit_item' => sprintf(
                __('Edit %s', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'new_item' => sprintf(
                __('New %s', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'view_item' => sprintf(
                __('View %s', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: plural post name */
            'view_items' => sprintf(
                __('View %s', 'otomaties-events'),
                $plural_name
            ),
            /* translators: %s: singular post name */
            'search_items' => sprintf(
                __('Search %s', 'otomaties-events'),
                $plural_name
            ),
            /* translators: %s: plural post name to lower */
            'not_found' => sprintf(
                __('No %s found.', 'otomaties-events'),
                strtolower($plural_name)
            ),
            /* translators: %s: plural post name to lower */
            'not_found_in_trash' => sprintf(
                __('No %s found in trash.', 'otomaties-events'),
                strtolower($plural_name)
            ),
            /* translators: %s: singular post name */
            'parent_item_colon' => sprintf(
                __('Parent %s:', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'all_items' => sprintf(
                __('All %s', 'otomaties-events'),
                $plural_name
            ),
            /* translators: %s: singular post name */
            'archives' => sprintf(
                __('%s Archives', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'attributes' => sprintf(
                __('%s Attributes', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name to lower */
            'insert_into_item' => sprintf(
                __('Insert into %s', 'otomaties-events'),
                strtolower($singular_name)
            ),
            /* translators: %s: singular post name to lower */
            'uploaded_to_this_item'    => sprintf(
                __('Uploaded to this %s', 'otomaties-events'),
                strtolower($singular_name)
            ),
            /* translators: %s: plural post name to lower */
            'filter_items_list' => sprintf(
                __('Filter %s list', 'otomaties-events'),
                strtolower($plural_name)
            ),
            /* translators: %s: singular post name */
            'items_list_navigation' => sprintf(
                __('%s list navigation', 'otomaties-events'),
                $plural_name
            ),
            /* translators: %s: singular post name */
            'items_list' => sprintf(
                __('%s list', 'otomaties-events'),
                $plural_name
            ),
            /* translators: %s: singular post name */
            'item_published' => sprintf(
                __('%s published.', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'item_published_privately' => sprintf(
                __('%s published privately.', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'item_reverted_to_draft' => sprintf(
                __('%s reverted to draft.', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'item_scheduled' => sprintf(
                __('%s scheduled.', 'otomaties-events'),
                $singular_name
            ),
            /* translators: %s: singular post name */
            'item_updated' => sprintf(
                __('%s updated.', 'otomaties-events'),
                $singular_name
            ),
        ];
    }

    /**
     * Translate taxonomy labels
     *
     * @param  string $singular_name The singular name for the taxonomy.
     * @param  string $plural_name   The plural name for the taxonomy.
     * @return array<string, string>
     */
    // private function taxonomyLabels($singular_name, $plural_name)
    // {
    //     return [
    //         /* translators: %s: plural taxonomy name */
    //         'search_items' => sprintf(
    //             __('Search %s', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         /* translators: %s: plural taxonomy name */
    //         'popular_items' => sprintf(
    //             __('Popular %s', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         /* translators: %s: plural taxonomy name */
    //         'all_items' => sprintf(
    //             __('All %s', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'parent_item' => sprintf(
    //             __('Parent %s', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'parent_item_colon' => sprintf(
    //             __('Parent %s:', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'edit_item' => sprintf(
    //             __('Edit %s', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'view_item' => sprintf(
    //             __('View %s', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'update_item' => sprintf(
    //             __('Update %s', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'add_new_item' => sprintf(
    //             __('Add New %s', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: singular taxonomy name */
    //         'new_item_name' => sprintf(
    //             __('New %s Name', 'otomaties-events'),
    //             $singular_name
    //         ),
    //         /* translators: %s: plural taxonomy name to lower */
    //         'separate_items_with_commas' => sprintf(
    //             __('Separate %s with commas', 'otomaties-events'),
    //             strtolower($plural_name)
    //         ),
    //         /* translators: %s: plural taxonomy name to lower */
    //         'add_or_remove_items' => sprintf(
    //             __('Add or remove %s', 'otomaties-events'),
    //             strtolower($plural_name)
    //         ),
    //         /* translators: %s: plural taxonomy name to lower */
    //         'choose_from_most_used' => sprintf(
    //             __('Choose from most used %s', 'otomaties-events'),
    //             strtolower($plural_name)
    //         ),
    //         /* translators: %s: plural taxonomy name to lower */
    //         'not_found' => sprintf(
    //             __('No %s found', 'otomaties-events'),
    //             strtolower($plural_name)
    //         ),
    //         /* translators: %s: plural taxonomy name to lower */
    //         'no_terms' => sprintf(
    //             __('No %s', 'otomaties-events'),
    //             strtolower($plural_name)
    //         ),
    //         /* translators: %s: plural taxonomy name */
    //         'items_list_navigation' => sprintf(
    //             __('%s list navigation', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         /* translators: %s: plural taxonomy name */
    //         'items_list' => sprintf(
    //             __('%s list', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         'most_used' => 'Most Used',
    //         /* translators: %s: plural taxonomy name */
    //         'back_to_items' => sprintf(
    //             __('&larr; Back to %s', 'otomaties-events'),
    //             $plural_name
    //         ),
    //         /* translators: %s: singular taxonomy name to lower */
    //         'no_item' => sprintf(
    //             __('No %s', 'otomaties-events'),
    //             strtolower($singular_name)
    //         ),
    //         /* translators: %s: singular taxonomy name to lower */
    //         'filter_by' => sprintf(
    //             __('Filter by %s', 'otomaties-events'),
    //             strtolower($singular_name)
    //         ),
    //     ];
    // }
}
