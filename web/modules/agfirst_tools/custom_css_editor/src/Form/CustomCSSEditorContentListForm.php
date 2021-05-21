<?php

/**
 * @file
 * Contains \Drupal\custom_css_editor\Form\CustomCSSEditorContentListForm
 */
namespace Drupal\custom_css_editor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\custom_css_editor\CustomCSS;
use Drupal\user\Entity\User;

/**
 * Configure custom_css_editor settings for this site.
 */
class CustomCSSEditorContentListForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_css_editor_content_list';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_css_editor.content_list',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('custom_css_editor.module_settings');

    $custom_css = new CustomCSS();
    $nodes = $custom_css->getContentList(true, 50);

    $form['custom_css_editor_content_list'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('Content Type'),
        $this->t('Author'),
        $this->t('Status'),
        $this->t('Operations'),
      ],
      '#empty' => t('No users found'),
      '#description' => 'If the Content type is marked as "disabled", the content has a value, ' .
        'but the content type has been disable on the Settings page.'
    ];

    foreach ($nodes as $key => $node) {
      $form['custom_css_editor_content_list'][$key]['label'] = array(
        '#markup' => $node->toLink()->toString(),
      );

      $bundle = \Drupal::entityTypeManager()->getStorage('node_type')->load($node->bundle());

      $allowed_bundles = $config->get('bundles') ?: [];
      $allowed_bundles = array_keys(array_filter($allowed_bundles));

      $form['custom_css_editor_content_list'][$key]['content_type'] = array(
        '#markup' => $bundle->label() . (!(in_array($bundle->id(), $allowed_bundles)) ? ' (disabled)' : ''),
      );

      $form['custom_css_editor_content_list'][$key]['author'] = array(
        '#markup' => User::load($node->getOwnerId())->getDisplayName(),
      );

      $form['custom_css_editor_content_list'][$key]['status'] = array(
        '#markup' => ($node->isPublished() ? $this->t('Published') : $this->t('Not Published')),
      );

      $form['custom_css_editor_content_list'][$key]['status'] = array(
        '#markup' => \Drupal::service('date.formatter')->format($node->getChangedTime(), 'short'),
      );

      $form['custom_css_editor_content_list'][$key]['operations'] = array(
        '#type' => 'dropbutton',
        '#links' => array(
          'simple_form' => array(
            'title' => $this->t('Edit'),
            'url' => Url::fromRoute('entity.node.edit_form', array('node' => $node->id())),
          ),
        ),
      );
    }

    $form['pager'] = array(
      '#type' => 'pager'
    );

    return $form;

  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }
}
