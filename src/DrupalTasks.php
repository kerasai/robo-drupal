<?php

namespace Kerasai\RoboDrupal;

use Kerasai\Robo\Config\ConfigHelperTrait;

/**
 * Drupal Robo tasks.
 */
trait DrupalTasks {

  use ConfigHelperTrait;

  /**
   * Prepares files directory.
   */
  public function installPrepareFilesDir() {
    $collection = $this->collectionBuilder();

    foreach (['public', 'private'] as $type) {
      if (!$dir = $this->getConfigVal("drupal.files.$type.path")) {
        $this->say("No $type files defined (drupal.files.$type.path).");
        continue;
      }

      $collection->taskExec("mkdir -p $dir");

      if ($owner = $this->getConfigVal("drupal.files.$type.owner")) {
        $collection->taskExec("chown -R $owner $dir");
      }
      if ($group = $this->getConfigVal("drupal.files.$type.group")) {
        $collection->taskExec("chgrp -R $group $dir");
      }

      $collection->taskExecStack()
        ->exec("find $dir -type d -exec chmod 2775 {} \\;")
        ->exec("find $dir -type f -exec chmod 0644 {} \\;");
    }

    return $collection;
  }

  /**
   * Install from existing config.
   */
  public function installExisting() {
    return $this->taskExec($this->getDrushCmd() . ' si -y --existing-config');
  }

  /**
   * Gets the command to run drush.
   *
   * @return string
   *   The drush command.
   */
  protected function getDrushCmd() {
    return $this->getConfigVal("drupal.drush", 'vendor/bin/drush');
  }

}
