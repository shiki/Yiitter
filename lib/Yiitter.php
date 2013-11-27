<?php

namespace Yiitter;

/**
 * A Yii application component that provides convenient access to the
 * {@link https://github.com/themattharris/tmhOAuth tmhOAuth} library by
 * {@link https://github.com/themattharris themattharris}. This allows Twitter
 * API configurations to be set up in the Yii config files and then easily create Twitter clients
 * using those configurations anywhere.
 *
 * To use, add this as a Yii component, and set the `connections` property to an array of
 * different Twitter clients that you want to use. Most of the time, you only need one. Different
 * clients are set with different array keys.
 *
 * The tmhOAuth library files are not included in this library. You need to download it yourself and
 * then set the `tmhOAuthLibPath` to its location.
 *
 * Here's an example Yii configuration using this library:
 *
 * <code>
 * ...
 * 'components' => array(
 *   'yiitter' => array(
 *     'class' => '\\Yiitter\\Yiitter',
 *     'tmhOAuthLibPath' => '/path/to/tmhOAuth/folder',
 *     'connections' => array(
 *       'default' => array(
 *         'consumer_key' => 'YOUR-CONSUMER-KEY',
 *         'consumer_secret' => 'YOUR-CONSUMER-SECRET',
 *       ),
 *     ),
 *   ),
 * )
 * ...
 * </code>
 *
 * Then, access your client by:
 *
 * <code>
 * $client = \Yii::app()->yiitter->getClient('default');
 * </code>
 *
 * Or, just:
 *
 * <code>
 * $client = \Yii::app()->yiitter->getClient(); // Assumed "default"
 * </code>
 *
 * The above will return an instance of {@link tmhOAuth} with the consumer key and secret already set.
 * If a client with the same configuration was previously created, {@link getClient} will return the previously
 * created client instance. If you do not want this behavior, you can use {@link createClient}.
 *
 * @author Shiki <bj@basanes.net>
 */
class Yiitter extends \CApplicationComponent
{
  /**
   * Path to the folder containing the tmhOAuth library (https://github.com/themattharris/tmhOAuth).
   * If you want to load that library, you don't need to specify this.
   *
   * @var string
   */
  public $tmhOAuthLibPath;

  protected $_clients = array();

  /**
   * Client configurations. This is normally set up in Yii config files. This is an array
   * containing configurations for {@link tmhOAuth} instances that will be created using this
   * application component.
   *
   * Sample value:
   *
   * <code>
   * array(
   *   'default' => array(
   *     'consumer_key' => 'YOUR-CONSUMER-KEY',
   *     'consumer_secret' => 'YOUR-CONSUMER-SECRET',
   *   ),
   * )
   * </code>
   *
   * @var array
   */
  public $connections = array(
    'default' => array(
      'consumer_key' => null,
      'consumer_secret' => null,
    ),
  );

  /**
   * {@inheriteddoc}
   */
  public function init()
  {
    parent::init();

    if (!$this->tmhOAuthLibPath || class_exists('tmhOAuth', false))
      return;

    require($this->tmhOAuthLibPath . '/tmhOAuth.php');
  }

  /**
   * Get an instance of `tmhOAuth` using the configuration pointed to by `$connectionKey`.
   * This will store the created instance locally and subsequent calls to this method using the same `$connectionKey`
   * will return the already created client.
   *
   * @param string $connectionKey The connection configuration key that can be found in {@link $connections}.
   *
   * @return tmhOAuth
   */
  public function getClient($connectionKey = 'default')
  {
    if (isset($this->_clients[$connectionKey]))
      return $this->_clients[$connectionKey];

    $client = $this->createClient($connectionKey);
    $this->_clients[$connectionKey] = $client;

    return $client;
  }

  /**
   * Create an instance of `tmhOAuth` using the configuration pointed to by `$connectionKey`.
   *
   * @param string $connectionKey The connection configuration key that can be found in {@link $connections}.
   *
   * @return tmhOAuth
   */
  public function createClient($connectionKey = 'default')
  {
    $connection = $this->connections[$connectionKey];
    $client = new \tmhOAuth($connection);

    return $client;
  }
}

