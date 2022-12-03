<?php

/**
 * @file
 * Theme for gliderjs views.
 */

use Drupal\gliderjs\Plugin\views\style\Gliderjs;
use Drupal\gliderjs\Utility\SettingsHelper;

/**
 * Prepares variables for gliderjs_views template.
 *
 * @param array $variables
 *   An associative array.
 */
function template_preprocess_gliderjs_views(array &$variables) {
  if (isset($variables['view']) && $variables['view']->style_plugin instanceof Gliderjs) {
    $options = $variables['view']->style_plugin->options;
    $slider_id = 'slider_id_' . $variables['view']->storage->id() . '_' . $variables['view']->current_display;

    $settings = SettingsHelper::buildSettings($slider_id, $options);

    // Attach library and settings.
    $variables['#attached']['library'][] = 'gliderjs/gliderjs';
    $variables['#attached']['drupalSettings']['gliderjs'][$slider_id] = $settings;

    // Pass the settings to the theme.
    $variables['slider_id'] = $slider_id;
  }
}
