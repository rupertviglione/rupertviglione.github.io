<?php

namespace Drupal\redis\Client;

use Drupal\Core\Site\Settings;
use Drupal\redis\ClientInterface;
use Predis\Client;

/**
 * PredisCluster client specific implementation.
 */
class PredisCluster implements ClientInterface {

  /**
   * {@inheritdoc}
   */
  public function getClient($host = NULL, $port = NULL, $base = NULL, $password = NULL, $replicationHosts = NULL) {

    $settings = Settings::get('redis.connection', []);
    $parameters = $settings['hosts'];
    $options = ['cluster' => 'redis'];

    $client = new Client($parameters, $options);
    return $client;

  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'PredisCluster';
  }

}