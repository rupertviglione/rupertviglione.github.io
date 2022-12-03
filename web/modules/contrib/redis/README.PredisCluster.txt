See README.md file.

Sample configuration.

settings.php

  $settings['redis.connection']['interface'] = 'PredisCluster';
  $settings['redis.connection']['hosts'] = ['tcp://0.0.0.1:6379', 'tcp://0.0.0.2:6379', 'tcp://0.0.0.3:6379'];
  $settings['cache']['bins']['bootstrap'] = 'cache.backend.chainedfast';
  $settings['cache']['bins']['discovery'] = 'cache.backend.chainedfast';
  $settings['cache']['bins']['config'] = 'cache.backend.chainedfast';
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['container_yamls'][] = 'redis.services.yml';

redis.services.yml

  services:
    cache_tags.invalidator.checksum:
    class: Drupal\redis\Cache\PredisClusterCacheTagsChecksum
    arguments: ['@redis.factory']
    tags:
      - { name: cache_tags_invalidator }
    lock:
      class: Drupal\Core\Lock\LockBackendInterface
      factory: ['@redis.lock.factory', get]
    lock.persistent:
      class: Drupal\Core\Lock\LockBackendInterface
      factory: ['@redis.lock.factory', get]
      arguments: [true]
    flood:
      class: Drupal\Core\Flood\FloodInterface
      factory: ['@redis.flood.factory', get]