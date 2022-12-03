<?php

namespace Drupal\gliderjs\Utility;

/**
 * Settings Helper.
 */
class SettingsHelper {

  /**
   * Build Settings.
   *
   * @param string $slider_id
   *   The id of the slider.
   * @param array $options
   *   The options of the slider.
   *
   * @return string
   *   A settings array that can be converted to JS for easy slider creation.
   */
  public static function buildSettings(string $slider_id, array $options): string {
    // Build general settings.
    $settings  = '{';
    $settings .= self::buildIndividualSettings($slider_id, $options);

    // Build breakpoint settings.
    $breakpoint_settings = '';
    if (!empty($options['breakpoint_settings'])) {
      foreach ($options['breakpoint_settings'] as $breakpoint => $breakpoint_options) {
        $breakpoint_settings .= '{';
        $breakpoint_settings .= '"breakpoint":' . $breakpoint;
        $breakpoint_settings .= ',"settings":{' . self::buildIndividualSettings($slider_id, $breakpoint_options, TRUE) . '}';
        $breakpoint_settings .= '},';
      }
      $breakpoint_settings = rtrim($breakpoint_settings, ',');
    }
    if (!empty($breakpoint_settings)) {
      $settings .= ',"responsive":[' . $breakpoint_settings . ']';
    }
    $settings .= '}';

    return $settings;
  }

  /**
   * Build Individual Settings.
   *
   * @param string $slider_id
   *   The id of the slider.
   * @param array $options
   *   The options of the slider.
   * @param bool $trim
   *   The trim flag for cleaning up settings.
   *
   * @return string
   *   A settings array that can be converted to JS for easy slider creation.
   */
  private static function buildIndividualSettings(string $slider_id, array $options, bool $trim = FALSE): string {
    $settings = '"draggable":' . (($options['draggable']) ? 'true' : 'false') . ',';

    if (!empty($options['drag_velocity'])) {
      $settings .= '"dragVelocity":' . $options['drag_velocity'] . ',';
    }

    if (!empty($options['duration'])) {
      $settings .= '"duration":' . $options['duration'] . ',';
    }

    $settings .= '"eventPropagate":' . (($options['event_propagate']) ? 'true' : 'false') . ',';
    $settings .= '"exactWidth":' . (($options['exact_width']) ? 'true' : 'false') . ',';

    if (!empty($options['item_width'])) {
      $settings .= '"itemWidth":' . $options['item_width'] . ',';
    }

    $settings .= '"resizeLock":' . (($options['resize_lock']) ? 'true' : 'false') . ',';
    $settings .= '"rewind":' . (($options['rewind']) ? 'true' : 'false') . ',';
    $settings .= '"scrollLock":' . (($options['scroll_lock']) ? 'true' : 'false') . ',';

    if (!empty($options['scroll_lock_delay'])) {
      $settings .= '"scrollLockDelay":' . $options['scroll_lock_delay'] . ',';
    }

    $settings .= '"scrollPropagate":' . (($options['scroll_propagate']) ? 'true' : 'false') . ',';
    $settings .= '"skipTrack":' . (($options['skip_track']) ? 'true' : 'false') . ',';

    if (!empty($options['slides_to_show'])) {
      if ($options['slides_to_show'] == 'auto') {
        $options['slides_to_show'] = '"' . $options['slides_to_show'] . '"';
      }
      $settings .= '"slidesToShow":' . $options['slides_to_show'] . ',';
    }

    if (!empty($options['slides_to_scroll'])) {
      if ($options['slides_to_scroll'] == 'auto') {
        $options['slides_to_scroll'] = '"' . $options['slides_to_scroll'] . '"';
      }
      $settings .= '"slidesToScroll":' . $options['slides_to_scroll'] . ',';
    }

    if ($options['show_dots']) {
      $settings .= '"dots":"' . "." . $slider_id . '-dots",';
    }

    if ($options['show_arrows']) {
      $settings .= '"arrows":{';
      $settings .= '"next":"' . "." . $slider_id . '-next",';
      $settings .= '"prev":"' . "." . $slider_id . '-prev"';
      $settings .= '}';
    }

    return ($trim) ? rtrim($settings, ',') : $settings;
  }

}
