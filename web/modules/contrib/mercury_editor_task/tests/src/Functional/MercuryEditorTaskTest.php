<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Mercury Editor Task Settings form.
 *
 * @group mercury_editor_task
 *
 * @covers \mercury_editor_task_menu_local_tasks_alter()
 * @covers \mercury_editor_task_entity_operation_alter())
 */
class MercuryEditorTaskTest extends BrowserTestBase {

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
    'block',
    'node',
    'taxonomy',
    'block_content',
    'mercury_editor_task',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
  }

  /**
   * Test settings form.
   */
  public function testSettingsForm(): void {
    $assert = $this->assertSession();

    $this->drupalLogin($this->rootUser);

    $this->drupalCreateContentType(['type' => 'basic_page', 'name' => 'Basic page']);
    $this->drupalCreateContentType(['type' => 'advanced_page', 'name' => 'Advanced page']);
    $basic_node = $this->drupalCreateNode(['type' => 'basic_page']);
    $advanced_node = $this->drupalCreateNode(['type' => 'advanced_page']);
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', 'advanced_page')
      ->save();

    // Check that basic page does not hav 'Layout' task.
    $this->drupalGet('node/' . $basic_node->id());
    $assert->linkNotExists('Layout');
    $assert->linkByHrefNotExists('/node/' . $basic_node->id() . '/mercury-editor');

    // Check that advanced page has 'Layout' task.
    $this->drupalGet('node/' . $advanced_node->id());
    $assert->linkExists('Layout');
    $assert->linkByHrefExists('/node/' . $advanced_node->id() . '/mercury-editor');

    // Check that advanced page has 'Layout' operation.
    $this->drupalGet('/admin/content');
    $assert->linkByHrefNotExists('/node/' . $basic_node->id() . '/mercury-editor');
    $assert->linkExists('Layout');
    $assert->linkByHrefExists('/node/' . $advanced_node->id() . '/mercury-editor');
  }

}
