<?php

declare(strict_types = 1);

namespace Drupal\pathauto_test\Seeder;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\user\Entity\User;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides shared functionality for seeders.
 */
abstract class SeederBase {

  protected $entities = [];
  protected $entityTypeManager;
  protected $userContext;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Gets the test user context.
   *
   * @return \Drupal\user\UserInterface|null
   *   The account context.
   */
  protected function getUserContext() : ? UserInterface {
    if (!$this->userContext) {
      $this->userContext = User::load(1);
    }
    return $this->userContext;
  }

  /**
   * Creates a new entity.
   *
   * @param string $entity_type_id
   *   The ID of the entity type of which to create a new entity.
   * @param string|null $bundle
   *   The entity's bundle or NULL if the entity type has no bundle.
   * @param mixed[] $values
   *   The array of entity values.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Created entity.
   *
   * @throws \InvalidArgumentException
   *   Thrown if the bundle is missing, or the bundle or entity values are
   *   invalid.
   */
  public function createEntity(string $entity_type_id, string $bundle = NULL, array $values = []) {
    // Set the entity's bundle.
    $entity_info = $this->entityTypeManager->getDefinition($entity_type_id);

    if ($entity_info->hasKey('bundle')) {
      if (!is_string($bundle)) {
        throw new \InvalidArgumentException(sprintf('Entity of type "%s" must be of a bundle, but none was specified.'));
      }
      $values[$entity_info->getKey('bundle')] = $bundle;
    }

    // Create the entity.
    $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);

    // Set the entity owner.
    if ($this->getUserContext() && $entity instanceof EntityOwnerInterface) {
      $entity->setOwnerId($this->getUserContext()->id());
    }

    if ($entity instanceof FieldableEntityInterface) {
      $violations = $entity->validate();
      if ($violations->count()) {
        $messages = [];
        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
          $messages[] = sprintf('%s (%s)', $violation->getPropertyPath(), $violation->getMessage());
        }
        throw new \InvalidargumentException(sprintf('Entity of type "%s" could not be created because of %d error(s) with the entity values: %s.', $entity_type_id, $violations->count(), implode(', ', $messages)));
      }
    }

    // Persist and keep track of the entity, so it can be deleted later.
    $this->entities[] = $entity;
    $entity->save();

    return $entity;
  }

  /**
   * Adds a translation for given entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   * @param string $langcode
   *   The langcode.
   * @param array $values
   *   The values.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The entity.
   */
  public function addTranslation(ContentEntityInterface $entity, string $langcode, array $values = []) {
    $translation = $entity->addTranslation($langcode, $values);
    // Set the entity owner.
    if ($this->getUserContext() && $translation instanceof EntityOwnerInterface) {
      $translation->setOwnerId($this->getUserContext()->id());
    }

    // Validate the entity.
    if ($entity instanceof FieldableEntityInterface) {
      $violations = $entity->validate();
      if ($violations->count()) {
        $messages = [];
        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
          $messages[] = sprintf('%s (%s)', $violation->getPropertyPath(), $violation->getMessage());
        }
        throw new \InvalidargumentException(sprintf('Translation could not be created because of %d error(s) with the entity values: %s.', $violations->count(), implode(', ', $messages)));
      }
    }
    $entity->save();

    return $entity;
  }

}
