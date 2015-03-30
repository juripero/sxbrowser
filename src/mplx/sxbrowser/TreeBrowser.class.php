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

use \mplx\skylablesx\Sx;

class TreeBrowser
{
    private $config;
    private $services;
    private $sx = null;

    private $voldata;
    private $files = array();
    private $lastpath = '/';

    public function __construct($volkey, $config, $services)
    {
        $this->config = $config['volumes'][$volkey];
        $this->services = $services;

        $this->config['volkey'] = $volkey;

        $this->sx = new Sx();
        $this->sx->setAuth($this->config['authkey']);
        $this->sx->setEndpoint($this->config['cluster']);
        $this->sx->setPort($this->config['port']);
        if ($this->config['ssl'] == true) {
            if (isset($this->config['sslverify']) && $this->config['sslverify'] == true) {
                $this->sx->setSSL(true, true);
            } else {
                $this->sx->setSSL(true, false);
            }
        } else {
            $this->sx->setSSL(false);
        }

        $volumes = $this->sx->getVolumeList();
        $volumes = (array) $volumes->volumeList;

        if (isset($volumes[$this->config['volume']])) {
            $this->voldata = $volumes[$this->config['volume']];
            $this->voldata->volume = $this->config['volume'];
            $this->voldata->cluster = $this->config['cluster'];
            if (isset($this->config['verbose'])) {
                $this->voldata->name = $this->config['verbose'];
            }
        } else {
            die('Error: volume not available in cluster');
        }
    }

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
