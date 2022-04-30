<?php

namespace Drupal\path_redirect_import\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\path_redirect_import\MigratePluginTrait;
use League\Csv\Writer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to export redirects.
 */
class ExportRedirectForm extends FormBase {
  use MigratePluginTrait;

  const MIGRATE_FOLDER = 'public://path_redirect_import/';
  const BATCH_SIZE = 50;
  const HEADERS = ['source', 'destination', 'language', 'status_code'];

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a MigrateRedirectForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_plugin_manager
   *   The plugin manager for config entity-based migrations.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The file system.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MigrationPluginManagerInterface $migration_plugin_manager, FileSystemInterface $file_system, Messenger $messenger, AccountInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->migrationPluginManager = $migration_plugin_manager;
    $this->fileSystem = $file_system;
    $this->messenger = $messenger;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.migration'),
      $container->get('file_system'),
      $container->get('messenger'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'migrate_redirect_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['markup'] = [
      '#markup' => 'A CSV will be exported with this structure:',
    ];

    $form['pre'] = [
      '#type' => 'html_tag',
      '#tag' => 'pre',
      '#value' => 'source,destination,language,status_code
source-path,&lt;front&gt;,und,301
source-path-other?param=value,/my-path,en,302
my-source-path,https://example.com,und',
    ];

    $form['actions'] = [
      '#type' => 'actions',
      '#weight' => 100,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export data'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No need to validate the form;.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Nothing to send from form for now.
    $this->batchPreparation();
  }

  /**
   * Creates the spreadsheet file to export entries to.
   *
   * @return \Drupal\file\FileInterface|false
   *   File.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getFile() {
    $filename = 'export_' . time() . '.csv';
    $uri = self::MIGRATE_FOLDER . $filename;
    $directory = self::MIGRATE_FOLDER;
    $result = $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    return file_save_data('', $uri, FileSystemInterface::EXISTS_REPLACE);
  }

  /**
   * Get the CSV writer.
   *
   * @param string $file_path
   *   Path of the file.
   * @param string $mode
   *   Mode to open the file.
   * @param array $configuration
   *   Array with CSV configuration.
   *
   * @return \League\Csv\Writer
   *   The writer.
   *
   * @throws \Drupal\migrate\MigrateException
   * @throws \League\Csv\Exception
   */
  protected static function createWriter($file_path, $mode, array $configuration) {
    $writer = Writer::createFromPath($file_path, $mode);

    $writer->setDelimiter($configuration['delimiter']);
    $writer->setEnclosure($configuration['enclosure']);
    $writer->setEscape($configuration['escape']);

    return $writer;
  }

  /**
   * Load the same configuration of migrate for CSV=>Table import path.
   *
   * @return array
   *   Configuration for csv info, separators, etc.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getConfigurationFromPlugin() {
    $migration_plugin = $this->migrationPlugin();

    return $migration_plugin->getSourcePlugin()->getConfiguration();
  }

  /**
   * Batch processing function.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function batchPreparation() {
    // Create file, folder and prepare variables.
    $file = $this->getFile();
    $configuration = $this->getConfigurationFromPlugin();

    // Load a list of all ids to process.
    $ids = $this->entityTypeManager->getStorage('redirect')->getQuery()->execute();

    // Breakdown process into small batches.
    $operations = [];
    $item_start = 0;
    foreach (array_chunk($ids, self::BATCH_SIZE) as $batch_data) {
      $operations[] = [
        get_class($this) . '::batchProcessExport',
        [$file, $configuration, $batch_data, $item_start, count($ids)],
      ];
      $item_start += self::BATCH_SIZE;
    }

    if (count($operations) > 0) {
      $batch = [
        'operations' => $operations,
        'title' => $this->t('Exporting redirect entities to file'),
        'init_message' => $this->t('Process started.'),
        'progress_message' => $this->t('Exporting...'),
        'error_message' => $this->t('An error occurred while exporting redirect entities.'),
        'finished' => get_class($this) . '::batchFinishedExport',
      ];

      batch_set($batch);
    }
    else {
      $this->messenger->addError(t('There are no redirections to export.'));
    }
  }

  /**
   * Export into the CSV per batches.
   *
   * @param \Drupal\file\entity\File $file
   *   File where information will be stored to.
   * @param array $configuration
   *   Configuration for CSV structure.
   * @param array $batch_data
   *   Ids of redirect entities to process.
   * @param int $start
   *   Next item to process.
   * @param int $total
   *   Total of items to process.
   * @param array $context
   *   Context array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function batchProcessExport(File $file, array $configuration, array $batch_data, $start, $total, array &$context) {
    $file_path = \Drupal::service('file_system')->realpath($file->getFileUri());

    if (empty($context['sandbox'])) {
      $context['finished'] = 0;
      $context['sandbox'] = [];
      $context['sandbox']['total'] = $total;
      $context['sandbox']['counter'] = $start;
      $context['results']['failures'] = isset($context['results']['failures']) ? $context['results']['failures'] : 0;
      $context['results']['file'] = $file;
      // Write header in first iteration of first batch.
      if (empty($start)) {
        // Write header.
        $writer = self::createWriter($file_path, 'w', $configuration);
        $writer->insertOne(self::HEADERS);
      }
    }

    $storage_handler = \Drupal::entityTypeManager()->getStorage('redirect');
    $entities = $storage_handler->loadMultiple($batch_data);

    if (empty($writer)) {
      $writer = self::createWriter($file_path, 'a', $configuration);
    }

    // Now export entities one by one, only with the fields we need.
    /** @var \Drupal\redirect\Entity\Redirect $redirect_entity */
    foreach ($entities as $redirect_entity) {
      try {
        $source = $redirect_entity->getSourceUrl();
        $redirect = $redirect_entity->getRedirectUrl();
        $lang = $redirect_entity->get('language')->value;
        $status_code = $redirect_entity->get('status_code')->value;
        $writer->insertOne([
          $source,
          $redirect->toString(),
          $lang,
          $status_code,
        ]);
      }
      catch (\Exception $e) {
        $context['results']['failures']++;
      }
      $context['sandbox']['counter']++;
    }

    if ($context['sandbox']['counter'] >= $context['sandbox']['total']) {
      $context['finished'] = 1;
      $context['results']['processed'] = $context['sandbox']['counter'];
    }
    else {
      $context['message'] = t('Exporting (@percent%).', [
        '@percent' => (int) (($context['sandbox']['counter'] / $context['sandbox']['total']) * 100),
      ]);
    }
  }

  /**
   * Finished callback for import batches.
   *
   * @param bool $success
   *   A boolean indicating whether the batch has completed successfully.
   * @param array $results
   *   The value set in $context['results'] by callback_batch_operation().
   * @param array $operations
   *   If $success is FALSE, contains the operations that remained unprocessed.
   */
  public static function batchFinishedExport($success, array $results, array $operations) {
    /** @var \Drupal\file\Entity\File $file */
    $file = !empty($results['file']) ? $results['file'] : NULL;
    if ($success && !empty($file)) {
      $uri = file_create_url($file->getFileUri());
      $url = Url::fromUri($uri);
      $download = Link::fromTextAndUrl(t('link'), $url);
      \Drupal::messenger()->addStatus(t('Export process finished. You may download the file through this %link', ['%link' => $download->toString()]));
      if (isset($results['failures']) && isset($results['processed'])) {
        \Drupal::messenger()->addStatus(t('Processed @processed items. Exported: @correct, failures: @failures',
          [
            '@processed' => $results['processed'],
            '@correct' => $results['processed'] - $results['failures'],
            '@failures' => $results['failures'],
          ]));
      }
    }
    else {
      \Drupal::messenger()->addError(t('Export process failed. Please review existing redirections or contact an administrator.'));
    }
    // In any other case, set file as temporary so that cron deletes it.
    if (!empty($file)) {
      $file->setTemporary();
      $file->save();
    }
  }

}
