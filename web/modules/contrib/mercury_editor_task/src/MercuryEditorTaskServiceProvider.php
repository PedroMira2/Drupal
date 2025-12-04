<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the controller for ME entity forms.
 */
class MercuryEditorTaskServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    if ($container->hasDefinition('mercury_editor.controller.entity_form')) {
      $definition = $container->getDefinition('mercury_editor.controller.entity_form');
      $definition->setClass('Drupal\mercury_editor_task\Controller\MercuryEditorTaskHtmlEntityFormController');
    }
    if ($container->hasDefinition('mercury_editor.content_translation_route_subscriber')) {
      $definition = $container->getDefinition('mercury_editor.content_translation_route_subscriber');
      $definition->setClass('Drupal\mercury_editor_task\Routing\MercuryEditorTaskContentTranslationRouteSubscriber');
    }
  }

}
