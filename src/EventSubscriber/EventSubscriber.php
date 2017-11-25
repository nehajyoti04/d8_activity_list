<?php

/**
 * @file
 * Contains \Drupal\d8_activity_list\EventSubscriber\EventSubscriber.
 */

namespace Drupal\d8_activity_list\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\d8_activity_list\D8ActivityListEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber EventSubscriber.
 */
class EventSubscriber implements EventSubscriberInterface {

  protected $currentPath;
  protected $loggerFactory;

  /**
   * EventSubscriber constructor.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   */
  function __construct(CurrentPathStack $current_path, LoggerChannelFactory $logger) {
    $this->currentPath = $current_path;
    $this->loggerFactory = $logger;
  }

  /**
   * Code that should be triggered on event specified
   */
  public function onRespond(FilterResponseEvent $event) {
    $current_path = $this->currentPath->getPath();
    $path_args = explode('/', $current_path);
    if($path_args[1] == 'node') {
      $response = $event->getResponse();
      $response->headers->set('Access-Control-Allow-Origin', array('*'));
    }
  }

  public function watchdogLogEntry(D8ActivityListEvent $event) {
    $response = $event->getNode();
    $node_title = $response->getTitle();
    $this->loggerFactory->get('Custom event listener: Node title')->notice($node_title);

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = 'onRespond';
    $events[D8ActivityListEvent::NODE_INSERT][] = 'watchdogLogEntry';
    return $events;
  }

}
