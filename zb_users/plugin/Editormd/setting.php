<?php
/**
* Editor.md 配置处理
*
* Editor.md for Z-BlogPHP
*
* @author      心扬<https://app.zblogcn.com/?auth=2ffbff0a-1207-4362-89fb-d9a780125e0a>
* @version     2.7
*/
header('Content-Type: application/json');
header('Content-Type: text/html;charset=utf-8');

require __DIR__ . '/../../../zb_system/function/c_system_base.php';
require __DIR__ . '/../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    echo JSON_msg_return(false, '没有访问权限!');
    die();
}
if (!$zbp->CheckPlugin('Editormd')) {
    echo JSON_msg_return(false, '插件未启用!');
    die();
}

if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST') {
    if ($_POST['type'] == 'theme') {
        if(isset($_POST["toolbartheme"]))$zbp->Config('Editormd')->toolbartheme = $_POST["toolbartheme"];
        if(isset($_POST["previewtheme"]))$zbp->Config('Editormd')->previewtheme = $_POST["previewtheme"];
        if(isset($_POST["editortheme"]))$zbp->Config('Editormd')->editortheme = $_POST["editortheme"];
        $zbp->SaveConfig('Editormd');
        echo JSON_msg_return(true, '主题保存成功!');
    } elseif ($_POST['type'] == 'setting') {
        if(isset($_POST["dynamictheme"]))$zbp->Config('Editormd')->dynamictheme = $_POST["dynamictheme"];
        if(isset($_POST["preview"]))$zbp->Config('Editormd')->preview = $_POST["preview"];
        if(isset($_POST["codetheme"]))$zbp->Config('Editormd')->codetheme = $_POST["codetheme"];
        if(isset($_POST["autoheight"]))$zbp->Config('Editormd')->autoheight = $_POST["autoheight"];
        if(isset($_POST["scrolling"]))$zbp->Config('Editormd')->scrolling = $_POST["scrolling"];
        if(isset($_POST["emoji"]))$zbp->Config('Editormd')->emoji = $_POST["emoji"];
        if(isset($_POST["htmldecode"]))$zbp->Config('Editormd')->htmldecode = $_POST["htmldecode"];
        if(isset($_POST["htmlfilter"]))$zbp->Config('Editormd')->htmlfilter = $_POST["htmlfilter"];
        if(isset($_POST["extras"]))$zbp->Config('Editormd')->extras = $_POST["extras"];
        if(isset($_POST["tocm"]))$zbp->Config('Editormd')->tocm = $_POST["tocm"];
        if(isset($_POST["tasklist"]))$zbp->Config('Editormd')->tasklist = $_POST["tasklist"];
        if(isset($_POST["flowchart"]))$zbp->Config('Editormd')->flowchart = $_POST["flowchart"];
        if(isset($_POST["katex"]))$zbp->Config('Editormd')->katex = $_POST["katex"];
        if(isset($_POST["sdiagram"]))$zbp->Config('Editormd')->sdiagram = $_POST["sdiagram"];
        if(isset($_POST["mipsupport"]))$zbp->Config('Editormd')->mipsupport = $_POST["mipsupport"];
        $zbp->SaveConfig('Editormd');
        echo JSON_msg_return(true, '配置保存成功!');
    }
} elseif (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'delay') {//暂停获取扩展包
            $zbp->Config('Editormd')->getpack = 'delay';
            $zbp->SaveConfig('Editormd');
        } elseif ($_GET['action'] == 'getpack') {//重新获取扩展包
            $zbp->Config('Editormd')->getpack = 'true';
            $zbp->SaveConfig('Editormd');
        } elseif ($_GET['action'] == 'pack') {//打包
            include __DIR__ . '/lib/EmdPack.class.php';
            $emdpack = new EmdPack;
            if (!$emdpack->Pack('images/github-emojis/') || !$emdpack->Pack('lib/katex/'))
                echo JSON_msg_echo(false, '打包失败！');
            else
                echo JSON_msg_return(true, '打包成功！');
        }
    } else {
        $result = GetPack();
        if ($result[0]) {
            $zbp->Config('Editormd')->getpack = 'false';
            $zbp->SaveConfig('Editormd');
            echo JSON_msg_return(true, $result[1]);
        } else {
            $zbp->Config('Editormd')->getpack = 'true';
            $zbp->SaveConfig('Editormd');
            echo JSON_msg_return(false, $result[1]);
        }
    }
} else {
    echo JSON_msg_return(false, '非法请求!');
    die();
}

/**
 * 获取远程包并解压
 * @return array
 */
function GetPack()
{
    include __DIR__ . '/lib/EmdPack.class.php';
    $emdpack = new EmdPack;

    $urls = array();
    if (function_exists('gzinflate')) {//是否支持解压
        $urls[] = 'http://oznuv0a19.bkt.clouddn.com/editormd-ext-packs/github-emojis.gpak';
        $urls[] = 'http://oznuv0a19.bkt.clouddn.com/editormd-ext-packs/katex.gpak';
    } else {
        $urls[] = 'http://oznuv0a19.bkt.clouddn.com/editormd-ext-packs/github-emojis.pak';
        $urls[] = 'http://oznuv0a19.bkt.clouddn.com/editormd-ext-packs/katex.pak';
    }

    $pack_sha1 = array(
        'github-emojis.gpak' => '9c5619eb75bf68c3f8fc02509858ebe630c6ca43',
        'github-emojis.pak'  => 'b6bed789dd47fc85db5d79759e853e9888c41e88',
        'katex.gpak'         => 'c2925cfea996244897157acd3f7ae5e2c7659d16',
        'katex.pak'          => '4603ee64623ac1dc9a3e22bb436cfa11b494bdd3'
    );
    $pack_md5  = array(
        'github-emojis.gpak' => '7beec1139c0d5b6db7b7c4e115f612a5',
        'github-emojis.pak'  => 'ce9e574c652391bb412cb4af1401cff2',
        'katex.gpak'         => '6f88cc607890b1197230a91a685f9d35',
        'katex.pak'          => '2d9d5801613e1b74513d22f690437739',
    );

    if (!file_exists(__DIR__ . '/lib/packs')) 
        if (!@mkdir(__DIR__ . '/lib/packs', 0755, true))
            return array(false, '创建文件夹出错！请检查服务器的文件读写权限！');
    
    $packs = array();
    foreach ($urls as $url) {
        $filename = basename($url);
        if (!file_exists(__DIR__ . '/lib/packs/' . $filename)) {
            $pack = $emdpack->GetRemotePack($url); //下载包
            if (empty($pack))
                return array(false, '下载扩展包出错！请检查服务器的网络状态！');
            //写入文件
            if (!@file_put_contents(__DIR__ . '/lib/packs/' . $filename, $pack))
                return array(false, '保存扩展包出错！请检查服务器的文件读写权限！');
        } else {
            $pack = @file_get_contents(__DIR__ . '/lib/packs/' . $filename);
        }
        //进行文件校验
        $checksum = true;
        if (function_exists('sha1')) {
            if (sha1($pack) !== $pack_sha1[$filename])
                $checksum = false;
        } elseif (function_exists('md5')) {
            if (md5($pack) !== $pack_md5[$filename])
                $checksum = false;
        } else {
            $checksum = false;
        }
        if (!$checksum)
            return array(false, '文件校验出错！文件可能被篡改，请停止获取扩展包并及时联系开发者！');
        
        $packs[] = $filename;
    }

    //解包
    foreach ($packs as $p) {
        if (!$emdpack->UnPack(__DIR__ . '/lib/packs/' . $p))
            return array(false, '解开扩展包出错！请检查服务器的文件读写权限！');
    }

    if (file_exists(__DIR__ . '/images/github-emojis') && file_exists(__DIR__ . '/lib/katex')) 
        return array(true, '成功获取扩展包！');
    else
        return array(false, '获取扩展包失败！');
}

/**
 * 返回JSON消息
 * @return string
 */
function JSON_msg_return($status, $msg)
{
    $arr = array(urlencode($status), urlencode($msg));

    return urldecode(json_encode($arr));
}
?>
