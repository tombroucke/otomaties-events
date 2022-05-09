# Otomaties Events

Add event functionality to your wordpress website

## Prerequisites
- PHP 8.x
- ACF PRO

## Installation
`composer require tombroucke/otomaties-events`

The plugin could be installed by cloning this repo and performing calling `composer install` from the root directory, but there will be no updates.

## Layout

### Templates
This plugin doesn't provide any templates. You should add `archive-events.php` and `content-event.php` yourself.

### Bootstrap
The registration form uses default bootstrap classes. Following classes should be whitelisted from purgecss
- alert
- alert-danger
- alert-success
- btn
- btn-primary
- col-md-6
- form-control
- g-3
- input-group
- input-group-text
- mb-3
- mb-5
- row

### Layout filters
Some filters are provided to swap bootstrap for another css framework
- otomaties_events_section_class
- otomaties_events_input_container_class
- otomaties_events_input_class
- otomaties_events_submit_class

## Archive
You can display an archive using a custom template or whatever. To be able to query events in the past, use 'event_scope' => 'past'. Example implementation (sage):

### Event query
```php
$args = [
	'post_type' => 'event',
	'posts_per_page' => get_option('posts_per_page'),
	'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1,
	'event_scope' => 'past',
];
$eventQuery = new \WP_Query($args);
```
```php
@while($eventQuery->have_posts()) @php($eventQuery->the_post())
	@include('partials.content-event')
@endwhile
@include('partials.pagination', ['wpQuery' => $eventQuery]) // Pagination: https://github.com/tombroucke/otomaties-sage-helper/blob/master/publishes/app/View/Composers/Pagination.php, https://github.com/tombroucke/otomaties-sage-helper/blob/master/publishes/resources/views/partials/pagination.blade.php
```

## Customization

### Render registration form in content
The registration form will be appended to the page content by default.
1. `add_filter('otomaties_events_show_registration_form', '__return_false');`
2. Use shortcode `[otomaties-events-registration-form]` to display form in different section

## Todo
WPML support
