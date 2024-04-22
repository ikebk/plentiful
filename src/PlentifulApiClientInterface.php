<?php
namespace Drupal\plentiful;

interface PlentifulApiClientInterface {
  /**
   * Makes an API call.
   *
   * @param string $endpoint
   *   The API endpoint to call.
   * @param array $query
   *   The API endpoint query.
   *
   * @return array
   *   The API response.
   */
  public function makeApiCall($endpoint, $query = [], $count = null);

  /**
   * return array of users
   */
  public function getUsers(): array;
}