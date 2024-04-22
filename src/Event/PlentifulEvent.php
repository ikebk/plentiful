<?php
namespace Drupal\plentiful\Event;

use Drupal\Component\EventDispatcher\Event;

/**
* Event that is fired when user views plentiful block 
*/
class PlentifulEvent extends Event {

    const EVENT_NAME = 'plentiful_api';

    /**
     * The API response data.
     *
     * @var array
     */
    protected $responseData;

    /**
     * Constructor.
     *
     * @param array $responseData
     *   The API response data.
     */ 
    public function __construct(array $responseData)
    {
        $this->responseData = $responseData;
    }

    /**
     * Get the API response data.
     *
     * @return array
     *   The API response data.
     */
    public function getResponseData() {
        return $this->responseData;
    }
}