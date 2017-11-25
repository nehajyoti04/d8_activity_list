<?php

namespace Drupal\d8_activity_list;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\ClientInterface;

/**
 * WeatherService.
 */
class WeatherService {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  protected $configFactory;

  /**
   * Constructs a database object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   */
  public function __construct(ClientInterface $http_client, ConfigFactory $configFactory) {
    $this->httpClient = $http_client;
    $this->configFactory = $configFactory;
  }

  /**
   * Get a complete query for the API.
   */
  public function createRequest($options) {
    $query = [];
    $appid_config = $this->configFactory->get('d8_activity_list.settings')->get('appid');
    $query['appid'] = Html::escape($appid_config);
    $query['cnt'] = $options['count'];
    $input_data = Html::escape($options['city_name']);
    $query['q'] = $input_data;
    return $query;
  }

  /**
   * Return the data from the API in xml format.
   */
  public function getWeatherInformation($options) {
    try {
      $response = $this->httpClient->request('GET','http://api.openweathermap.org/data/2.5/weather',
      [
        'query' => $this->createRequest($options),
      ]);
    }
    catch (GuzzleException $e) {
      return FALSE;
    }
    return $response->getBody()->getContents();
  }

}
