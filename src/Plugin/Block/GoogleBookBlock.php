<?php

namespace Drupal\d8_activity_list\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class GoogleBookBlock
 * @package Drupal\d8_activity_list\Plugin\Block
 *
 * @Block(
 *   id = "google_book_block",
 *   admin_label = @Translation("Google Book Block")
 * )
 */
class GoogleBookBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $http_client;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->http_client = $client;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }


  public function build() {

    $api = 'https://www.googleapis.com/books/v1/volumes';
    $options = [
      'query' => ['q' => 'isbn:'. $this->configuration['isbn']],
    ];
    $url = Url::fromUri($api, $options)->toString();
    try {
      $response = $this->http_client->get($url);
      $res = json_decode($response->getBody(), true)['items'][0];
      $volume_info = $res['volumeInfo'];
      $title = $res['volumeInfo']['title'];
      $subtitle = $res['volumeInfo']['subtitle'];
      $authors = $res['volumeInfo']['authors'][0];
      $publishedDate = $volume_info['publishedDate'];
      $description = $volume_info['description'];
      $build = [
        '#theme' => 'item_list',
        '#items' => [
          $title,
          $subtitle,
          $authors,
          $publishedDate,
          $description
        ]
      ];

    }
    catch (RequestException $e) {

    }

    return $build;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['isbn'] = [
      '#type' => 'textfield',
      '#title' => 'Google Book Isbn Code',
      '#default_value' => $this->configuration['isbn']
    ];

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['isbn'] = $form_state->getValue('isbn');
  }
}