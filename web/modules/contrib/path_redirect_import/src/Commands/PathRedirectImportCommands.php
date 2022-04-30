<?php

namespace Drupal\path_redirect_import\Commands;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_tools\Commands\MigrateToolsCommands;
use Drupal\path_redirect_import\Form\MigrateRedirectForm;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class PathRedirectImportCommands extends MigrateToolsCommands {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * PathRedirectImportCommands constructor.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager
   *   Migration Plugin Manager service.
   * @param \Drupal\Core\Datetime\DateFormatter $dateFormatter
   *   Date formatter service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $keyValue
   *   Key-value store service.
   * @param \Drupal\Core\File\FileSystem $fileSystem
   *   File System service.
   */
  public function __construct(MigrationPluginManager $migrationPluginManager, DateFormatter $dateFormatter, EntityTypeManagerInterface $entityTypeManager, KeyValueFactoryInterface $keyValue, FileSystem $fileSystem) {
    parent::__construct($migrationPluginManager, $dateFormatter, $entityTypeManager, $keyValue);
    $this->fileSystem = $fileSystem;
  }

  /**
   * Imports the redirects defined in the CSV file passed as argument.
   *
   * @param string $file
   *   The CSV file to import.
   *
   * @command path_redirect_import:import
   * @aliases prii
   */
  public function commandName($file) {
    if (!file_exists($file)) {
      $this->logger()->error("File $file doesn't exist \n");
      exit;
    }

    $this->fileSystem->copy($file, MigrateRedirectForm::MIGRATE_FILE_PATH, FileSystemInterface::EXISTS_REPLACE);

    $this->resetStatus('path_redirect_import');

    $this->import('path_redirect_import', [
      'limit' => 0,
      'update' => TRUE,
      'force' => FALSE,
    ]);

    $this->logger()->success(dt('Achievement unlocked.'));
  }

}
