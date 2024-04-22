<?php
namespace Drupal\plentiful\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\PlentifulApiClientInterface;

/**
* Event that is fired when user views 
*/
class PlentifulEvent extends Event {

    const EVENT_NAME = 'plentiful_api';

    /**
    * the user account
    *
    * @var \Drupal\user\UserInterface
    */
    public $account;

 
    public function __construct(UserInterface $account)
    {
        $this->account = $account;
    }

}