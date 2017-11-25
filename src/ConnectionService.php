<?php

namespace Drupal\d8_activity_list;

use Drupal\Core\Database\Connection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\ClientInterface;

/**
 * WeatherService.
 */
class ConnectionService {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;


  function __construct(Connection $database) {
    $this->database = $database;
  }

  public function fetch() {
    $query = $this->database->select('d8_demo', 'd8')
      ->fields('d8', ['first_name', 'last_name'])
      ->orderBy('id', 'DESC')
      ->execute()->fetchAssoc();

    return $query;
  }

  public function insert($first_name, $last_name) {
    if ($first_name || $last_name) {
      $this->database->insert('d8_demo')->fields(
        array(
          'first_name' => $first_name,
          'last_name' => $last_name,
        )
      )->execute();
    }
  }

}
