<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\language\Traits\LanguageTestTrait;

/**
 * Tests the content translation operations available in the content listing.
 *
 * @group content_translation
 */
class MercuryEditorTaskContentTranslationTest extends BrowserTestBase {
  use LanguageTestTrait;

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
    'language',
    'content_translation',
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

    // Enable additional languages.
    $langcodes = ['es', 'ast'];
    foreach ($langcodes as $langcode) {
      static::createLanguageFromLangcode($langcode);
    }
  }

  /**
   * Test settings form.
   */
  public function testSettingsForm(): void {
    $assert = $this->assertSession();

    $this->drupalCreateContentType(['type' => 'basic_page', 'name' => 'Basic page']);
    $this->drupalCreateContentType(['type' => 'advanced_page', 'name' => 'Advanced page']);
    $basic_node = $this->drupalCreateNode(['type' => 'basic_page']);
    $advanced_node = $this->drupalCreateNode(['type' => 'advanced_page']);
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', 'advanced_page')
      ->save();

    // Enable translation for the current entity type and ensure the change is
    // picked up.
    /** @var \Drupal\content_translation\ContentTranslationManagerInterface $content_translation_manager */
    $content_translation_manager = \Drupal::service('content_translation.manager');
    $content_translation_manager->setEnabled('node', 'basic_page', TRUE);
    $content_translation_manager->setEnabled('node', 'advanced_page', TRUE);

    // Login as root user.
    $this->drupalLogin($this->rootUser);

    // Check that basic page does not include mercury editor operation.
    $this->drupalGet('/node/' . $basic_node->id() . '/translations');
    $assert->linkByHrefExists('/node/' . $basic_node->id() . '/edit');
    $assert->linkByHrefNotExists('/node/' . $basic_node->id() . '/mercury-editor');

    // Check that advanced page does include mercury editor operation.
    $this->drupalGet('/node/' . $advanced_node->id() . '/translations');
    $assert->linkByHrefExists('/node/' . $advanced_node->id() . '/edit');
    $assert->linkByHrefExists('/node/' . $advanced_node->id() . '/mercury-editor');

    // Create a Spanish translation.
    $advanced_node->addTranslation('es', ['title' => 'Advanced page (Spanish)']);
    $advanced_node->save();

    // Check that advanced page does includes Spanish mercury editor operations.
    $this->drupalGet('/node/' . $advanced_node->id() . '/translations');
    $assert->linkByHrefExists('/es/node/' . $advanced_node->id() . '/edit');
    $assert->linkByHrefExists('/es/node/' . $advanced_node->id() . '/mercury-editor');
    $assert->linkByHrefExists('/es/node/' . $advanced_node->id() . '/delete');
  }

}
