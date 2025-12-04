<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

/**
 * The Mercury Editor Task form display builder interface.
 */
interface MercuryEditorTaskFormDisplayBuilderInterface {

  /**
   * Update Mercury Editor form displays for all content types.
   *
   * @param bool $force
   *   Force the recreation for form displays for existing bundles.
   *
   * @return bool
   *   TRUE if Mercury Editor form displays for all content types have been updated.
   */
  public function updateContentTypes(bool $force = FALSE): bool;

  /**
   * Update Mercury Editor form displays for a content types.
   *
   * @param string $bundle
   *   The content bundle.
   * @param bool $force
   *   Force the recreation for form displays..
   *
   * @return bool
   *   TRUE if Mercury Editor form displays for the content bundle has been updated.
   */
  public function updateContentType(string $bundle, bool $force = FALSE): bool;

}
