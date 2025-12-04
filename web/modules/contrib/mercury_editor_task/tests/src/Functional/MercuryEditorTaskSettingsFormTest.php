<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;

/**
 * Tests the Mercury Editor Task Settings form.
 *
 * @group mercury_editor_task
 *
 * @covers \Drupal\mercury_editor_task\Form\MercuryEditorTaskSettingsForm
 */
class MercuryEditorTaskSettingsFormTest extends BrowserTestBase {
  use ParagraphsTestBaseTrait;

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
    'taxonomy',
    'block_content',
    'mercury_editor_task',
  ];

  /**
   * Test settings form.
   */
  public function testSettingsForm(): void {
    $assert = $this->assertSession();

    $admin_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($admin_user);

    // Update the label and components configuration.
    $this->drupalGet('/admin/config/content/mercury-editor/task');
    $edit = [
      'label' => 'Mercury Editor',
      'components' => 'title',
    ];
    $this->submitForm($edit, 'Save configuration');
    $assert->responseContains('The configuration options have been saved.');

    // Check that the label and components configuration is updated.
    $this->assertEquals(
      'Mercury Editor',
      $this->config('mercury_editor_task.settings')->get('label')
    );
    $this->assertEquals(
      ['title'],
      $this->config('mercury_editor_task.settings')->get('components')
    );

    // Make the advance page use Mercury Editor.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', 'advanced_page')
      ->save();

    // Create basic page.
    $this->drupalCreateContentType(['type' => 'basic_page', 'name' => 'Basic page']);

    // Create advanced page using layout paragraphs.
    $this->addParagraphedContentType('advanced_page', 'field_content', 'layout_paragraphs');

    // Make the advanced page node type use Mercury Editor.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', 'advanced_page')
      ->save();

    // Check that 'mercury_editor' form mode is not set up.
    $form_display_storage = \Drupal::entityTypeManager()->getStorage('entity_form_display');
    $this->assertNotNull($form_display_storage->load('node.basic_page.default'));
    $this->assertNotNull($form_display_storage->load('node.advanced_page.default'));
    $this->assertNull($form_display_storage->load('node.basic_page.mercury_editor'));
    $this->assertNull($form_display_storage->load('node.advanced_page.mercury_editor'));

    // Update all existing Mercury Editor form modes.
    $this->drupalGet('/admin/config/content/mercury-editor/task');
    $edit = ['update' => TRUE];
    $this->submitForm($edit, 'Save configuration');
    $assert->responseContains('The configuration options have been saved.');
    $assert->responseContains('Mercury Editor form displays have been updated.');

    // Check that 'mercury_editor' form mode is set up for advanced page.
    $this->assertNotNull($form_display_storage->load('node.basic_page.default'));
    $this->assertNotNull($form_display_storage->load('node.advanced_page.default'));
    $this->assertNull($form_display_storage->load('node.basic_page.mercury_editor'));
    $this->assertNotNull($form_display_storage->load('node.advanced_page.mercury_editor'));
  }

}
