<?php

namespace Otomaties\Events;

class FormField
{

    private $type;
    private $label;
    private $required;

    public function __construct(array $fieldsettings)
    {
        $defaults = [
            'field_type' => 'text',
            'label' => '',
            'required' => false,
        ];
        $settings = wp_parse_args($fieldsettings, $defaults);
        $this->type = $settings['field_type'];
        $this->label = $settings['label'];
        $this->required = $settings['required'];
    }

    public function label()
    {
        return $this->label;
    }

    public function type()
    {
        return $this->type;
    }

    public function required()
    {
        return $this->required;
    }

    public function slug()
    {
        return sanitize_title($this->label);
    }

    public function render()
    {
        $output = sprintf(
            '<label for="%s">%s %s</label>',
            $this->slug(),
            $this->label,
            $this->required ? ' <span class="text-danger">*</span>' : ''
        );
        if ($this->type == 'textarea') {
            $output .= sprintf(
                '<textarea name="extra_fields[%s]" class="%s" %s></textarea>',
                $this->slug(),
                apply_filters('otomaties_events_input_class', 'form-control'),
                $this->required ? 'required' : ''
            );
        } else {
            $output .= sprintf(
                '<input type="%s" class="%s" name="extra_fields[%s]" %s>',
                $this->type,
                apply_filters('otomaties_events_input_class', 'form-control'),
                $this->slug(),
                $this->required ? 'required' : ''
            );
        }
        echo apply_filters('otomaties_events_form_field', $output, $this);
    }
}
