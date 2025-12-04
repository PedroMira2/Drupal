<?php

namespace Drupal\mercury_editor_task;

/**
 * Interface for MercuryEditorTasksInlineEntityForm service.
 */
interface MercuryEditorTasksInlineEntityFormInterface {

  /**
   * Alters IEF table variables to add a Mercury Editor link.
   *
   * @param array $table
   *   The table array to modify.
   */
  public function addMercuryEditorLinks(array &$table): void;

}
