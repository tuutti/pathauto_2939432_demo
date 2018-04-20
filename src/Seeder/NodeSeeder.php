<?php

declare(strict_types = 1);

namespace Drupal\pathauto_test\Seeder;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\pathauto_test\Generator\NodeGenerator;

/**
 * Seed blog posts.
 */
final class NodeSeeder extends SeederBase {

  use NodeGenerator;

  /**
   * The node storage.
   *
   * @var \Drupal\node\NodeStorage
   */
  protected $nodeStorage;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->nodeStorage = $entityTypeManager->getStorage('node');

    parent::__construct($entityTypeManager);
  }

  /**
   * The seeder.
   *
   * @return \Drupal\node\NodeInterface
   *   The node.
   */
  public function seed() : NodeInterface {
    $node = $this->createBlogNode('Test node', 'en');

    return $node;
  }

}
