<?php

namespace Drupal\Tests\scheduler_content_moderation_integration\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\workflows\Entity\Workflow;

/**
 * Base class for the Scheduler Content Moderation validator constraint tests.
 */
abstract class ConstraintTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'action',
    'content_moderation',
    'datetime',
    'field',
    'node',
    'options',
    'scheduler_content_moderation_integration',
    'system',
    'user',
    'workflows',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('node', 'node_access');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('content_moderation_state');
    $this->installConfig('content_moderation');

    $this->configureExampleNodeType();
    $this->configureEditorialWorkflow();
  }

  /**
   * Configure example node type.
   */
  protected function configureExampleNodeType() {
    $node_type = NodeType::create([
      'type' => 'example',
    ]);
    $node_type->save();
  }

  /**
   * Configures the editorial workflow for the example node type.
   */
  protected function configureEditorialWorkflow() {
    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'example');
    $workflow->save();
  }

}
