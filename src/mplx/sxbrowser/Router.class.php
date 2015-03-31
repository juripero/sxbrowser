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

class Router
{
    private $sx = null;
    private $config;
    private $services = array();

    /**
    * Constructor
    */
    public function __construct($config)
    {
        // configuration
        $this->config = $config;

        // template engine
        $loader = new \Twig_Loader_Filesystem(SXB_PATH_TPL . $this->config['sxbrowser']['template']);
        $this->services['twig'] = new \Twig_Environment($loader);
        if (SXB_DEBUG) {
            $this->services['twig']->enableDebug();
            $this->services['twig']->addExtension(new \Twig_Extension_Debug());
        }
        if (isset($this->config['template'][$this->config['sxbrowser']['template']])) {
            $this->services['twig']->addGlobal(
                'tplcfg',
                $this->config['template'][$this->config['sxbrowser']['template']]
            );
        }
        $filter = new \Twig_SimpleFilter('prettyBytes', '\mplx\sxbrowser\Util::prettyBytes');
        $this->services['twig']->addFilter($filter);
    }

    /**
    * Run
    */
    public function run()
    {
        $uri = Util::uriToPath();

        if (isset($_GET['file'])) {
            die ('download not implemented :(');
        } elseif ($uri == '/' || $uri == '') {
            $volumes = new VolumeBrowser($this->config, $this->services);
            $volumes->render();
        } else {
            $request = explode('/', ltrim($uri, '/'));
            $volume = array_shift($request);
            $path = '/' . implode('/', $request);

            $forcedir = false;
            if (isset($this->config['volumes'][$volume]['forcedir'])) {
                $forcedir = $this->config['volumes'][$volume]['forcedir'];
            }

            if (isset($this->config['volumes'][$volume])) {
                $tree = new TreeBrowser($volume, $this->config, $this->services);
                if ($result = $tree->fetch($path, $forcedir)) {
                    $tree->render();
                } else {
                    echo "Error: cannot fetch path";
                }
            } else {
                echo "Error: invalid volume";
            }
        }
    }
}
