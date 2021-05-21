<?php

namespace Drupal\custom_css_editor;

use Drupal\node\Entity\Node;
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
   * @param bool $pager
   * @param int $limit
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getContentList($pager = FALSE, $limit = 10) {

    $db = \Drupal::database();
    $query = $db->select('custom_css_data', 'ccd');
    $query->fields('ccd');

    if ($pager) {
      $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);
      $results = $pager->execute()->fetchAll();
    } else {
      $results = $query->execute()->fetchAll();
    }

    $nids = [];
    foreach($results as $record) {
      if (!empty(trim($record->css_code))) {
        $nids[] = $record->nid;
      }
    }

    return Node::loadMultiple($nids);

  }

  public function getBundles() {

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
