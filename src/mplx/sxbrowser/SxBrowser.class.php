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

use mplx\skylablesx\Sx;

/**
* Browse directory tree
*/
abstract class SxBrowser
{
    protected $config;
    protected $services;
    protected $sx = null;
    protected $voldata;

    /**
    * Constructor
    *
    * @param string $volkey
    * @param array $config
    * @param array $services
    */
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
}
