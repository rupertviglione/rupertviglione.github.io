<?php

namespace Drupal\redis\PersistentLock;

use Drupal\redis\Lock\PhpRedisCluster as ClusterLock;
use Drupal\redis\ClientFactory;

/**
 * PhpRedisCluster persistent lock backend.
 */
class PhpRedisCluster extends ClusterLock {

  /**
   * Creates a PhpRedisCluster persistent lock backend.
   *
   * @param \Drupal\redis\ClientFactory $factory
   *   The ClientFactory object to initialize the client.
   */
  public function __construct(ClientFactory $factory) {
    // Do not call the parent constructor to avoid registering a shutdown
    // function that releases all the locks at the end of a request.
    $this->client = $factory->getClient();
    // Set the lockId to a fixed string to make the lock ID the same across
    // multiple requests. The lock ID is used as a page token to relate all the
    // locks set during a request to each other.
    // @see \Drupal\Core\Lock\LockBackendInterface::getLockId()
    $this->lockId = 'persistent';
  }

}