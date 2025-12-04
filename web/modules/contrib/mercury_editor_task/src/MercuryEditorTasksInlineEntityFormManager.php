<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Url;

/**
 * Service for adding Mercury Editor links to Inline Entity Form tables.
 *
 * This service inspects Inline Entity Form table rows and, when applicable,
 * inserts an "Open Mercury Editor" button into the actions column for
 * entities that are enabled for Mercury Editor.
 */
class MercuryEditorTasksInlineEntityFormManager implements MercuryEditorTasksInlineEntityFormInterface {

  /**
   * Constructs a MercuryEditorTasksInlineEntityForm object.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function addMercuryEditorLinks(array &$table): void {

    foreach ($table as $key => &$row) {
      // Early return if basic requirements aren't met.
      if (!is_int($key) ||
          !isset($row['status']['#object'], $row['actions']['ief_entity_edit'])) {
        continue;
      }

      $entity = $row['status']['#object'];
      if (!$entity instanceof ContentEntityInterface || $entity->isNew()) {
        continue;
      }

      // Check if entity is enabled for Mercury Editor.
      if (!$this->isMercuryEditorEnabled($entity)) {
        continue;
      }

      $link = $this->buildMercuryEditorLink($entity);
      $this->insertAfter($row['actions'], 'ief_entity_edit', 'ief_entity_mercury_editor', $link);
      $table['#attached']['library'][] = 'mercury_editor_task/mercury_editor_task.inline_entity_form';
    }
  }

  /**
   * Inserts a key/value after a target key in an array.
   *
   * @param array $array
   *   The array to modify (passed by reference).
   * @param string $target_key
   *   The key after which to insert the new key/value.
   * @param string $new_key
   *   The new key to insert.
   * @param mixed $new_value
   *   The value associated with the new key.
   *
   * @return void
   *   The array is modified in place.
   */
  private function insertAfter(array &$array, string $target_key, string $new_key, mixed $new_value): void {
    $new = [];
    foreach ($array as $key => $value) {
      $new[$key] = $value;
      if ($key === $target_key) {
        $new[$new_key] = $new_value;
      }
    }
    $array = $new;
  }

  /**
   * Checks if an entity is enabled for Mercury Editor.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   TRUE if Mercury Editor is enabled for the entity bundle, FALSE otherwise.
   */
  private function isMercuryEditorEnabled(ContentEntityInterface $entity): bool {
    $entity_type = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    $bundles = $this->configFactory->get('mercury_editor.settings')->get('bundles');

    return !empty($bundles[$entity_type][$bundle]);
  }

  /**
   * Builds the Mercury Editor link render array.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity for which to build the link.
   *
   * @return array
   *   A render array for the Mercury Editor link.
   */
  private function buildMercuryEditorLink(ContentEntityInterface $entity): array {
    return [
      '#type' => 'link',
      '#title' => $this->configFactory->get('mercury_editor_task.settings')->get('label'),
      '#url' => Url::fromRoute('entity.node.mercury_editor_task', [
        $entity->getEntityTypeId() => $entity->id(),
      ]),
      '#attributes' => [
        'target' => '_blank',
        'class' => 'button',
      ],
    ];
  }

}
