<?php

namespace Drupal\redis\Flood;

/**
 * Defines the database flood backend. This is the default Drupal backend.
 */
class PredisCluster extends Predis {
  // Just fall back to PhpRedis implementation for now because at the moment
  // it is 100% overlapping at the moment.
}