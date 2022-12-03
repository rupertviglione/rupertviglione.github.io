<?php

namespace Drupal\redis\Cache;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Cache\CacheTagsChecksumInterface;
use Predis\Client;

/**
 * Predis cache backend.
 */
class PredisCluster extends Predis {

  /**
   * PredisCluster constructor.
   *
   * @param string $bin
   *   The bin.
   * @param \Predis\Client $client
   *   The client.
   * @param \Drupal\Core\Cache\CacheTagsChecksumInterface $checksum_provider
   *   The checksum provider.
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   *   The serializer.
   */
  public function __construct($bin, Client $client, CacheTagsChecksumInterface $checksum_provider, SerializationInterface $serializer) {
    parent::__construct($bin, $client, $checksum_provider, $serializer);
  }

}