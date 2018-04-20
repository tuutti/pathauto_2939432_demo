<?php

declare(strict_types = 1);

namespace Drupal\pathauto_test\Generator;

use Drupal\node\NodeInterface;

/**
 * Provides a trait to generate nodes.
 */
trait NodeGenerator {

  /**
   * Create new blog node.
   *
   * @param string $title
   *   The node title.
   * @param string $langcode
   *   The node langcode.
   * @param bool $translate
   *   Boolea indicating whether to translate node or not.
   *
   * @return \Drupal\node\NodeInterface
   *   The node.
   */
  public function createBlogNode(string $title, string $langcode, $translate = TRUE) : NodeInterface {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->createEntity('node', 'article', [
      'langcode' => $langcode,
      'title' => sprintf('%s %s', $title, $langcode),
      'body' => [
        'format' => 'plain_text',
        'value' => 'DSAdsasad',
      ],
    ]);

    if ($translate) {
      $lang = $langcode === 'fi' ? 'en' : 'fi';

      $this->addTranslation($node, $lang, [
        'title' => sprintf('%s %s', $title, $lang),
        'body' => [
          'format' => 'plain_text',
          'value' => 'DSAdsasad',
        ],
      ]);
    }
    $node->save();

    return $node;
  }

}
