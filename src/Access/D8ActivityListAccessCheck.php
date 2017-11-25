<?php

namespace Drupal\d8_activity_list\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;

/**
 * Checks access for displaying configuration translation page.
 */
class D8ActivityListAccessCheck implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param $account
   *  Run access checks for this account.
   *
   * @param \Drupal\node\NodeInterface $node
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access($account, NodeInterface $node) {
    // Check permissions and combine that with any custom access checking needed.
    if($account->id() == $node->getOwnerId()) {
      return AccessResult::allowed();
    } else {
      return AccessResult::forbidden();
    }
  }

}
