<?php

namespace Drupal\redis\Flood;

use Drupal\Core\Flood\FloodInterface;

/**
 * Defines the database flood backend. This is the default Drupal backend.
 */
class PhpRedisCluster extends PhpRedis implements FloodInterface {
  // Just fall back to PhpRedis implementation for now because at the moment
  // it is 100% overlapping at the moment.
}