<?php

namespace Drupal\Tests\scheduler_content_moderation_integration\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\workflows\Entity\Workflow;

/**
 * Test covering the UnPublishedStateConstraintValidator.
 *
 * @coversDefaultClass \Drupal\scheduler_content_moderation_integration\Plugin\Validation\Constraint\UnPublishStateConstraintValidator
 *
 * @group scheduler
 */
class UnPublishedStateConstraintTest extends ConstraintTestBase {

  /**
   * Test valid scheduled publishing state to valid scheduled un-publish
   * state transitions.
   *
   * @covers ::validate
   */
  public function testValidPublishStateToUnPublishStateTransition() {
    $node = Node::create([
      'type' => 'example',
      'title' => 'Test title',
      'moderation_state' => 'draft',
      'publish_state' => 'published',
      'unpublish_state' => 'archived',
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Test an invalid un-publish transition from a nodes current moderation
   * state.
   *
   * @cover ::validate
   */
  public function testInvalidUnPublishStateTransition() {
    $node = Node::create([
      'type' => 'example',
      'title' => 'Test title',
      'moderation_state' => 'draft',
      'unpublish_state' => 'archived',
    ]);

    $violations = $node->validate();

    $this->assertCount(1, $violations);
    $this->assertEquals('The scheduled un-publishing state of <em class="placeholder">archived</em> is not a valid transition from the current moderation state of <em class="placeholder">draft</em> for this content.', $violations->get(0)->getMessage());
  }

  /**
   * Test invalid transition from scheduled published to scheduled un-published
   * state.
   *
   * @covers ::validate
   */
  public function testInvalidPublishStateToUnPublishStateTransition() {
    // @todo Implement this test.
  }

}
