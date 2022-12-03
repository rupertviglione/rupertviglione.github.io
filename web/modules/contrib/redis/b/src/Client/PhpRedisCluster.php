<?php

namespace Drupal\redis\Client;

use Drupal\Core\Site\Settings;
use Drupal\redis\ClientInterface;

/**
 * PhpRedis client specific implementation.
 */
class PhpRedisCluster implements ClientInterface {

  const DEFAULT_READ_TIMEOUT = 1.5;
  const DEFAULT_TIMEOUT = 2;

  /**
   * The settings.
   *
   * @var array
   */
  private $settings;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'PhpRedisCluster';
  }

  /**
   * {@inheritdoc}
   */
  public function getClient($host = NULL, $port = NULL, $base = NULL, $password = NULL) {
    // Get the redis connection settings because we need some
    // client specific one.
    $this->initSettings();

    $client = new \RedisCluster($this->getClusterName(), $this->getSeeds(), $this->getTimeout(), $this->getReadTimeout(), $this->getPersistent());

    return $client;
  }

  /**
   * Initialize the settings.
   */
  private function initSettings() {
    $this->settings = Settings::get('redis.connection', []);
  }

  /**
   * Get the cluster name if configured.
   *
   * @return string|null
   *   Cluster name or NULL if not configured.
   */
  private function getClusterName() {
    if (isset($this->settings['cluster_name'])) {
      return $this->settings['cluster_name'];
    }

    return NULL;
  }

  /**
   * Get the seeds for the cluster connection.
   *
   * @return array
   *   An array of hosts.
   */
  private function getSeeds() {
    if (isset($this->settings['seeds'])) {
      return $this->settings['seeds'];
    }

    return [implode(':', [$this->settings['host'], $this->settings['port']])];
  }

  /**
   * Get the configured timeout.
   *
   * @return float
   *   Configured timeout or self::DEFAULT_TIMEOUT
   */
  private function getTimeout() {
    if (isset($this->settings['timeout'])) {
      return $this->settings['timeout'];
    }

    return self::DEFAULT_TIMEOUT;
  }

  /**
   * Get the configured read timeout.
   *
   * @return float
   *   Configured timeout or self::DEFAULT_READ_TIMEOUT
   */
  private function getReadTimeout() {
    if (isset($this->settings['read_timeout'])) {
      return $this->settings['read_timeout'];
    }

    return self::DEFAULT_READ_TIMEOUT;
  }

  /**
   * Get the persistent flag for the RedisCluster option.
   *
   * @return bool
   *   Return the persistent
   */
  private function getPersistent() {
    if (isset($this->settings['persistent'])) {
      return $this->settings['persistent'];
    }

    return FALSE;
  }

}