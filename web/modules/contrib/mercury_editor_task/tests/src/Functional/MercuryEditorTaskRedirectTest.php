<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Mercury Editor Task redirect functionality.
 *
 * @group mercury_editor_task
 *
 * @covers mercury_editor_task_form_node_form_alter()
 * @covers \Drupal\mercury_editor_task\MercuryEditorTaskFormAlter::formAlter()
 * @covers \Drupal\mercury_editor_task\MercuryEditorTaskFormAlter::formSubmit()
 */
class MercuryEditorTaskRedirectTest extends BrowserTestBase {

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
    'mercury_editor_task',
  ];

  /**
   * A user with permission to create and edit content.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $contentUser;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Create some content types for testing.
    $this->drupalCreateContentType(['type' => 'basic_page', 'name' => 'Basic page']);
    $this->drupalCreateContentType(['type' => 'advanced_page', 'name' => 'Advanced page']);

    // Enable Mercury Editor for the advanced page content type.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', TRUE)
      ->save();
  }

  /**
   * Tests the redirect create node option.
   */
  public function testRedirectCreateNode(): void {
    $this->drupalLogin($this->rootUser);

    // Configure to redirect on create only.
    $this->config('mercury_editor_task.settings')
      ->set('redirect_create', TRUE)
      ->set('redirect_update', FALSE)
      ->set('redirect_label', 'Save then layout')
      ->save();

    // Test creating a new node.
    $this->drupalGet('node/add/advanced_page');
    $this->assertSession()->statusCodeEquals(200);

    // Should have both Save and "Save then layout" buttons.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save then layout"]');
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');

    // Verify the page contains the form.
    $this->assertSession()->fieldExists('title[0][value]');

    // Fill out and submit the form with the layout button.
    $edit = [
      'title[0][value]' => 'Test Advanced Page',
    ];
    $this->submitForm($edit, 'Save then layout');

    // Verify we're redirected to the Mercury Editor task page.
    $this->assertSession()->addressMatches('/\/mercury-editor\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
    $this->assertSession()->statusCodeEquals(200);

    // Now test editing an existing node - should not have layout button.
    $node = $this->drupalGetNodeByTitle('Test Advanced Page');
    $this->assertNotNull($node, 'Node was created successfully');
    $this->drupalGet('node/' . $node->id() . '/edit');

    // For existing nodes, should only have regular Save button.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementNotExists('css', 'input[type="submit"][value="Save then layout"]');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests the redirect update option.
   */
  public function testRedirectUpdateNode(): void {
    $this->drupalLogin($this->rootUser);

    // Configure to show layout button on update only.
    $this->config('mercury_editor_task.settings')
      ->set('redirect_create', FALSE)
      ->set('redirect_update', TRUE)
      ->set('redirect_label', 'Custom Layout Button')
      ->save();

    // Test creating a new node with Mercury Editor enabled.
    $this->drupalGet('node/add/advanced_page');
    $this->assertSession()->statusCodeEquals(200);

    // Should only have regular Save button for new nodes.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementNotExists('css', 'input[type="submit"][value="Custom Layout Button"]');

    // Create the node with regular save.
    $edit = [
      'title[0][value]' => 'Test Advanced Page Update',
    ];
    $this->submitForm($edit, 'Save');

    // Should redirect to node view page.
    $this->assertSession()->addressMatches('/\/node\/\d+$/');

    // Test editing the existing node.
    $node = $this->drupalGetNodeByTitle('Test Advanced Page Update');
    $this->assertNotNull($node, 'Node was created successfully');
    $this->drupalGet('node/' . $node->id() . '/edit');

    // Should have both buttons for existing nodes.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Custom Layout Button"]');

    // Test that the layout button redirects to Mercury Editor.
    $edit = [
      'title[0][value]' => 'Updated Advanced Page Title',
    ];
    $this->submitForm($edit, 'Custom Layout Button');

    // Should redirect to Mercury Editor.
    $this->assertSession()->addressMatches('/\/mercury-editor\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests both create and update redirect options enabled together.
   */
  public function testCreateAndUpdateOptions(): void {
    $this->drupalLogin($this->rootUser);

    // Configure both options.
    $this->config('mercury_editor_task.settings')
      ->set('redirect_create', TRUE)
      ->set('redirect_update', TRUE)
      ->set('redirect_label', 'Go to Layout')
      ->save();

    // Test creating a new node - should have both buttons.
    $this->drupalGet('node/add/advanced_page');
    $this->assertSession()->statusCodeEquals(200);

    // With both options enabled, should have both Save and layout buttons.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Go to Layout"]');

    // Create the node with the layout button.
    $edit = [
      'title[0][value]' => 'Test Both Options',
    ];
    $this->submitForm($edit, 'Go to Layout');

    // Verify redirect to Mercury Editor.
    $this->assertSession()->addressMatches('/\/mercury-editor\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');

    // Test editing existing node - should also have both buttons.
    $node = $this->drupalGetNodeByTitle('Test Both Options');
    $this->assertNotNull($node, 'Node was created successfully');
    $this->drupalGet('node/' . $node->id() . '/edit');

    // Should have both Save and custom layout button.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Go to Layout"]');

    // Test that regular Save button works normally.
    $edit = [
      'title[0][value]' => 'Updated Both Options Title',
    ];
    $this->submitForm($edit, 'Save');

    // Should redirect to the node view page, not Mercury Editor.
    $this->assertSession()->addressMatches('/\/node\/\d+$/');
    $this->assertSession()->pageTextContains('Updated Both Options Title');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests that destination parameter is overridden.
   */
  public function testDestinationOverride(): void {
    $this->drupalLogin($this->rootUser);

    // Configure redirect for create nodes.
    $this->config('mercury_editor_task.settings')
      ->set('redirect_create', TRUE)
      ->set('redirect_update', FALSE)
      ->set('redirect_label', 'Save then layout')
      ->save();

    // Visit the form with a destination parameter.
    $this->drupalGet('node/add/advanced_page', ['query' => ['destination' => '/admin/content']]);
    $this->assertSession()->statusCodeEquals(200);

    // Fill out and submit the form with the layout button.
    $edit = [
      'title[0][value]' => 'Test Destination Override',
    ];
    $this->submitForm($edit, 'Save then layout');

    // Should redirect to Mercury Editor, not the destination.
    $this->assertSession()->addressMatches('/\/mercury-editor\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
    // Verify we're not at the admin content page.
    $current_url = $this->getSession()->getCurrentUrl();
    $this->assertStringNotContainsString('/admin/content', $current_url);
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests that redirect options are disabled for content types without Mercury Editor.
   */
  public function testDisabledForNonMercuryEditorContentTypes(): void {
    $this->drupalLogin($this->rootUser);

    // Configure redirect options.
    $this->config('mercury_editor_task.settings')
      ->set('redirect_create', TRUE)
      ->set('redirect_update', TRUE)
      ->set('redirect_label', 'Save then layout')
      ->save();

    // Test creating a basic_page (no Mercury Editor enabled).
    $this->drupalGet('node/add/basic_page');
    $this->assertSession()->statusCodeEquals(200);

    // Should only have regular Save button, no layout button.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementNotExists('css', 'input[type="submit"][value="Save then layout"]');

    // Verify this is actually a basic_page form.
    $this->assertSession()->fieldExists('title[0][value]');

    // Test creating the node to ensure it works normally.
    $edit = [
      'title[0][value]' => 'Test Basic Page',
    ];
    $this->submitForm($edit, 'Save');

    // Should redirect to the node view page, not Mercury Editor.
    $this->assertSession()->addressMatches('/\/node\/\d+$/');
    $this->assertSession()->pageTextContains('Test Basic Page');
    $this->assertSession()->statusCodeEquals(200);

    // Now test editing the basic_page node - should also not have layout button.
    $node = $this->drupalGetNodeByTitle('Test Basic Page');
    $this->assertNotNull($node, 'Node was created successfully');
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertSession()->statusCodeEquals(200);

    // Should only have regular Save button, no layout button on edit either.
    $this->assertSession()->elementExists('css', 'input[type="submit"][value="Save"]');
    $this->assertSession()->elementNotExists('css', 'input[type="submit"][value="Save then layout"]');
  }

}
