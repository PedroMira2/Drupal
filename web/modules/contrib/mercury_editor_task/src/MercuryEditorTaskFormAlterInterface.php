<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for Mercury Editor Task form alter operations.
 */
interface MercuryEditorTaskFormAlterInterface {

  /**
   * Alters node forms to add Mercury Editor redirect functionality.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function formAlter(array &$form, FormStateInterface $form_state): void;

  /**
   * Form submission handler for the Save then layout button.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public static function formSubmit(array &$form, FormStateInterface $form_state): void;

}
