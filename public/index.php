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

use mplx\sxbrowser;

require_once '../src/bootstrap.php';

try {
    $router = new mplx\sxbrowser\Router($config);
    $router->run();
} catch (\Exception $e) {
    if (SXB_DEBUG) {
        echo "<h1>Something went wrong...</h1>";
        echo "<h2>Debug Mode</h2>";
        echo $e->getMessage();
    } else {
        echo "Something went wrong...";
    }
}
