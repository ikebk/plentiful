<?php
namespace Drupal\plentiful;

use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Http\ClientFactory;
use Psr\Log\LoggerInterface;

// use Drupal\Core\Config\ConfigFactoryInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\plentiful\PlentifulApiClientInterface;


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

  protected $counts;

  /**
   * Constructs a new ApiClient object.
   *
   * @param \Drupal\Core\Http\ClientFactory $client_factory
   *   The HTTP client factory.
   */
  public function __construct(ClientFactory $client_factory, $apiBaseUrl, LoggerInterface $logger) {
    $this->httpClientFactory = $client_factory;
    $this->apiBaseUrl = $apiBaseUrl;
    $this->logger = $logger;
    $this->results = [];
    $this->counts = 0;
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
    // $response = $this->httpClientFactory->fromOptions(
    //   [
    //     'base_uri' => 'https://reqres.in', 
    //     'query' => ['page' => 2]
    //   ]
    // )->get('api/users');

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

        if ($sub_call) {
          $this->makeApiCall($endpoint, ['page' => ($query['page'] + 1)], $count);
        }

        if ($count < count($this->results)) {
          $this->results = array_slice($this->results, 0, $count);
        }
      }

      // echo '<pre>'. print_r($results, 1) .'</pre>';
      return $this;

    } catch (RequestException $e) {
      $this->logger->error('API request failed with error: @error', ['@error' => $e->getMessage()]);
      return NULL;
    }
  }

  public function getUsers($count = null): array {
    if ($this->results) {
      return $this->results;
    }
    return [];
  }
}