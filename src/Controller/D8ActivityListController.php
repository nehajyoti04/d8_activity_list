<?php

namespace Drupal\d8_activity_list\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Builds an static callback page.
 */
class D8ActivityListController extends ControllerBase {

  /**
   * Call back for route static_content.
   */
  public function d8_static_callback() {
    $element = array(
      '#markup' => 'Hello! I am your node listing page.',
    );
    return $element;
  }

  public function d8_dynamic_listing_callback($arg = 1) {
    $element = array(
      '#markup' => 'Hello! I am your ' . $arg . ' listing page.',
    );
    return $element;
  }

  public function d8_node_detail_callback(NodeInterface $node) {
    return node_view($node, 'full');
  }

  public function d8_multiple_nodes_callback(NodeInterface $node1, NodeInterface $node2, NodeInterface $node3) {
    $element = array(
      '#markup' => "This is muliple node page with nodes \n" . $node1->getTitle() . $node2->getTitle() . " and " . $node3->getTitle(),
    );
    return $element;
  }

  public function access(AccountInterface $account, NodeInterface $node) {
    // Check permissions and combine that with any custom access checking needed.
    if ($account->id() == $node->getOwnerId()) {
      return AccessResult::allowed();
    } else {
      return AccessResult::forbidden();
    }
  }
}
