<?php

namespace Yiitter;

/**
 * Creates an exception from a tmhOAuth instance based on its last response data.
 *
 * @author Shiki <bj@basanes.net>
 */
class ClientException extends \Exception
{
  public $statusCode;

  /**
   * Create an instance of self.
   *
   * @param \tmhOAuth $client
   */
  public function __construct(\tmhOAuth $client)
  {
    if (!empty($client->response['response'])) {
      $response = json_decode($client->response['response'], true);
      if (array_key_exists('errors', $response)) {
        $message = $response['errors'][0]['message'];
        $code = $response['errors'][0]['code'];
      }
    }

    if (!isset($message))
      $message = $client->response['error'];
    if (!isset($code))
      $code = $client->response['errno'];

    $this->statusCode = $client->response['info']['http_code'];

    parent::__construct($message, $code);
  }

  /**
   * Creates an instance of self only if the \tmhOAuth instance's response is an error.
   *
   * @param \tmhOAuth $client
   * @return self
   */
  public static function createIfFailed(\tmhOAuth $client)
  {
    if ($client->response['info']['http_code'] == 200
      || $client->response['info']['http_code'] == 201) {
      return null;
    }

    return new self($client);
  }
}

