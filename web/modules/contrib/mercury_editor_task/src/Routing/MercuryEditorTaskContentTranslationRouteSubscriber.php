<?php

namespace Drupal\mercury_editor_task\Routing;

use Drupal\mercury_editor\Routing\ContentTranslationRouteSubscriber;
use Symfony\Component\Routing\RouteCollection;

/**
 * Alters content translation routes to use alternate controller.
 */
class MercuryEditorTaskContentTranslationRouteSubscriber extends ContentTranslationRouteSubscriber {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    // Alter translation routes.
    // @phpstan-ignore-next-line
    $entity_type_bundles = \Drupal::config('mercury_editor.settings')->get('bundles') ?? [];
    // Do not alter node routes.
    unset($entity_type_bundles['node']);
    foreach (array_keys($entity_type_bundles) as $entity_type_id) {
      if ($route = $collection->get("entity.$entity_type_id.content_translation_add")) {
        $defaults = $route->getDefaults();
        $defaults['_controller'] = '\Drupal\mercury_editor\Controller\MercuryEditorContentTranslationController::add';
        $route->setDefaults($defaults);
      }
    }
  }

}
