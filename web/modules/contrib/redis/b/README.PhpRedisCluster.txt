See README.md file.

See README.PhpRedis.txt file because PhpRedisCluster requires the same PHP Redis extension.
However the extension version should be >=3.0.0.
Extension info can be found here: https://github.com/phpredis/phpredis

See RedisCluster() class documentation:
https://github.com/phpredis/phpredis/blob/develop/cluster.markdown#readme

Example settings.php configuration for using the PhpRedisCluster:

    ....
    $settings['redis.connection']['interface']          = 'PhpRedisCluster';
    $settings['redis.connection']['seeds']              = ['192.168.0.1:6379', '192.168.100.100:6379'];
    $settings['redis.connection']['read_timeout']       = 1.5;
    $settings['redis.connection']['timeout']            = 2;

    // You can also use some additional parameters for PhpRedisCluster as:
    // cluster_name - use if set in php.ini e.g.
    //   $settings['redis.connection']['cluster_name'] = 'redis_cluster';
    // persistent - persistent connections to each node e.g.
    //   $settings['redis.connection']['persistent'] = FALSE;

    // Set the Drupal's default cache backend.
    $settings['cache']['default'] = 'cache.backend.redis';

    // Always set the fast backend for bootstrap, discover and config, otherwise
    // this gets lost when redis is enabled.
    $settings['cache']['bins']['bootstrap'] = 'cache.backend.chainedfast';
    $settings['cache']['bins']['discovery'] = 'cache.backend.chainedfast';
    $settings['cache']['bins']['config'] = 'cache.backend.chainedfast';

Also, in your project services.yml file you should change the service for
"cache_tags.invalidator.checksum" to use Drupal\redis\Cache\PhpRedisClusterCacheTagsChecksum class.

    .....
    services:
      # Cache tag checksum backend. Used by redis and most other cache backend
      # to deal with cache tag invalidations.
      cache_tags.invalidator.checksum:
       class: Drupal\redis\Cache\PhpRedisClusterCacheTagsChecksum
       arguments: ['@redis.factory']
       tags:
         - { name: cache_tags_invalidator }
    .....

You can copy/paste the example.services.yml in your settings folder, override the value for
cache_tags.invalidator.checksum service and include the yml file in your settings.php as shown in the
examples in README.md