<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service for handling Mercury Editor Task form alterations.
 */
class MercuryEditorTaskFormAlter implements MercuryEditorTaskFormAlterInterface {

  /**
   * Constructs a MercuryEditorTaskFormAlter object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory
  ) {}

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    // Check if the form object is an entity form.
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof EntityFormInterface) {
      return;
    }

    // Check if the entity is a node form.
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_object->getEntity();
    if (!$node instanceof NodeInterface) {
      return;
    }

    // Check if the submit button exists so we can clone it.
    if (!isset($form['actions']['submit'])) {
      return;
    }

    // Check if the form_display is set in form storage.
    $form_storage = $form_state->getStorage();
    if (!isset($form_storage['form_display'])) {
      return;
    }

    // Check that we are only altering the 'default' form display.
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
    $form_display = $form_storage['form_display'];
    if ($form_display->getMode() !== 'default') {
      return;
    }

    // Check if Mercury Editor is enabled for this content type.
    $has_mercury_editor = $this->configFactory
      ->get('mercury_editor.settings')
      ->get('bundles.node.' . $node->bundle());
    if (!$has_mercury_editor) {
      return;
    }

    // Get config options.
    $config = $this->configFactory->get('mercury_editor_task.settings');
    $redirect_label = $config->get('redirect_label');
    $redirect_create = $config->get('redirect_create');
    $redirect_update = $config->get('redirect_update');

    if (($node->isNew() && $redirect_create)
      || (!$node->isNew() && $redirect_update)) {
      // Clone the Save button and add a new save option.
      $form['actions']['save_redirect_layout'] = $form['actions']['submit'];
      $form['actions']['save_redirect_layout']['#value'] = $redirect_label;

      // Ensure the button always appears when using Gin Admin theme.
      $form['actions']['save_redirect_layout']['#gin_action_item'] = TRUE;

      // Add a custom submit handler to our new submit action.
      $form['actions']['save_redirect_layout']['#submit'][] = [static::class, 'formSubmit'];

      // Remove the primary button type from our cloned button.
      if (isset($form['actions']['save_redirect_layout']['#button_type'])) {
        unset($form['actions']['save_redirect_layout']['#button_type']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function formSubmit(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof EntityFormInterface) {
      return;
    }

    // Redirect to the Mercury Editor task after saving.
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_object->getEntity();
    $form_state->setRedirect('entity.node.mercury_editor_task', ['node' => $node->id()]);

    // Clear any destination param to ensure our redirect takes precedence.
    $request = \Drupal::request();
    $request?->query->remove('destination');
  }

}
