<?php

declare(strict_types=1);

namespace Drupal\Tests\mercury_editor_task\Kernel;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\mercury_editor_task\MercuryEditorTaskFormDisplayBuilderInterface;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;

/**
 * Test Mercury Editor Task form display builder service.
 *
 * @covers \Drupal\mercury_editor_task\MercuryEditorTaskFormDisplayBuilder
 * @group mercury_editor_task
 */
class MercuryEditorTaskFormDisplayBuilderTest extends EntityKernelTestBase {
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
  protected static $modules = [
    'entity_reference_revisions',
    'node',
    'taxonomy',
    'block_content',
    'paragraphs',
    'layout_discovery',
    'layout_paragraphs',
    'mercury_editor',
    'mercury_editor_task',
  ];

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * The Mercury Editor Task form display builder.
   */
  protected MercuryEditorTaskFormDisplayBuilderInterface $formDisplayBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('paragraph');

    $this->installConfig(['mercury_editor', 'mercury_editor_task']);

    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
    $this->formDisplayBuilder = $this->container->get('mercury_editor_task.form_display_builder');
  }

  /**
   * Test Mercury Editor Task form display builder service.
   */
  public function testBuilder(): void {
    // Create basic page node type with default form displays.
    NodeType::create(['type' => 'basic_page', 'name' => 'Basic page'])->save();
    $this->entityDisplayRepository->getFormDisplay('node', 'basic_page', 'default')->save();

    // Create advanced page node type with form displays using layout paragraphs.
    $this->addParagraphedContentType('advanced_page', 'field_content', 'layout_paragraphs');

    // Make the advanced page node type use Mercury Editor.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.advanced_page', 'advanced_page')
      ->save();

    $form_display_storage = \Drupal::entityTypeManager()->getStorage('entity_form_display');

    /* ********************************************************************** */

    // Check that the 'default' form display exists.
    $this->assertNotNull($form_display_storage->load('node.basic_page.default'));
    $this->assertNotNull($form_display_storage->load('node.advanced_page.default'));
    $this->assertNull($form_display_storage->load('node.basic_page.mercury_editor'));
    $this->assertNull($form_display_storage->load('node.advanced_page.mercury_editor'));

    // Update the form displays.
    $this->formDisplayBuilder->updateContentTypes();

    // Check that the 'mercury_editor' form display for 'advanced_page' exists.
    $this->assertNull($form_display_storage->loadUnchanged('node.basic_page.mercury_editor'));
    $this->assertNotNull($form_display_storage->loadUnchanged('node.advanced_page.mercury_editor'));

    // Check that the 'mercury_editor' form display can't be created
    // for basic page because it does use layout paragraphs.
    $this->config('mercury_editor.settings')
      ->set('bundles.node.basic_page', 'basic_page')
      ->save();
    $this->formDisplayBuilder->updateContentTypes(TRUE);
    $this->assertNotNull($form_display_storage->loadUnchanged('node.basic_page.default'));
    $this->assertNull($form_display_storage->loadUnchanged('node.basic_page.mercury_editor'));

    // Check the components are displayed in the 'mercury_editor' form display for 'advanced_page'.
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.mercury_editor');
    $expected_components = [
      'created',
      'field_content',
      'promote',
      'status',
      'sticky',
      'title',
      'uid',
      'langcode',
      'revision_log',
    ];
    $actual_components = array_keys($form_display->getComponents());
    $this->assertEquals($expected_components, $actual_components);

    // Display on the uid component.
    $this->config('mercury_editor_task.settings')
      ->set('components', ['uid'])
      ->save();

    // Check that updating form displays without $ force = TRUE,
    // does not update existing form displays.
    $this->formDisplayBuilder->updateContentTypes();
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.mercury_editor');
    $actual_components = array_keys($form_display->getComponents());
    $this->assertEquals($expected_components, $actual_components);

    // Check that updating form displays with $ force = TRUE,
    // does update existing form displays.
    // IMPORTANT: Drupal core does not allow langcode
    // or revision log to be removed.
    $this->formDisplayBuilder->updateContentTypes(TRUE);
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.mercury_editor');
    $expected_components = [
      'field_content',
      'uid',
      'langcode',
      'revision_log',
    ];
    $actual_components = array_keys($form_display->getComponents());
    $this->assertEquals($expected_components, $actual_components);

    // Create a field group (w/o the field group module installed).
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.default');
    $form_display->setThirdPartySetting('field_group', 'group_test', ['children' => ['title']]);
    $form_display->save();

    // Trigger an update.
    $this->formDisplayBuilder->updateContentTypes(TRUE);

    // Check that the field group is removed.
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.mercury_editor');
    $this->assertNull($form_display->getThirdPartySetting('field_group', 'group_test'));

    // Display on the group_test field group.
    $this->config('mercury_editor_task.settings')
      ->set('components', ['group_test'])
      ->save();

    // Check that field group components are preserved.
    $this->formDisplayBuilder->updateContentTypes(TRUE);
    $form_display = $form_display_storage->loadUnchanged('node.advanced_page.mercury_editor');
    $expected_components = [
      'field_content',
      'title',
      'langcode',
      'revision_log',
    ];
    $actual_components = array_keys($form_display->getComponents());
    $this->assertEquals($expected_components, $actual_components);
    $this->assertEquals(['children' => ['title']], $form_display->getThirdPartySetting('field_group', 'group_test'));

    // Check that the 'mercury_editor' form display is removed when no components are entered.
    $this->config('mercury_editor_task.settings')
      ->set('components', [])
      ->save();
    $this->formDisplayBuilder->updateContentTypes(TRUE);
    $this->assertNull($form_display_storage->loadUnchanged('node.advanced_page.mercury_editor'));
  }

}
