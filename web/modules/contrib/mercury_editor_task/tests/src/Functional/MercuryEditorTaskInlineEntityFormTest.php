<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\paragraphs\Entity\ParagraphsType;

/**
 * Tests the Mercury Editor Task integration with Inline Entity Form.
 *
 * @group mercury_editor_task
 *
 * @covers \Drupal\mercury_editor_task\Service\MercuryEditorTasksInlineEntityForm::addMercuryEditorLinks()
 */
class MercuryEditorTaskInlineEntityFormTest extends WebDriverTestBase {

  // phpcs:disable
  /**
   * Disabled config schema checking until the mercury_editor.module has fixed its schema.
   *
   * @see https://www.drupal.org/project/mercury_editor/issues/3491742
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field',
    'field_ui',
    'mercury_editor',
    'mercury_editor_task',
    'inline_entity_form',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->setupContentTypes();
    $this->setupParagraphTypes();
    $this->setupFields();
    $this->setupFormDisplay();
  }

  /**
   * Sets up the content types needed for testing.
   */
  protected function setupContentTypes(): void {
    // Create Advanced Page content type.
    $this->drupalCreateContentType([
      'type' => 'advanced_page',
      'name' => 'Advanced Page',
    ]);

    // Enable Mercury Editor for the advanced page content type.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', TRUE)
      ->save();

    // Configure Mercury Editor Task settings.
    $this->config('mercury_editor_task.settings')
      ->set('label', 'Layout')
      ->save();
  }

  /**
   * Sets up paragraph types needed for testing.
   */
  protected function setupParagraphTypes(): void {
    // Create a paragraph type for content references.
    $paragraph_type = ParagraphsType::create([
      'id' => 'content_reference',
      'label' => 'Content Reference',
    ]);
    $paragraph_type->save();
  }

  /**
   * Sets up the fields needed for testing.
   */
  protected function setupFields(): void {
    // Add layout paragraphs field to advanced_page.
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_layout_paragraphs',
      'entity_type' => 'node',
      'type' => 'entity_reference_revisions',
      'settings' => [
        'target_type' => 'paragraph',
      ],
      'cardinality' => -1,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'advanced_page',
      'label' => 'Layout Paragraphs',
      'settings' => [
        'handler' => 'default:paragraph',
        'handler_settings' => [
          'target_bundles' => [
            'content_reference' => 'content_reference',
          ],
        ],
      ],
    ]);
    $field->save();

    // Add content reference field to the content_reference paragraph type.
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_content_reference',
      'entity_type' => 'paragraph',
      'type' => 'entity_reference',
      'settings' => [
        'target_type' => 'node',
      ],
      'cardinality' => 1,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'content_reference',
      'label' => 'Content Reference',
      'settings' => [
        'handler' => 'default:node',
        'handler_settings' => [
          'target_bundles' => [
            'advanced_page' => 'advanced_page',
          ],
        ],
      ],
    ]);
    $field->save();
  }

  /**
   * Sets up form display configurations for testing.
   */
  protected function setupFormDisplay(): void {
    // Configure the advanced_page form display.
    $display = \Drupal::service('entity_display.repository')
      ->getFormDisplay('node', 'advanced_page', 'default');
    $display->setComponent('field_layout_paragraphs', [
      'type' => 'layout_paragraphs',
      'weight' => 10,
    ]);
    $display->save();

    // Configure the paragraph form display to use Inline Entity Form.
    $display = \Drupal::service('entity_display.repository')
      ->getFormDisplay('paragraph', 'content_reference', 'default');
    $display->setComponent('field_content_reference', [
      'type' => 'inline_entity_form_complex',
      'weight' => 0,
      'settings' => [
        'form_mode' => 'default',
        'override_labels' => FALSE,
        'label_singular' => '',
        'label_plural' => '',
        'allow_new' => TRUE,
        'allow_existing' => TRUE,
        'match_operator' => 'CONTAINS',
        'collapsible' => FALSE,
        'collapsed' => FALSE,
      ],
    ]);
    $display->save();
  }

  /**
   * Tests that Mercury Editor links appear in Inline Entity Form tables.
   *
   * This is a simplified test that verifies the service integration
   * without requiring complex JavaScript interactions.
   */
  public function testMercuryEditorLinksInInlineEntityForm(): void {
    $this->drupalLogin($this->rootUser);

    // Create a referenced node that has Mercury Editor enabled.
    $this->drupalGet('node/add/advanced_page');
    $this->submitForm([
      'title[0][value]' => 'Referenced Content',
    ], 'Save');
    $this->assertSession()->pageTextContains('Advanced Page Referenced Content has been created.');

    // Get the created node.
    $referenced_node = $this->drupalGetNodeByTitle('Referenced Content');
    $this->assertNotNull($referenced_node, 'Referenced node was created');

    // Test the service directly by simulating an IEF table structure.
    $mercury_editor_service = \Drupal::service('mercury_editor_task.inline_entity_form');

    // Simulate the IEF table structure that would be passed to our service.
    $table = [
      0 => [
        'status' => ['#object' => $referenced_node],
        'actions' => [
          'ief_entity_edit' => [
            '#type' => 'link',
            '#title' => 'Edit',
          ],
        ],
      ],
    ];

    // Call our service method.
    $mercury_editor_service->addMercuryEditorLinks($table);

    // Verify that the Mercury Editor link was added.
    $this->assertArrayHasKey('ief_entity_mercury_editor', $table[0]['actions']);
    $mercury_link = $table[0]['actions']['ief_entity_mercury_editor'];

    // Verify the link properties.
    $this->assertEquals('link', $mercury_link['#type']);
    $this->assertEquals('Layout', $mercury_link['#title']);
    $this->assertEquals('_blank', $mercury_link['#attributes']['target']);
    $this->assertStringContainsString('button', $mercury_link['#attributes']['class']);

    // Verify the URL points to the correct Mercury Editor route.
    $url = $mercury_link['#url'];
    $this->assertEquals('entity.node.mercury_editor_task', $url->getRouteName());
    $route_params = $url->getRouteParameters();
    $this->assertEquals($referenced_node->id(), $route_params['node']);
  }

}
