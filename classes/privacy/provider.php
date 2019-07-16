<?php

namespace report_cmcompetency\privacy;
defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;

class provider implements 
    \core_privacy\local\metadata\provider, 
    \core_privacy\local\metadata\null_provider {
    
  public static function get_metadata(collection $collection) : collection {
    return $collection;
  }

  /**
   * Get the language string identifier with the component's language
   * file to explain why this plugin stores no data.
   *
   * @return  string
   */
  public static function get_reason() : string {
      return 'privacy:metadata';
  }

}
