<?php
/**
 * sxbrowser
 * simple skylable sx directory browser
 *
 * @package     sxbrowser
 * @author      Martin Pircher <mplx+coding@donotreply.at>
 * @copyright   Copyright (c) 2014-2015, Martin Pircher
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 **/

// @codingStandardsIgnoreFile

use Symfony\Component\Yaml\Yaml;

// paths
define('SXB_PATH_SRC', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SXB_PATH_TPL', SXB_PATH_SRC . 'templates' . DIRECTORY_SEPARATOR);

// time zone
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

// composer autoloader
$vendor_autoload = SXB_PATH_SRC . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!file_exists($vendor_autoload)) {
    die('error: autoloader missing - did you run composer!?');
}
require_once $vendor_autoload;

// sxbroser autoloader
spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');
    $path = explode('\\', $class);
    $filename = array_pop($path);

    $class = SXB_PATH_SRC .
        implode(DIRECTORY_SEPARATOR, $path) .
        DIRECTORY_SEPARATOR .
        $filename .
        '.class.php';

    if (file_exists($class)) {
        require_once $class;
    }
}, true, true);

// config file
$configfile = SXB_PATH_SRC . '..' . DIRECTORY_SEPARATOR . 'config.local.yml';
if (file_exists($configfile)) {
    $config = Yaml::parse(file_get_contents($configfile));
} else {
    die('error: config not found');
}

// debugging
if (!defined('SXB_DEBUG')) {
    if ($config['sxbrowser']['debug']) {
        define('SXB_DEBUG', true);
    } else {
        define('SXB_DEBUG', false);
    }
}

if (SXB_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_erors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
