<?php

namespace Drupal\custom_css_editor;

use Exception;

class CustomCSS {

  public $nid;
  public $css_code;

  /**
   * CustomCSS constructor.
   */
  function __construct() {

  }

  /**
   * @param $nid
   */
  public function get($nid) {

    $db = \Drupal::database();
    $query = $db->select('custom_css_data', 'ccd');
    $query->fields('ccd');
    $query->condition('ccd.nid', $nid, '=');

    $result = $query->execute();

    foreach($result as $record) {
      $this->nid = $record->nid;
      $this->css_code = $record->css_code;
      break;
    }

  }

  /**
   * @param $nid
   * @param string $css_code
   * @throws Exception
   */
  public function save($nid, $css_code = '') {

    $db = \Drupal::database();
    $db->merge('custom_css_data')
      ->insertFields([
        'nid' => $nid,
        'css_code' => $css_code,
      ])
      ->updateFields([
        'css_code' => $css_code,
      ])
      ->key( 'nid', $nid)
      ->execute();

  }

}
