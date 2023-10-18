<?php

namespace Otomaties\Events;

class FormField
{
    /**
    * Form field type eg. textarea, number, text
    *
    * @var string
    */
    private string $type;
    
    /**
    * Form field label
    *
    * @var string
    */
    private string $label;
    
    /**
    * Whether the form field is required
    *
    * @var boolean
    */
    private bool $required;
    
    /**
    * Form field options
    *
    * @var array<string, string>
    */
    private array $options;
    
    /**
    * Setup form field
    *
    * @param array<string, mixed> $fieldsettings
    */
    public function __construct(array $fieldsettings)
    {
        $defaults = [
            'field_type' => 'text',
            'label' => '',
            'required' => false,
            'options' => [],
        ];
        $settings = wp_parse_args($fieldsettings, $defaults);
        $this->type = $settings['field_type'];
        $this->label = $settings['label'];
        $this->options = $settings['options'] ?: [];
        $this->required = filter_var($settings['required'], FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
    * Get field label
    *
    * @return string
    */
    public function label() : string
    {
        return $this->label;
    }
    
    /**
    * Get field type
    *
    * @return string
    */
    public function type() : string
    {
        return $this->type;
    }
    
    /**
    * Test if field is required
    *
    * @return boolean
    */
    public function required() : bool
    {
        return $this->required;
    }
    
    /**
    * Get field slug
    *
    * @return string
    */
    public function slug() : string
    {
        return sanitize_title($this->label);
    }
    
    /**
    * Get field options
    *
    * @return array<string, string>
    */
    public function options() : array
    {
        return $this->options;
    }
    
    public function optionValue(string $key) : ?string
    {
        $options = $this->options();
        $foundOptions = array_filter($options, function ($option) use ($key) {
            return $option['key'] == $key;
        });
        return $foundOptions ? reset($foundOptions)['label'] : null;
    }
    
    /**
    * Render field
    *
    * @return void
    */
    public function render() : void
    {
        $output = '';
        
        if (apply_filters('otomaties_events_display_input_label', true)) {
            $output .= sprintf(
                '<label for="%s">%s %s</label>',
                $this->slug(),
                $this->label,
                $this->required ? ' <span class="text-danger">*</span>' : ''
            );
        }
        
        switch($this->type) {
            case 'select':
                $output .= sprintf(
                    '<select name="extra_fields[%s]" class="%s" %s>',
                    $this->slug(),
                    apply_filters('otomaties_events_input_class', 'form-select'),
                    $this->required ? 'required' : ''
                );
                foreach ($this->options() as $option) {
                    $output .= sprintf(
                        '<option value="%s">%s</option>',
                        $option['key'],
                        $option['label']
                    );
                }
                $output .= '</select>';
                break;
                case 'textarea':
                    $output .= sprintf(
                        '<textarea name="extra_fields[%s]" class="%s" placeholder="%s" %s></textarea>',
                        $this->slug(),
                        apply_filters('otomaties_events_input_class', 'form-control'),
                        $this->label,
                        $this->required ? 'required' : ''
                    );
                    break;
                    
                    default:
                    $output .= sprintf(
                        '<input type="%s" class="%s" placeholder="%s" name="extra_fields[%s]" %s>',
                        $this->type,
                        apply_filters('otomaties_events_input_class', 'form-control'),
                        $this->label,
                        $this->slug(),
                        $this->required ? 'required' : ''
                    );
                    break;
                }
                echo apply_filters('otomaties_events_form_field', $output, $this);
            }
        }
        