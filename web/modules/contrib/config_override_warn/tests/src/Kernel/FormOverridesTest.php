<?php

namespace Drupal\Tests\config_override_warn\Kernel;

use Drupal\block\BlockForm;
use Drupal\KernelTests\KernelTestBase;
use Drupal\system\Form\SiteInformationForm;

/**
 * Tests module overrides of configuration using event subscribers.
 *
 * @group config_override_warn
 * @covers \Drupal\config_override_warn\FormOverrides
 */
class FormOverridesTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'config',
    'block',
    'config_override_test',
    'config_override_warn',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('block');
    $this->installConfig('config_override_test');
  }

  /**
   * Test overridden values.
   *
   * @param array $config_to_set
   *   Any configuration that needs to be set.
   * @param string $form_class
   *   The form class name to use.
   * @param array $expected_overrides
   *   The expected value from the getFormOverrides() method.
   * @param bool $override_flag
   *   The override flag to set.
   *
   * @dataProvider providerFormOverrides
   */
  public function testFormOverrides(array $config_to_set, $form_class, array $expected_overrides, $override_flag) {
    foreach ($config_to_set as $config_name => $values) {
      $config = $this->config($config_name);
      foreach ($values as $key => $value) {
        $config->set($key, $value);
      }
      $config->save(TRUE);
    }
    $GLOBALS['config_test_run_module_overrides'] = $override_flag;
    $form = \Drupal::classResolver($form_class);
    $overrides = $this->container->get('config_override_warn.form_overrides')->getFormOverrides($form);
    $this->assertSame($expected_overrides, $overrides);
    unset($GLOBALS['config_test_run_module_overrides']);
  }

  /**
   * Data provider for testing form overrides.
   *
   * @return array
   *   An array of test cases.
   */
  public function providerFormOverrides() {
    return [
      // Test with show_values turned on, show that both name and slogan are
      // overridden with their original and overridden values.
      [
        [
          'system.site' => [
            'name' => 'Original name',
            'slogan' => 'Original slogan',
          ],
          'config_override_warn.settings' => [
            'show_values' => TRUE,
          ],
        ],
        SiteInformationForm::class,
        [
          'system.site' => [
            'name' => [
              'original' => '\'Original name\'',
              'override' => '\'ZOMG overridden site name\'',
            ],
            'slogan' => [
              'original' => '\'Original slogan\'',
              'override' => '\'Yay for overrides!\'',
            ],
          ],
        ],
        TRUE,
      ],
      // Test the same as above just with only the name being overridden.
      [
        [
          'system.site' => [
            'name' => 'Original name',
            'slogan' => 'Yay for overrides!',
          ],
          'config_override_warn.settings' => [
            'show_values' => TRUE,
          ],
        ],
        SiteInformationForm::class,
        [
          'system.site' => [
            'name' => [
              'original' => '\'Original name\'',
              'override' => '\'ZOMG overridden site name\'',
            ],
          ],
        ],
        TRUE,
      ],
      // With show_values turned off (default), we should just see that the
      // name and slogan values are overridden, without values.
      [
        [],
        SiteInformationForm::class,
        [
          'system.site' => [
            'name' => NULL,
            'slogan' => NULL,
          ],
        ],
        TRUE,
      ],
      // Test the same as above just with only the slogan being overridden.
      [
        [
          'system.site' => [
            'name' => 'ZOMG overridden site name',
          ],
        ],
        SiteInformationForm::class,
        [
          'system.site' => [
            'slogan' => NULL,
          ],
        ],
        TRUE,
      ],
      // Test with no overrides.
      [
        [],
        SiteInformationForm::class,
        [],
        FALSE,
      ],
    ];
  }

  /**
   * Test config entity form overridden values.
   *
   * @param array $config_to_set
   *   Any configuration that needs to be set.
   * @param string $form_class
   *   The form class name to use.
   * @param array $expected_overrides
   *   The expected value from the getFormOverrides() method.
   * @param bool $override_flag
   *   The override flag to set.
   *
   * @dataProvider providerConfigEntityOverrides
   */
  public function testConfigEntityOverrides(array $config_to_set, $form_class, $entity_type, $entity_id, array $expected_overrides, $override_flag) {
    foreach ($config_to_set as $config_name => $values) {
      $config = $this->config($config_name);
      foreach ($values as $key => $value) {
        $config->set($key, $value);
      }
      $config->save(TRUE);
    }
    $GLOBALS['it_is_pirate_day'] = $override_flag;
    /** @var \Drupal\Core\Entity\EntityFormInterface $form */
    $form = \Drupal::classResolver($form_class);
    $entity = $this->container->get('entity_type.manager')->getStorage($entity_type)->load($entity_id);
    $form->setEntity($entity);
    $overrides = $this->container->get('config_override_warn.form_overrides')->getFormOverrides($form);
    $this->assertSame($expected_overrides, $overrides);
    unset($GLOBALS['it_is_pirate_day']);
  }

  /**
   * Data provider for testing config entity overrides.
   *
   * @return array
   *   An array of test cases.
   */
  public function providerConfigEntityOverrides() {
    return [
      // Test with show_values turned on.
      [
        [
          'config_override_warn.settings' => [
            'show_values' => TRUE,
          ],
        ],
        BlockForm::class,
        'block',
        'call_to_action',
        [
          'block.block.call_to_action' => [
            'settings' => [
              'original' => var_export(['label' => 'Shop for cheap now!'], TRUE),
              'override' => var_export(['label' => 'Draw yer cutlasses!'], TRUE),
            ],
          ],
        ],
        TRUE,
      ],
      // Test the same as above but with a value that is not overridden.
      [
        [
          'block.block.call_to_action' => [
            'settings.label' => 'Draw yer cutlasses!',
          ],
          'config_override_warn.settings' => [
            'show_values' => TRUE,
          ],
        ],
        BlockForm::class,
        'block',
        'call_to_action',
        [],
        TRUE,
      ],
      // Test with show_values turned off (default).
      [
        [],
        BlockForm::class,
        'block',
        'call_to_action',
        [
          'block.block.call_to_action' => [
            'settings' => NULL,
          ],
        ],
        TRUE,
      ],
      // Test with no overrides.
      [
        [],
        BlockForm::class,
        'block',
        'call_to_action',
        [],
        FALSE,
      ],
    ];
  }

}
