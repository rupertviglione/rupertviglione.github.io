<?php

namespace Drupal\gliderjs\Plugin\views\style;

use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\core\form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Style plugin to render a slider with Gliderjs.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "gliderjs",
 *   title = @Translation("Glider.js"),
 *   help = @Translation("Render a Glider.js slider."),
 *   theme = "gliderjs_views",
 *   display_types = { "normal" }
 * )
 */
class Gliderjs extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The breakpoint manager.
   *
   * @var \Drupal\breakpoint\BreakpointManagerInterface
   */
  protected BreakpointManagerInterface $breakpointManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('config.factory'),
      $container->get('breakpoint.manager')
    );
  }

  /**
   * Constructs a PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\breakpoint\BreakpointManagerInterface $breakpoint_manager
   *   The breakpoint manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, ConfigFactoryInterface $config_factory, BreakpointManagerInterface $breakpoint_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->definition = $plugin_definition + $configuration;
    $this->moduleHandler = $module_handler;
    $this->configFactory = $config_factory;
    $this->breakpointManager = $breakpoint_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    return [
      'slides_to_show' => ['default' => 1],
      'slides_to_scroll' => ['default' => 1],
      'show_arrows' => ['default' => TRUE],
      'show_dots' => ['default' => FALSE],
      'item_width' => ['default' => 1],
      'exact_width' => ['default' => FALSE],
      'scroll_lock' => ['default' => FALSE],
      'scroll_lock_delay' => ['default' => 250],
      'resize_lock' => ['default' => TRUE],
      'rewind' => ['default' => FALSE],
      'draggable' => ['default' => FALSE],
      'drag_velocity' => ['default' => 3.3],
      'duration' => ['default' => 0.5],
      'scroll_propagate' => ['default' => FALSE],
      'event_propagate' => ['default' => TRUE],
      'skip_track' => ['default' => FALSE],
      'breakpoint_settings' => ['default' => ''],
    ] + parent::defineOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $range = range(0, 10);
    unset($range[0]);
    $options = ['auto' => 'auto'] + $range;

    $form['slides_to_show'] = [
      '#title' => $this->t('Slides to Show'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->options['slides_to_show'],
      '#description' => $this->t('The number of slides to show in container. If this value is set to "auto", it will be automatically calculated based upon the number of items able to fit within the container viewport.'),
    ];

    $form['slides_to_scroll'] = [
      '#title' => $this->t('Slides to Scroll'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->options['slides_to_scroll'],
      '#description' => $this->t('The number of slides to scroll when arrow navigation is used. If this value is set to "auto", it will match the value of "Slides to Show".'),
    ];

    $form['show_arrows'] = [
      '#title' => $this->t('Show Arrows'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['show_arrows'],
    ];

    $form['show_dots'] = [
      '#title' => $this->t('Show Dots'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['show_dots'],
    ];

    $form['item_width'] = [
      '#title' => $this->t('Item Width'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 9999,
      '#step' => 1,
      '#field_suffix' => 'px',
      '#default_value' => $this->options['item_width'],
      '#states' => [
        'visible' => [
          ':input[name="style_options[slides_to_show]"]' => ['value' => 'auto'],
        ],
      ],
    ];

    $form['exact_width'] = [
      '#title' => $this->t('Exact Width'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['exact_width'],
      '#description' => $this->t('This prevents resizing items to fit when "Slides to Show" is set to "auto". This will yield fractional slides if your container is not sized appropriately.'),
    ];

    $form['scroll_lock'] = [
      '#title' => $this->t('Scroll Lock'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['scroll_lock'],
      '#description' => $this->t('If checked, it will scroll to the nearest slide after any scroll interactions.'),
    ];

    $form['scroll_lock_delay'] = [
      '#title' => $this->t('Scroll Lock Delay'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 99999,
      '#step' => 1,
      '#default_value' => $this->options['scroll_lock_delay'],
      '#description' => $this->t('The delay in milliseconds to wait before the scroll happens.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[scroll_lock]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['resize_lock'] = [
      '#title' => $this->t('Resize Lock'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['resize_lock'],
      '#description' => $this->t('If checked, the nearest slide on resizing of the window will be locked.'),
    ];

    $form['rewind'] = [
      '#title' => $this->t('Rewind'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['rewind'],
      '#description' => $this->t('If checked, it will scroll to the beginning/end when its respective endpoint is reached.'),
    ];

    $form['draggable'] = [
      '#title' => $this->t('Draggable'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['draggable'],
      '#description' => $this->t('If checked, the list can be scrolled by click and dragging with the mouse.'),
    ];

    $form['drag_velocity'] = [
      '#title' => $this->t('Drag Velocity'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 10,
      '#step' => 0.1,
      '#default_value' => $this->options['drag_velocity'],
      '#description' => $this->t('How much to aggravate the velocity of the mouse dragging.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[draggable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['duration'] = [
      '#title' => $this->t('Duration'),
      '#type' => 'number',
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.1,
      '#default_value' => $this->options['duration'],
      '#description' => $this->t('An aggravator used to control animation speed. Higher is slower!'),
    ];

    $form['scroll_propagate'] = [
      '#title' => $this->t('Scroll Propagate'),
      '#type' => 'hidden',
      '#value' => $this->options['scroll_propagate'],
      '#description' => $this->t('Whether or not to release the scroll events from the container.'),
    ];

    $form['event_propagate'] = [
      '#title' => $this->t('Event Propagate'),
      '#type' => 'hidden',
      '#value' => $this->options['event_propagate'],
      '#description' => $this->t('Whether or not Glider.js events should bubble (useful for binding events to all sliders).'),
    ];

    $form['skip_track'] = [
      '#title' => $this->t('Skip Track'),
      '#type' => 'hidden',
      '#value' => $this->options['skip_track'],
      '#description' => $this->t('Whether or not Glider.js should skip wrapping its children with a "glider-track" <div>.'),
    ];

    if ($this->moduleHandler->moduleExists('breakpoint')) {
      $output_breakpoint = [];
      $output_breakpoint['breakpoint_settings'] = [
        '#type' => 'fieldset',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#tree' => TRUE,
        '#title' => $this->t('Breakpoint Settings'),
      ];

      $theme = $this->configFactory->get('system.theme')->get('default');
      $breakpoints = $this->breakpointManager->getBreakpointsByGroup($theme);

      foreach ($breakpoints as $breakpoint_name => $breakpoint_item) {
        $media_query = $breakpoint_item->getPluginDefinition()['mediaQuery'];
        $breakpoint = abs(filter_var($media_query, FILTER_SANITIZE_NUMBER_INT));

        $output_breakpoint['breakpoint_settings'][$breakpoint] = [
          '#type' => 'fieldset',
          '#collapsible' => TRUE,
          '#collapsed' => FALSE,
          '#title' => $this->t('Breakpoint: @breakpoint_name', ['@breakpoint_name' => $breakpoint_name]),
        ];

        foreach ($form as $key => $element) {
          $output_breakpoint['breakpoint_settings'][$breakpoint][$key] = $element;
          if (isset($this->options['breakpoint_settings'][$breakpoint][$key])) {
            $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#default_value'] = $this->options['breakpoint_settings'][$breakpoint][$key];
          }

          switch ($key) {
            case 'item_width':
            case 'exact_width':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="style_options[breakpoint_settings][' . $breakpoint . '][slides_to_show]"]' => ['value' => 'auto'],
                ],
              ];
              break;

            case 'scroll_lock_delay':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="style_options[breakpoint_settings][' . $breakpoint . '][scroll_lock]"]' => ['checked' => TRUE],
                ],
              ];
              break;

            case 'drag_velocity':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="style_options[breakpoint_settings][' . $breakpoint . '][draggable]"]' => ['checked' => TRUE],
                ],
              ];
              break;
          }
        }
      }
      $form += $output_breakpoint;
    }
  }

}
