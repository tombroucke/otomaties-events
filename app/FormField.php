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

    public function label() {
        return $this->label;
    }

    public function type() {
        return $this->type;
    }

    public function required() {
        return $this->required;
    }

    public function slug() {
        return sanitize_title($this->label);
    }

    public function render() {
        $output = '<label for="' . $this->slug() . '">' . $this->label . ($this->required ? ' <span class="text-danger">*</span>' : '') . '</label>';
        if ($this->type == 'textarea') {
            $output .= '<textarea name="extra_fields[' . $this->slug() . ']" class="form-control" ' . ($this->required ? 'required' : '') . '></textarea>';
        } else {
            $output .= '<input type="' . $this->type . '" name="extra_fields[' . $this->slug() . ']" ' . ($this->required ? 'required' : '') . ' />';
        }
        echo $output;
    }
}
