<?php

namespace Drupal\d8_activity_list;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

class D8ActivityListEvent extends Event {
  const NODE_INSERT = 'node.insert';

  protected $node;

  public function __construct(NodeInterface $node)
  {
    $this->node = $node;
  }
  public function getNode()
  {
    return $this->node;
  }

}