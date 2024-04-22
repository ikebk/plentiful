<?php
namespace Drupal\plentiful\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\plentiful\Event\PlentifulEvent;


class PlentifulEventSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    $events['plentiful.api_response'][] = ['onApiResponse'];
    return $events;
  }

  public function onApiResponse(PlentifulEvent $event) {
    // Get the API response data
    $responseData = $event->getResponseData();

    // Perform actions based on the API response data
    // For example, log the response data
    \Drupal::logger('plentiful_api_response')->info('Received API response: @response', ['@response' => print_r($responseData, TRUE)]);
  }

}