<?php
/**
 * 远程包操作类
 */
class EmdPack
{
    /**
     * @var array 所有子目录列表
     * @private
     */
    private $subdirs = array();

    /**
     * @var array 所有文件列表
     * @private
     */
    private $files = array();

    /**
     * 打包并保存文件
     * @param string $rootdir 包根目录
     * @return bool
     */
    public function Pack($rootdir) 
    {
        $this->GetAllFileDir($rootdir);

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<emdpack>';
        $xml .= '<rootdir>' . $rootdir . '</rootdir>'; //根目录

        //子目录
        foreach ($this->subdirs as $key => $value) {
            $value = str_replace($rootdir, '', $value);
            $value = preg_replace('/[^(\x20-\x7F)]*/', '', $value);
            $xml .= '<folder><path>' . $value . '</path></folder>';
        }
        $this->subdirs = null;

        //文件
        foreach ($this->files as $key => $value) {
            $d = str_replace($rootdir, '', $value);
            $c = base64_encode(file_get_contents($value));
            $xml .= '<file><path>' . $d . '</path><stream>' . $c . '</stream></file>';
        }
        $this->files = null;

        $xml .= '</emdpack>';

        $gzxml = gzdeflate($xml, 9);//压缩

        if (!file_exists(__DIR__ . '/packs')) 
            mkdir(__DIR__ . '/packs', 0755, true);

        $packname = basename($rootdir);
        $put   = file_put_contents(__DIR__ . '/packs/' . $packname . '.pak', $xml);
        $gzput = file_put_contents(__DIR__ . '/packs/' . $packname . '.gpak', $gzxml);


        if ($put && $gzput) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 解包并保存文件
     * @param $packpath 包文件路径
     * @return bool
     */
    public static function UnPack($packpath) 
    {
        ob_clean();
        
        $xml = @file_get_contents($packpath);
        $ext = pathinfo($packpath, PATHINFO_EXTENSION);
        if ($ext == 'gpak') {
            $xml = gzinflate($xml);
        }

        $xml = simplexml_load_string($xml);
        if (!$xml) {
            return false;
        }

        $rootdir = $xml->rootdir;

        if (!file_exists($rootdir)) {
            @mkdir($rootdir, 0755, true);
        }

        foreach ($xml->folder as $folder) {
            $f = $rootdir . $folder->path;
            if (!file_exists($f)) {
                @mkdir($f, 0755, true);
            }
        }

        foreach ($xml->file as $file) {
            $s = base64_decode($file->stream);
            $f = $rootdir . $file->path;
            if (!@file_put_contents($f, $s)) {
                return false;
            }
            if (function_exists('chmod')) {
                @chmod($f, 0755);
            }
        }

        return true;
    }

    /**
     * 递归获取所有子目录和文件
     * @param string $dir 当前目录
     * @private
     * @return void
     */
    private function GetAllFileDir($dir)
    {
        if (function_exists('scandir')) {
            foreach (scandir($dir) as $d) {
                if (is_dir($dir . $d)) {
                    if (substr($d, 0, 1) != '.') {
                        $this->GetAllFileDir($dir . $d . '/');
                        $this->subdirs[] = $dir . $d . '/';
                    }
                } else {
                    $this->files[] = $dir . $d;
                }
            }
        } else {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if (is_dir($dir . $file)) {
                        if (substr($file, 0, 1) != '.') {
                            $this->subdirs[] = $dir . $file . '/';
                            $this->GetAllFileDir($dir . $file . '/');
                        }
                    } else {
                        $this->files[] = $dir . $file;
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * 获取远程包
     * @param string $url 远程地址
     * @return string 包
     */
    public function GetRemotePack($url)
    {
        global $zbp;
        if (class_exists('Network')) {//使用系统的网络连接类
            $ajax = Network::Create();
            $ajax->open('GET', $url);
            $ajax->enableGzip();
            $ajax->setTimeOuts(120, 120, 0, 0);
            $ajax->setRequestHeader('User-Agent', '');
            $ajax->setRequestHeader('Cookie', '');
            $ajax->setRequestHeader('Website',$zbp->host);
            if(isset($_SERVER['HTTP_ACCEPT']))
                $ajax->setRequestHeader('Accept',$_SERVER['HTTP_ACCEPT']);
            if(defined('APPCENTRE_CAN_NOT_HTTPS'))
                $ajax->setRequestHeader('nohttps','true');
            $ajax->send();

            $i = 0;
            while ($i < 3 && ($ajax->status == 301 || $ajax->status == 302) ) {
                $i = $i + 1;
                $ajax->open('GET', $ajax->getResponseHeader('location'));
                $ajax->enableGzip();
                $ajax->setTimeOuts(120, 120, 0, 0);
                $ajax->setRequestHeader('User-Agent', '');
                $ajax->setRequestHeader('Cookie', '');
                $ajax->setRequestHeader('Website',$zbp->host);
                if(isset($_SERVER['HTTP_ACCEPT']))
                    $ajax->setRequestHeader('Accept',$_SERVER['HTTP_ACCEPT']);
                if(defined('APPCENTRE_CAN_NOT_HTTPS'))
                    $ajax->setRequestHeader('nohttps','true');
                $ajax->send();
            }

            return $ajax->responseText;
	    } else { //使用curl
            $ch = curl_init($url);
            if (extension_loaded('zlib')) {
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            if(isset($_SERVER['HTTP_ACCEPT'])){
                curl_setopt($ch,CURLOPT_HTTPHEADER,array (
                    'Accept: ' . $_SERVER['HTTP_ACCEPT']
                ));
            }
            curl_setopt($ch, CURLOPT_USERAGENT, '');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            if (ini_get("safe_mode") == false && ini_get("open_basedir") == false) {
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            }

            $r = curl_exec($ch);
            curl_close($ch);

            return $r;
        }
    }
}
