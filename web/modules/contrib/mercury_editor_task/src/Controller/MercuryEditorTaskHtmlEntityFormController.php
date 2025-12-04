<?php

declare(strict_types=1);

namespace Drupal\mercury_editor_task\Controller;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\mercury_editor\Controller\MercuryEditorHtmlEntityFormController;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Wrapping controller for ME entity forms that serve as the main page body.
 */
class MercuryEditorTaskHtmlEntityFormController extends MercuryEditorHtmlEntityFormController {

  /**
   * {@inheritdoc}
   */
  public function getContentResult(Request $request, RouteMatchInterface $route_match): array|RedirectResponse {
    $form_arg = $this->entityFormController->getFormArgument($route_match);
    [$entity_type_id] = explode('.', $form_arg);

    if (
      // For non nodes, always check (and apply) for Mercury Editor.
      ($entity_type_id !== 'node' && $route_match->getRouteName() !== 'mercury_editor.editor')
      ||
      // For nodes, only apply  Mercury Editor to the 'entity.node.mercury_editor_task' route.
      ($entity_type_id === 'node' && $route_match->getRouteName() === 'entity.node.mercury_editor_task')
    ) {
      /** @var array|\Symfony\Component\HttpFoundation\RedirectResponse $result */
      $result = parent::getContentResult($request, $route_match);
      if ($result instanceof RedirectResponse) {
        return $result;
      }
    }

    $result = $this->entityFormController->getContentResult($request, $route_match);
    // Hide layout paragraphs builder widgets on the node edit form.
    if ($entity_type_id === 'node') {
      foreach ($result as $key => $value) {
        if (Element::isRenderArray($value)
          && NestedArray::keyExists($value, ['widget', 'layout_paragraphs_builder'])) {
          // For the Schema.org Devel module's generate query parameter we must
          // visually hide the widget to ensure all data is submitted as expected.
          if ($request->query->get('schemadotorg_devel_generate')) {
            $result[$key]['#attributes']['style'] = 'display: none';
          }
          else {
            unset($result[$key]);
          }
        }
      }
    }
    return $result;
  }

  /**
   * Checks Mercury Editor access for a specific node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to check access for.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   Allowed is the node can be updated using Mercury Editor.
   */
  public static function checkAccess(NodeInterface $node): AccessResultInterface {
    // Check the user can update the node.
    if (!$node->access('update')) {
      return AccessResult::forbidden();
    }

    // Check that the node has Mercury Editor enabled.
    $has_mercury_editor = \Drupal::config('mercury_editor.settings')
      ->get('bundles.node.' . $node->bundle());
    if (!$has_mercury_editor) {
      return AccessResult::forbidden();
    }

    // Allow access to the route.
    return AccessResult::allowed();
  }

}
