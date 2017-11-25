<?php

namespace Drupal\d8_activity_list\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Block(
 *   id= "cache_block",
 *   admin_label= @Translation("Custom Cache Block")
 * )
 */

class CacheBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * @var AccountInterface $account
   */
  protected $account;

  /**
   * CacheBlock constructor.
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Database\Driver\mysql\Connection $database
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, AccountInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->account = $account;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('current_user')
    );
  }

  /**
   * @return array
   */
  public function build() {

    $output = '';
    $cache_tag_nids = '';
    $query = $this->database->select('node_field_data', 'n')
      ->fields('n', ['nid', 'title'])
      ->orderBy('nid', 'DESC')
      ->range(0, 3)
      ->execute()->fetchAll();
    foreach($query as $index => $row) {
      $output .= 'Node ' . ($index +1) . " => " . $row->title. '<br>';
      $block_cache_tags[] = "node:" . $row->nid;
    }

    $email_address  = $this->account->getEmail();
    $output .= "Current user's email address: " . $email_address;

    $block_cache_tags[] = 'user:' . $this->account->id();

    // Here adding #cache attribute to attach cache tag to this block
    // to invalidate cache of render array of this block.
    // Also, adding cache tag for each node as node:nid so that particular
    // node cache tag gets invalidated once node title is updated.
    $build = array(
      '#markup' => $output,
      '#cache' => array(
        'tags' => $block_cache_tags,
      ),
    );

    return $build;
  }

}
