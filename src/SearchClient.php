<?php

namespace WPPerformance\Search\Inc;

use Exception;
use Meilisearch\Client;

// singleton class
class Search_Client
{
    public static $app_url = null;

    public static $app_key = null;
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            if (!self::$app_url || !self::$app_key) {
                throw new Exception('PHP constant MEILISEARCH_APP_ID and MEILISEARCH_KEY_PUBLIC are not set in wp-config.php file');
            }
            self::$instance = new Client(self::$app_url, self::$app_key);
        }

        return self::$instance;
    }

    public static function initKeys($app_url, $app_key)
    {
        self::$app_url = $app_url;
        self::$app_key = $app_key;
    }
}
