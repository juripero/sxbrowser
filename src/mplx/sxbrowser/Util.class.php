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

namespace mplx\sxbrowser;

/**
* Utils
*/
class Util
{
    /**
    * binaryprefix SI units
    *
    * @param mixed $size
    * @param boolean $iec
    * @return string
    */
    public static function prettyBytes($size, $iec = false)
    {
        $i=0;
        if ($iec) {
            $sizetype = array ('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        } else {
            $sizetype = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        }

        if (gettype($size) == 'integer' || gettype($size) == 'float') {
            while ($size > 1024) {
                $size = $size / 1024;
                $i++;
            }
            $size = ceil($size);
        }
        return $size . ' ' . $sizetype[$i];
    }

    /**
    * URI to path
    *
    * @param $uri string
    * @return string
    */
    public static function uriToPath($uri = null)
    {
        if ($uri === null) {
            $uri = '' . $_SERVER['REQUEST_URI'];
        }

        if (strpos($uri, '?') > 0) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return urldecode($uri);
    }

    /**
    * Breadcrumbs
    *
    * @param string $volkey
    * @param string $path
    * @param string $forcedir
    * @param string $delimiter
    * @return array
    */
    public static function breadcrumbs($volkey, $path, $forcedir = false, $delimiter = '/')
    {
        $up = $delimiter . $volkey . $delimiter;
        $crumbs = array(array(
            'name' => $volkey,
            'path' => $up
        ));

        $forcecut = strpos($path, $forcedir);
        if ($forcedir && $forcecut !== false) {
            $path = substr($path, $forcecut + strlen($forcedir));
        }

        $path = trim($path, $delimiter);
        $path = explode($delimiter, $path);
        if ($path[0] != '') {
            foreach ($path as $element) {
                $crumbs[] = array (
                    'name' => $element,
                    'path' => $up . $element . $delimiter
                );
                $up = $up . $element . $delimiter;
            }
        }
        return $crumbs;
    }
}
