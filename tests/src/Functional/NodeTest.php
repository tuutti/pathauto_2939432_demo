<?php

namespace Drupal\Tests\pathauto_test\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\pathauto_test\Seeder\NodeSeeder;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests all node types exposed to GraphQL.
 *
 * @group pathauto_test
 */
class NodeTest extends BrowserTestBase {

  public static $modules = [
    'language',
    'locale',
    'menu_ui',
    'menu_link_content',
    'content_translation',
    'pathauto_test',
  ];

  protected $profile = 'standard';

  /**
   * Boolean indicating whether the language is installed or not.
   *
   * @var bool
   */
  protected $languageInstalled = FALSE;

  /**
   * Enables translation for the current entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The entity bundle.
   * @param bool $rebuild
   *   The boolean indicating whether to rebuild caches.
   */
  protected function enableTranslation(string $entityType, string $bundle, bool $rebuild = FALSE) {
    if (!$this->languageInstalled) {
      ConfigurableLanguage::createFromLangcode('fi')
        ->save();
      $this->languageInstalled = TRUE;
    }
    // Enable translation for the current entity type and ensure the change is
    // picked up.
    \Drupal::service('content_translation.manager')->setEnabled($entityType, $bundle, TRUE);

    if ($rebuild) {
      drupal_static_reset();
      \Drupal::entityTypeManager()->clearCachedDefinitions();
      \Drupal::service('router.builder')->rebuild();
      \Drupal::service('entity.definition_update_manager')->applyUpdates();

      // Rebuild the container so that the new languages are picked up by
      // services that hold a list of languages.
      $this->rebuildContainer();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->enableTranslation('node', 'article', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function testQuery() {
    $seeder = new NodeSeeder($this->container->get('entity_type.manager'));
    $node = $seeder->seed();
    $node->delete();
  }

}
