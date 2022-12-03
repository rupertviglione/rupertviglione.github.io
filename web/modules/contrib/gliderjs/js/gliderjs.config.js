/**
 * @file
 * Javascript for gliderjs configuracion.
 *
 * Available variables:
 * - slider_id: The slider container element id.
 * - slides_to_show: The number of slides to show in container.
 * - slides_to_scroll: The number of slides to scroll with arrow navigation.
 * - show_arrows: Show Arrows.
 * - show_dots: Show Dots.
 * - item_width: The item width.
 * - exact_width: Use the exact item width.
 * - scroll_lock: If scroll to the nearest slide after any scroll interactions.
 * - scroll_lock_delay: Delay in milliseconds to wait before the scroll happens.
 * - resize_lock: If the nearest slide on resizing of the window will be locked.
 * - rewind: Scroll to beginning/end when its respective endpoint is reached.
 * - draggable: The list can be scrolled by click and dragging with the mouse.
 * - drag_velocity: How much to aggravate the velocity of the mouse dragging.
 * - duration: An aggravator used to control animation speed. Higher is slower!
 * - scroll_propagate: Whether or not to release the scroll events from the container.
 * - event_propagate: Whether or not Glider.js events should bubble (useful for binding events to all sliders).
 * - skip_track: Whether or not Glider.js should skip wrapping its children with a "glider-track" <div>.
 * - breakpoint_settings: Breakpoint Settings.
 *
 * */

((Drupal) => {
  Drupal.behaviors.gliderJS = {
    attach(context, settings) {
      const gliders = once('gliderJS', '[data-gliderjs-module]', context);
      gliders.forEach((el) => {
        const options = JSON.parse(settings.gliderjs[el.id]);
        const glider = new Glider(el, options);
      });
    },
  };
})(Drupal);
