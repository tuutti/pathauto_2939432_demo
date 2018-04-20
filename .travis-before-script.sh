#!/bin/bash

set -e $DRUPAL_TI_DEBUG

if [ "$DRUPAL_TI_CORE_BRANCH" == "8.5.x" ]; then
  cd "$DRUPAL_TI_DRUPAL_BASE/drupal"

  composer update phpunit/phpunit --with-dependencies --no-progress
fi
