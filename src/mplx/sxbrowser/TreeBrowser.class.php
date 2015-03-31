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
* Browse directory tree
*/
class TreeBrowser extends SxBrowser
{
    private $files = array();
    private $lastpath = '/';

    /**
    * fetch contents of path
    *
    * @param string $path
    * @param string $forcedir
    */
    public function fetch($path, $forcedir = false)
    {
        $this->forcedir = $forcedir;
        if ($forcedir) {
            $path = '/' . trim($forcedir, '/') . '/' . ltrim($path, '/');
        }

        $this->lastpath = $path;
        if ($path == '/') {
            $files = $this->sx->getFileList($this->voldata->volume);
        } else {
            $files = $this->sx->getFileList($this->voldata->volume, $path);
        }

        $this->files = $files;
        return $this->files;
    }

    /**
    * render with twig
    */
    public function render()
    {
        echo $this->services['twig']->render(
            'tree.tpl.html',
            array(
                'volkey' => $this->config['volkey'],
                'directories' => self::getDirs($this->files->fileList, $this->lastpath),
                'files' => self::getFiles($this->files->fileList, $this->lastpath),
                'crumbs' => Util::breadcrumbs($this->config['volkey'], $this->lastpath, $this->forcedir)
            )
        );
    }

    /**
    * extract directories from list
    *
    * @param array $list
    * @param string $stripstr
    * @return array
    */
    private static function getDirs($list, $stripstr = false)
    {
        $dir = array();
        foreach ($list as $name => $meta) {
            if ($stripstr && substr($name, 0, strlen($stripstr)) == $stripstr) {
                $name = substr($name, strlen($stripstr));
            }
            if (!isset($meta->fileSize)) {
                $dir[] = array (
                    'name' => $name
                );
            }
        }
        return $dir;
    }

    /**
    * extract files from list
    *
    * @param array $list
    * @param string $stripstr
    * @return array
    */
    private static function getFiles($list, $stripstr = false)
    {
        $files = array();
        foreach ($list as $name => $meta) {
            if ($stripstr && substr($name, 0, strlen($stripstr)) == $stripstr) {
                $name = substr($name, strlen($stripstr));
            }
            if (isset($meta->fileSize)) {
                $files[] = array (
                    'name' => $name,
                    'fsize' => $meta->fileSize
                );
            }
        }
        return $files;
    }
}
