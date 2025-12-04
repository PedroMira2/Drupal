<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task\EventSubscriber;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters the node content translation overview to add mercury_editor_task operation to translations.
 *
 * @see \Drupal\content_translation\Controller\ContentTranslationController::overview
 */
class MercuryEditorTaskContentTranslationEventSubscriber extends ServiceProviderBase implements EventSubscriberInterface {

  /**
   * Constructs a MercuryEditorTaskContentTranslationEventSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Alters node content translation page to include a layout operation.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   The event to process.
   */
  public function onView(ViewEvent $event): void {
    if ($this->routeMatch->getRouteName() !== 'entity.node.content_translation_overview') {
      return;
    }

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->routeMatch->getParameter('node');
    $entity_type_id = $node->getEntityTypeId();
    $bundle = $node->bundle();
    if (empty($this->configFactory->get('mercury_editor.settings')->get("bundles.$entity_type_id.$bundle"))) {
      return;
    }

    $result = $event->getControllerResult();

    $rows =& NestedArray::getValue($result, ['content_translation_overview', '#rows']);
    foreach ($rows as &$row) {
      $last_cell_index = (count($row) - 1);
      $links =& NestedArray::getValue($row, [$last_cell_index, 'data', '#links']);
      if (!$links) {
        continue;
      }

      $edit_link = $links['edit'] ?? NULL;
      if (!$edit_link) {
        continue;
      }

      /** @var \Drupal\Core\Url $url */
      $url = $edit_link['url'];
      $mercury_editor_task_link = [
        'title' => $this->configFactory->get('mercury_editor_task.settings')->get('label'),
        'url' => Url::fromRoute('entity.node.mercury_editor_task', $url->getRouteParameters(), $url->getOptions()),
        'language' => $edit_link['language'],
      ];
      $this->insertAfter($links, 'edit', 'mercury_editor_task', $mercury_editor_task_link);
    }

    $event->setControllerResult($result);
  }

  /**
   * Inserts a new key/value after the key in the array.
   *
   * @param array &$array
   *   An array to insert in to.
   * @param string $target_key
   *   The key to insert after.
   * @param string $new_key
   *   The key to insert.
   * @param mixed $new_value
   *   The value to insert.
   */
  protected function insertAfter(array &$array, string $target_key, string $new_key, mixed $new_value): void {
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Run before main_content_view_subscriber.
    $events[KernelEvents::VIEW][] = ['onView', 100];
    return $events;
  }

}
