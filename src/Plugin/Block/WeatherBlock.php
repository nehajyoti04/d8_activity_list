<?php

namespace Drupal\d8_activity_list\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\d8_activity_list\WeatherService;

/**
 * Provides a 'OpenWeatherBlock' Block.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("Weather Block"),
 * )
 */
class WeatherBlock extends BlockBase implements ContainerFactoryPluginInterface {


  /**
   * The module handler.
   *
   * @var \Drupal\d8_activity_list\WeatherService
   */
  protected $weatherservice;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   *
   * @var string $weatherservice
   *   The information from the Weather service for this block.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, WeatherService $weatherservice) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->weatherservice = $weatherservice;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('d8_activity_list.weather_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['city_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Enter City name'),
      '#required' => TRUE,
      '#default_value' => !empty($config['city_name']) ? $config['city_name'] : '',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
  $this->setConfigurationValue('city_name', $form_state->getValue('city_name'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $output = json_decode($this->weatherservice->getWeatherInformation($config), TRUE);
    $attributes = array(
      'humidity' => $output['main']['humidity'],
      'temp_max' => $output['main']['temp_max'] - 273.15,
      'temp_min' => $output['main']['temp_min'] - 273.15 ,
      'pressure' => $output['main']['pressure'],
      'wind_speed' => $output['wind']['speed']
    );
    $build[] = [
      '#theme' => 'd8_activity_list',
      '#d8_activity_list_detail' => $attributes,
      '#attached' => array(
        'library' => array(
          'd8_activity_list/d8_activity_list_theme',
        ),
      ),
    ];
    return $build;
  }

}
