<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Options;

class CustomizeSetting extends \WP_Customize_Setting {
  protected function update($value) {
    $options = Options::fetch();
    $key = preg_replace('/^esaf_/', '', $this->id);

    if($key && property_exists($options, $key)) {
      $options->$key = $value;
      $options->store();
    }
  }

  protected function get_root_value($default = null) {
    $options = Options::fetch();
    $key = preg_replace('/^esaf_/', '', $this->id);

    if($key && property_exists($options, $key)) {
      return $options->$key;
    }

    return $default;
  }
}
