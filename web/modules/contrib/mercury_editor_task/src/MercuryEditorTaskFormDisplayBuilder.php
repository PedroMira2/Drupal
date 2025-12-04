<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 * The Mercury Editor Task form display builder service.
 */
class MercuryEditorTaskFormDisplayBuilder implements MercuryEditorTaskFormDisplayBuilderInterface {

  /**
   * Constructs a MercuryEditorTaskFormDisplayBuilder object.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function updateContentTypes(bool $force = FALSE): bool {
    $updated = FALSE;

    $bundles = $this->configFactory
      ->get('mercury_editor.settings')
      ->get('bundles.node') ?? [];
    foreach ($bundles as $bundle) {
      if ($this->updateContentType($bundle, $force)) {
        $updated = TRUE;
      }
    }

    return $updated;
  }

  /**
   * {@inheritdoc}
   */
  public function updateContentType(string $bundle, bool $force = FALSE): bool {
    $bundles = $this->configFactory
      ->get('mercury_editor.settings')
      ->get('bundles.node') ?? [];
    if (!in_array($bundle, $bundles)) {
      return FALSE;
    }

    $default_display = $this->entityDisplayRepository->getFormDisplay('node', $bundle, 'default');
    $mercury_editor_display = $this->entityDisplayRepository->getFormDisplay('node', $bundle, 'mercury_editor');
    if (!$mercury_editor_display->isNew() && !$force) {
      return FALSE;
    }

    $displayed_components = $this->configFactory
      ->get('mercury_editor_task.settings')
      ->get('components');

    // If components are empty, delete the display.
    if (empty($displayed_components)) {
      $mercury_editor_display->delete();
      return FALSE;
    }

    // Get layout paragraphs components.
    $layout_paragraphs_components = $this->getLayoutParagraphsComponents($mercury_editor_display)
      + $this->getLayoutParagraphsComponents($default_display);

    // If there mo layout paragraphs in default or mercury editor displays,
    // we should not create or update the dedicated 'mercury_editor' display.
    if (empty($layout_paragraphs_components)) {
      return FALSE;
    }

    // Append layout components to displayed components.
    $displayed_components = array_merge($displayed_components, array_keys($layout_paragraphs_components));

    // Append field group children to displayed components.
    $field_group = $default_display->getThirdPartySettings('field_group');
    foreach ($field_group as $group_name => $group) {
      if (in_array($group_name, $displayed_components)) {
        $displayed_components = array_merge($displayed_components, $group['children']);
      }
    }

    $displayed_components = array_unique($displayed_components);

    // Copy the default display's properties, instead of duplicating the display
    // to ensure existing uuids are not overwritten.
    $display_properties = ['langcode', 'dependencies', 'third_party_settings', 'content', 'hidden'];
    foreach ($display_properties as $display_property) {
      $mercury_editor_display->set(
        $display_property,
        $default_display->get($display_property)
      );
    }

    // Make sure an existing display is always enabled.
    $mercury_editor_display->setStatus(TRUE);

    // Remove components from mercury editor display.
    $components = $mercury_editor_display->getComponents();
    foreach ($components as $component_name => $component) {
      if (!in_array($component_name, $displayed_components)) {
        $mercury_editor_display->removeComponent($component_name);
      }
    }

    // Add and remove layout paragraphs components from displays.
    foreach ($layout_paragraphs_components as $layout_paragraphs_component_name => $layout_paragraphs_component) {
      // Add layout paragraphs components to mercury editor display.
      $mercury_editor_display
        ->setComponent($layout_paragraphs_component_name, $layout_paragraphs_component);
      // Remove layout paragraph components from the default display.
      $default_display
        ->removeComponent($layout_paragraphs_component_name);
    }

    // Cleanup displays.
    $this->cleanupEntityFormDisplay($default_display);
    $this->cleanupEntityFormDisplay($mercury_editor_display);

    // Save the displays.
    $default_display->save();
    $mercury_editor_display->save();

    // Set updated status.
    return TRUE;
  }

  /**
   * Cleans up the entity form display.
   *
   * @param \Drupal\Core\Entity\Display\EntityFormDisplayInterface $display
   *   The entity form display interface to clean up.
   */
  protected function cleanupEntityFormDisplay(EntityFormDisplayInterface $display): void {
    // Update field groups.
    $field_group = $display->getThirdPartySettings('field_group');
    $displayed_components = array_keys($display->getComponents());
    if ($field_group) {
      foreach ($field_group as $group_name => $group) {
        $group['children'] = array_intersect($group['children'], $displayed_components);
        if (empty($group['children'])) {
          $display->unsetThirdPartySetting('field_group', $group_name);
        }
        else {
          $display->setThirdPartySetting('field_group', $group_name, $group);
        }
      }
    }
  }

  /**
   * Retrieves the components of type 'layout_paragraphs' from the given entity form display.
   *
   * @param \Drupal\Core\Entity\Display\EntityFormDisplayInterface $display
   *   The entity form display containing components to be filtered.
   *
   * @return array
   *   An array of component names of type 'layout_paragraphs'.
   */
  protected function getLayoutParagraphsComponents(EntityFormDisplayInterface $display): array {
    $layout_paragraphs_components = [];
    $components = $display->getComponents();
    foreach ($components as $component_name => $component) {
      if (isset($component['type'])
        && $component['type'] === 'layout_paragraphs') {
        $layout_paragraphs_components[$component_name] = $component;
      }
    }
    return $layout_paragraphs_components;
  }

}
