<?php

namespace Drupal\gliderjs\Plugin\Field\FieldFormatter;

use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\gliderjs\Utility\SettingsHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'gliderjs' formatter.
 *
 * @FieldFormatter(
 *   id = "gliderjs",
 *   module = "gliderjs",
 *   label = @Translation("Glider.js"),
 *   field_types = {
 *     "image",
 *     "entity_reference"
 *   }
 * )
 */
class GliderjsFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

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
   * EntityType manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructor.
   *
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   Defines an interface for entity field definitions.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\breakpoint\BreakpointManagerInterface $breakpoint_manager
   *   The breakpoint manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ModuleHandlerInterface $module_handler, ConfigFactoryInterface $config_factory, BreakpointManagerInterface $breakpoint_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->moduleHandler = $module_handler;
    $this->configFactory = $config_factory;
    $this->breakpointManager = $breakpoint_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('module_handler'),
      $container->get('config.factory'),
      $container->get('breakpoint.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Display a slider using Glider.js.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $slider_id = 'slider_id_' . $this->fieldDefinition->getUniqueIdentifier();
    $options = $this->getSettings();
    $settings = SettingsHelper::buildSettings($slider_id, $options);

    $elements = [];
    foreach ($items as $item) {

      // Render Entity reference items.
      if ($item instanceof EntityReferenceItem) {
        $view_builder = $this->entityTypeManager->getViewBuilder($item->entity->getEntityTypeId());
        // @todo - set the display mode in settings. This uses default.
        $elements['#slides'][] = $view_builder->view($item->entity);
      }
      // Regular images.
      else {
        $elements['#slides'][] = $item;
      }
    }

    // Attach library and settings.
    $elements['#attached']['library'][] = 'gliderjs/gliderjs';
    $elements['#attached']['drupalSettings']['gliderjs'][$slider_id] = $settings;

    // Pass the settings to the theme.
    $elements['#slider_id'] = $slider_id;
    $elements['#theme'] = 'gliderjs_formatter';

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'slides_to_show' => 1,
      'slides_to_scroll' => 1,
      'show_arrows' => TRUE,
      'show_dots' => FALSE,
      'item_width' => 1,
      'exact_width' => FALSE,
      'scroll_lock' => FALSE,
      'scroll_lock_delay' => 250,
      'resize_lock' => TRUE,
      'rewind' => FALSE,
      'draggable' => FALSE,
      'drag_velocity' => 3.3,
      'duration' => 0.5,
      'scroll_propagate' => FALSE,
      'event_propagate' => TRUE,
      'skip_track' => FALSE,
      'breakpoint_settings' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $range = range(0, 10);
    unset($range[0]);
    $options = ['auto' => 'auto'] + $range;

    $form['slides_to_show'] = [
      '#title' => $this->t('Slides to Show'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->getSetting('slides_to_show'),
      '#description' => $this->t('The number of slides to show in container. If this value is set to "auto", it will be automatically calculated based upon the number of items able to fit within the container viewport.'),
    ];

    $form['slides_to_scroll'] = [
      '#title' => $this->t('Slides to Scroll'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->getSetting('slides_to_scroll'),
      '#description' => $this->t('The number of slides to scroll when arrow navigation is used. If this value is set to "auto", it will match the value of "Slides to Show".'),
    ];

    $form['show_arrows'] = [
      '#title' => $this->t('Show Arrows'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('show_arrows'),
    ];

    $form['show_dots'] = [
      '#title' => $this->t('Show Dots'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('show_dots'),
    ];

    $form['item_width'] = [
      '#title' => $this->t('Item Width'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 9999,
      '#step' => 1,
      '#field_suffix' => 'px',
      '#default_value' => $this->getSetting('item_width'),
      '#states' => [
        'visible' => [
          ':input[name="settings[formatter][settings][slides_to_show]"]' => ['value' => 'auto'],
        ],
      ],
    ];

    $form['exact_width'] = [
      '#title' => $this->t('Exact Width'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('exact_width'),
      '#description' => $this->t('This prevents resizing items to fit when "Slides to Show" is set to "auto". This will yield fractional slides if your container is not sized appropriately.'),
    ];

    $form['scroll_lock'] = [
      '#title' => $this->t('Scroll Lock'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('scroll_lock'),
      '#description' => $this->t('If checked, it will scroll to the nearest slide after any scroll interactions.'),
    ];

    $form['scroll_lock_delay'] = [
      '#title' => $this->t('Scroll Lock Delay'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 99999,
      '#step' => 1,
      '#default_value' => $this->getSetting('scroll_lock_delay'),
      '#description' => $this->t('The delay in milliseconds to wait before the scroll happens.'),
      '#states' => [
        'visible' => [
          ':input[name="settings[formatter][settings][scroll_lock]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['resize_lock'] = [
      '#title' => $this->t('Resize Lock'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('resize_lock'),
      '#description' => $this->t('If checked, the nearest slide on resizing of the window will be locked.'),
    ];

    $form['rewind'] = [
      '#title' => $this->t('Rewind'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('rewind'),
      '#description' => $this->t('If checked, it will scroll to the beginning/end when its respective endpoint is reached.'),
    ];

    $form['draggable'] = [
      '#title' => $this->t('Draggable'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('draggable'),
      '#description' => $this->t('If checked, the list can be scrolled by click and dragging with the mouse.'),
    ];

    $form['drag_velocity'] = [
      '#title' => $this->t('Drag Velocity'),
      '#type' => 'number',
      '#min' => 0,
      '#max' => 10,
      '#step' => 3.3,
      '#default_value' => $this->getSetting('drag_velocity'),
      '#description' => $this->t('How much to aggravate the velocity of the mouse dragging.'),
      '#states' => [
        'visible' => [
          ':input[name="settings[formatter][settings][draggable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['duration'] = [
      '#title' => $this->t('Duration'),
      '#type' => 'number',
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.1,
      '#default_value' => $this->getSetting('duration'),
      '#description' => $this->t('An aggravator used to control animation speed. Higher is slower!'),
    ];

    $form['scroll_propagate'] = [
      '#title' => $this->t('Scroll Propagate'),
      '#type' => 'hidden',
      '#value' => $this->getSetting('scroll_propagate'),
      '#description' => $this->t('Whether or not to release the scroll events from the container.'),
    ];

    $form['event_propagate'] = [
      '#title' => $this->t('Event Propagate'),
      '#type' => 'hidden',
      '#value' => $this->getSetting('event_propagate'),
      '#description' => $this->t('Whether or not Glider.js events should bubble (useful for binding events to all sliders).'),
    ];

    $form['skip_track'] = [
      '#title' => $this->t('Skip Track'),
      '#type' => 'hidden',
      '#value' => $this->getSetting('skip_track'),
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

      $stored_settings = $this->getSetting('breakpoint_settings');

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
          $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#default_value'] = $stored_settings[$breakpoint][$key] ?? '';

          switch ($key) {
            case 'item_width':
            case 'exact_width':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="settings[formatter][settings][breakpoint_settings][' . $breakpoint . '][slides_to_show]"]' => ['value' => 'auto'],
                ],
              ];
              break;

            case 'scroll_lock_delay':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="settings[formatter][settings][breakpoint_settings][' . $breakpoint . '][scroll_lock]"]' => ['checked' => TRUE],
                ],
              ];
              break;

            case 'drag_velocity':
              $output_breakpoint['breakpoint_settings'][$breakpoint][$key]['#states'] = [
                'visible' => [
                  ':input[name="settings[formatter][settings][breakpoint_settings][' . $breakpoint . '][draggable]"]' => ['checked' => TRUE],
                ],
              ];
              break;
          }
        }
      }
      $form += $output_breakpoint;
    }

    return $form;
  }

}
