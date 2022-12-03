<?php

namespace Drupal\redis\Cache;

/**
 * Cache tags invalidations checksum implementation that uses redis.
 */
class PredisClusterCacheTagsChecksum extends RedisCacheTagsChecksum {

  /**
   * {@inheritdoc}
   */
  public function invalidateTags(array $tags) {
    $keys_to_increment = [];
    foreach ($tags as $tag) {
      // Only invalidate tags once per request unless they are written again.
      if (isset($this->invalidatedTags[$tag])) {
        continue;
      }
      $this->invalidatedTags[$tag] = TRUE;
      unset($this->tagCache[$tag]);
      $keys_to_increment[] = $this->getTagKey($tag);
    }
    if ($keys_to_increment) {
      $pipe = $this->client->pipeline();
      foreach ($keys_to_increment as $key) {
        $pipe->incr($key);
      }
      $pipe->execute();
    }
  }

}