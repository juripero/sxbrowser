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
use Symfony\Component\Yaml\Yaml;

/**
* Handle files
*/
class Files extends SxBrowser
{

    /**
    * return mimetype
    *
    * @param string $ext
    * @return string|boolean
    */
    public static function mimetype($ext)
    {
        $mimefile = SXB_PATH_SRC . '..' . DIRECTORY_SEPARATOR . 'mime.yml';
        if (file_exists($mimefile)) {
            $mimes = Yaml::parse(file_get_contents($mimefile));
        }
        if (isset($mimes[$ext])) {
            return $mimes[$ext];
        } else {
            return false;
        }
    }

    /**
    * get file from sx cluster and return to browser
    *
    * @param string $volkey
    * @param string $filename
    * @param boolean $forceDownload
    * @return boolean
    * @todo this will definitly break on larger files!
    */
    public function download($volkey, $filename, $forceDownload = false)
    {
        // $bytes = $this->sx->downloadFile($this->voldata->volume, $file, 'php://stdout');
        $file = $this->sx->getFile($this->voldata->volume, $filename);

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $header = $this->mimetype($ext);

        if ($forceDownload || $header === false) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . trim($filename, '/'));
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($file));
        } else {
            header('Content-Type:' . $header);
        }

        echo $file;
        return true;
    }
}
