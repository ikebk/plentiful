<?php
namespace Drupal\plentiful;

use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Http\ClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Drupal\plentiful\PlentifulApiClientInterface;
use Drupal\plentiful\Event\PlentifulEvent;

class ReqresApiClient implements PlentifulApiClientInterface {
  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClientFactory;

  /**
   * The base URL for the API.
   *
   * @var string
   */
  protected $apiBaseUrl;
  
  /**
   * The logger for the API.
   *
   * @var string
   */
  protected $logger;
  
  /**
   *
   * @var array
   */
  protected $results;

  /**
   * @var 
   */
  protected $eventDispatcher;

  /**
   * Constructs a new ReqresApiClient object.
   *
   * @param \Drupal\Core\Http\ClientFactory $client_factory
   * @param string $apiBaseUrl
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   * 
   */
  public function __construct(ClientFactory $client_factory, $apiBaseUrl, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher) {
    $this->httpClientFactory = $client_factory;
    $this->apiBaseUrl = $apiBaseUrl;
    $this->logger = $logger;
    $this->eventDispatcher = $eventDispatcher;
    $this->results = [];
  }

  /**
   * Makes an API call.
   * @param string $endpoint
   *   The API endpoint to call.
   * @param array $query
   *   The API endpoint query.
   * 
   * @return array
   *   The API response.
   */
  public function makeApiCall($endpoint, $query = [], $count = null) {
    $sub_call = false;
    $options = [
      'base_uri' => $this->apiBaseUrl, 
      'query' => $query
    ];
    
    try {
      $response = $this->httpClientFactory->fromOptions($options)->get($endpoint);
      $results = json_decode($response->getBody()->getContents(), TRUE);
      $this->results = array_merge($this->results, $results['data']);
      
      if (!is_null($count)) {
        if (isset($results['total']) && $count < $results['total']) {
          $sub_call = true;
        }

        if (count($this->results) === $results['total']) {
          $sub_call = false;
        }
        // call subsequent pages base on limit settings in block config
        if ($sub_call) {
          $this->makeApiCall($endpoint, ['page' => ($query['page'] + 1)], $count);
        }

        if ($count < count($this->results)) {
          $this->results = array_slice($this->results, 0, $count);
        }
      }
      return $this;
    } catch (RequestException $e) {
      $this->logger->error('API request failed with error: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

  public function getUsers(): array {
    // Register and dispatch the event with the API response data
    $event = new PlentifulEvent($this->results);
    $this->eventDispatcher->dispatch('plentiful.api_response', $event);

    return $this->results;
  }
}