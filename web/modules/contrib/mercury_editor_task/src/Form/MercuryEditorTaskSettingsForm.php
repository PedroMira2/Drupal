<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\ConfigTarget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\RedundantEditableConfigNamesTrait;
use Drupal\mercury_editor_task\MercuryEditorTaskFormDisplayBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Mercury Editor Task settings for this site.
 */
class MercuryEditorTaskSettingsForm extends ConfigFormBase {
  use RedundantEditableConfigNamesTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'mercury_editor_task_settings';
  }

  /**
   * The cache tag invalidator service.
   */
  protected CacheTagsInvalidatorInterface $cacheTagsInvalidator;

  /**
   * The Mercury Editor Task form display builder service.
   */
  protected MercuryEditorTaskFormDisplayBuilderInterface $formDisplayBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->cacheTagsInvalidator = $container->get('cache_tags.invalidator');
    $instance->formDisplayBuilder = $container->get('mercury_editor_task.form_display_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task/Operation label'),
      '#description' => $this->t('Enter the label to display for the dedicated Mercury Editor task and operation.'),
      '#config_target' => 'mercury_editor_task.settings:label',
    ];

    $form['components'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Form mode components'),
      '#description' => $this->t("Enter the field names, field groups, and components to display via the dedicated 'Mercury Editor' form display.")
        . ' '
        . $this->t("The 'Default' form display MUST initially include a layout paragraphs widget to generate a dedicated 'Mercury Editor' form display.")
        . ' '
        . $this->t("All layout paragraphs widgets will be moved from 'Default' form display to the dedicated 'Mercury Editor' form display.")
        . ' '
        . $this->t("Leave blank to not create a dedicated 'Mercury Editor' form display for each entity type and bundle."),
      '#config_target' => new ConfigTarget(
        'mercury_editor_task.settings',
        'components',
        // Converts config value to a form value.
        fn($value) => implode("\n", $value),
        // Converts form value to a config value.
        fn($value) => array_unique(array_map('trim', explode("\n", trim($value)))),
      ),
    ];

    // Add the redirect options.
    $form['redirect_create'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable "Save and edit layout" button when creating a node'),
      '#description' => $this->t('If checked, a "Save and edit layout" button will be added to the node form on Mercury Editor enabled content types. This button will redirect to Mercury Editor after saving a new node.'),
      '#config_target' => 'mercury_editor_task.settings:redirect_create',
    ];
    $form['redirect_update'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable "Save and edit layout" button when editing a node'),
      '#description' => $this->t('If checked, a "Save and edit layout" button will be added to the node form on Mercury Editor enabled content types. This button will redirect to Mercury Editor after editing a node.'),
      '#config_target' => 'mercury_editor_task.settings:redirect_update',
    ];
    $form['redirect_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Save and edit layout button label'),
      '#description' => $this->t('Enter the label to display for the  "Save and edit layout" button.'),
      '#config_target' => 'mercury_editor_task.settings:redirect_label',
      '#states' => [
        'visible' => [
          ['input[name="redirect_create"]' => ['checked' => TRUE]],
          'or',
          ['input[name="redirect_update"]' => ['checked' => TRUE]],
        ],
      ],
    ];

    // Update all existing 'Mercury Editor' form displays on save.
    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Update all existing 'Mercury Editor' form displays after saving this configuration."),
      '#description' => $this->t("If checked, 'Mercury Editor' form displays will be created/updated for all existing content types after saving this configuration."),
      '#return_value' => TRUE,
      '#prefix' => '<hr/>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);
    if ($form_state->getValue('update')) {
      $this->formDisplayBuilder->updateContentTypes(TRUE);
      $this->messenger()->addStatus($this->t('Mercury Editor form displays have been updated.'));
    }
    $this->cacheTagsInvalidator->invalidateTags(['local_task']);
  }

}
