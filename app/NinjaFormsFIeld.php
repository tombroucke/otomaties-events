<?php

namespace Otomaties\Events;

/**
* Fired during plugin activation.
*
* This class defines all code necessary to run during the plugin's activation.
*
*/

class NinjaFormsField
{
    public function get_id()
    {
        return 'index_55';
    }
    
    public function get_settings()
    {
        return [
            'objectType' => 'Field',
            'objectDomain' => 'fields',
            'editActive' => false,
            'order' => -1,
            'idAttribute' => 'id',
            'label' => 'Tickets',
            'type' => 'number',
            'key' => 'number_2861733481269',
            'label_pos' => 'default',
            'required' => true,
            'default' => 1,
            'placeholder' => '',
            'container_class' => '',
            'element_class' => '',
            'input_limit' => '',
            'manual_key' => false,
            'admin_label' => '',
            'help_text' => '',
            'num_min' => '',
            'num_max' => '',
            'num_step' => 1,
            'value' => '',
        ];
    }

    public function get_setting(string $key) {
        return $this->get_settings()[$key];
    }
}
