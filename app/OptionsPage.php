<?php //phpcs:ignore
namespace Otomaties\Events;

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Add ACF options pages
 */
class OptionsPage
{

    /**
     * Create option page
     *
     * @return void
     */
    public function addOptionsPage() : void
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
            ->addTab('general', [
                'label' => __('General', 'otomaties-events')
            ])
            ->addText('otomaties_events_events_archive_slug', [
                'label' => __('Archive slug', 'otomaties-events'),
                'default_value' => __('events', 'otomaties-events'),
                'instructions' => sprintf(
                    __('<a href="%s">Re-save permalinks</a> after updating this value', 'otomaties-events'),
                    admin_url('options-permalink.php')
                )
            ])
            ->addPostObject('otomaties_events_event_default_location', [
                'label' => __('Default location', 'otomaties-events'),
                'post_type' => 'location',
                'allow_null' => true,
            ])
            ->addTrueFalse('otomaties_events_hide_registrations_closed_notice', [
                'label' => __('Hide registrations closed notice', 'otomaties-events'),
                'default_value' => false,
                'message' => __('Hide "Registrations are closed" notice', 'otomaties-events'),
            ])
            ->addTrueFalse('otomaties_events_default_merge_extra_form_fields', [
                'label' => __('Merge extra registration form fields', 'otomaties-events'),
                'default_value' => false,
                'message' => __(
                    'Merge extra registration form fields with default registration form fields',
                    'otomaties-events'
                ),
                'instructions' => __('Can be overriden per event', 'otomaties-events'),
            ])
            ->addTrueFalse('otomaties_events_default_hide_tickets_title', [
                'label' => __('Hide tickets title', 'otomaties-events'),
                'default_value' => false,
                'message' => __('Hide tickets title on registration form', 'otomaties-events'),
                'instructions' => __('Can be overriden per event', 'otomaties-events')
            ])
            ->addTab('email', [
                'label' => __('E-mail', 'otomaties-events')
            ])
            ->addAccordion('confirmation_email', [
                'label' => __('Confirmation e-mail', 'otomaties-events')
            ])
            ->addTrueFalse('otomaties_events_enable_confirmation_email', [
                'label' => __('Enable/disable', 'otomaties-events'),
                'message' => __('Enable confirmation e-mail', 'otomaties-events'),
                'default_value' => 1
            ])
            ->addText('otomaties_events_confirmation_from_name', [
                'label' => __('Reply-to name', 'otomaties-events'),
                'conditional_logic' => [
                    'field' => 'otomaties_events_enable_confirmation_email',
                    'operator' => '==',
                    'value' =>'1'
                ],
            ])
            ->addEmail('otomaties_events_confirmation_from_email', [
                'label' => __('Reply-to e-mail', 'otomaties-events'),
                'conditional_logic' => [
                    'field' => 'otomaties_events_enable_confirmation_email',
                    'operator' => '==',
                    'value' =>'1'
                ],
            ])
            ->addText('otomaties_events_confirmation_email_subject', [
                'label' => __('Subject', 'otomaties-events'),
                'instructions' => sprintf(
                    __('Available merge tags: %s', 'otomaties-events'),
                    implode(', ', Mailer::mergeTags())
                ),
                'default_value' => __('Registration confirmation', 'otomaties-events')
            ])
            ->addWysiwyg('otomaties_events_confirmation_email', [
                'label' => __('Confirmation e-mail content', 'otomaties-events'),
                'instructions' => sprintf(
                    __('Available merge tags: %s', 'otomaties-events'),
                    implode(', ', Mailer::mergeTags())
                ),
                'conditional_logic' => [
                    'field' => 'otomaties_events_enable_confirmation_email',
                    'operator' => '==',
                    'value' =>'1'
                ],
                'default_value' => __("Hi {first_name},\n\nThanks for subscribing for {event} on {event_date}.\n\n<h3>Tickets:</h3>\n\n{ticket_table}\n\nKind regards", 'otomaties-events') // phpcs:ignore Generic.Files.LineLength
            ])
            ->addAccordion('confirmation_email_end')->endpoint()
            ->addAccordion('notification_email', [
                'label' => __('Notification e-mail', 'otomaties-events')
            ])
            ->addTrueFalse('otomaties_events_enable_notification_email', [
                'label' => __('Enable/disable', 'otomaties-events'),
                'message' => __('Enable notification e-mail', 'otomaties-events'),
                'default_value' => 1
            ])
            ->addEmail('otomaties_events_notification_recipients', [
                'label' => __('Recipient e-mail.', 'otomaties-events'),
                'instructions' => __('Separate multiple e-mailaddresses with comma\'s', 'otomaties-events'),
                'conditional_logic' => [
                    'field' => 'otomaties_events_enable_notification_email',
                    'operator' => '==',
                    'value' =>'1'
                ],
                'default_value' => get_bloginfo('admin_email'),
            ])
            ->addText('otomaties_events_notification_email_subject', [
                'label' => __('Subject', 'otomaties-events'),
                'instructions' => sprintf(
                    __('Available merge tags: %s', 'otomaties-events'),
                    implode(', ', Mailer::mergeTags())
                ),
                'default_value' => __('New registration for {event}', 'otomaties-events')
            ])
            ->addWysiwyg('otomaties_events_notification_email', [
                'label' => __('Notification e-mail content', 'otomaties-events'),
                'instructions' => sprintf(
                    __('Available merge tags: %s', 'otomaties-events'),
                    implode(', ', Mailer::mergeTags())
                ),
                'conditional_logic' => [
                    'field' => 'otomaties_events_enable_notification_email',
                    'operator' => '==',
                    'value' =>'1'
                ],
                'default_value' => __("Hi,\n\nThere is a new registration for {event} on {event_date}.\n\nName: {first_name} {last_name}\nEmail: {email}\nPhone: {phone}\nCustom fields: \n{custom_fields}\n\n<h3>Tickets:</h3>\n{ticket_table}\n\nKind regards", 'otomaties-events')// phpcs:ignore Generic.Files.LineLength
            ])
            ->addAccordion('notification_email_end')->endpoint()
            ->setLocation('options_page', '==', 'events-settings');
        acf_add_local_field_group($eventsSettings->build());
    }
}
