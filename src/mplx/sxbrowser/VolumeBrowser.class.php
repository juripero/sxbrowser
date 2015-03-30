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

class VolumeBrowser
{
    private $config;
    private $services;

    public function __construct($config, $services)
    {
        $this->config = $config;
        $this->services = $services;
    }

    public function render()
    {
        $volumes = array();
        foreach($this->config['volumes'] as $key => $vol) {
            if ($vol['verbose']) {
                $volumes[] = array (
                    'key' => $key,
                    'name' => $vol['verbose'],
                    'connect' => $vol['volume'] . '@' . $vol['cluster']
                );
            } else {
                $volumes[] = array (
                    'key' => $key,
                    'name' => $vol['volume'],
                    'connect' => $vol['volume'] . '@' . $vol['cluster']
                );
            }
        }

        echo $this->services['twig']->render(
            'volumes.tpl.html',
            array(
                'volumes' => $volumes
            )
        );
    }
}
