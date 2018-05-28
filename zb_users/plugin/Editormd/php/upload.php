<?php

	/*
	 * PHP upload for Editor.md
     *
     * @FileName: upload.php
     * @Auther: Pandao
     * @E-mail: pandao@vip.qq.com
     * @CreateTime: 2015-02-13 23:20:04
     * @UpdateTime: 2015-02-14 14:52:50
     * Copyright@2015 Editor.md all right reserved.
	 */

    //header("Content-Type:application/json; charset=utf-8"); // Unsupport IE
    //header("Content-Type:text/html; charset=utf-8");
    //header("Access-Control-Allow-Origin: *");

    require __DIR__ . '/../../../../zb_system/function/c_system_base.php';
    require __DIR__ . '/../../../../zb_system/function/c_system_admin.php';

    $zbp->Load();
    $action = 'UploadPst';
    if (!$zbp->CheckRights($action)) {
        $zbp->ShowError(6);
        die();
    }

    $upload_dir = 'zb_users/upload/' . date('Y/m') . '/';
    $savePath = ZBP_PATH . $upload_dir;
    $saveURL = $bloghost . $upload_dir;
    $max_size = $zbp->option['ZC_UPLOAD_FILESIZE'] * 1024; //kB

    require __DIR__ . "/editormd.uploader.class.php";

    //error_reporting(E_ALL & ~E_NOTICE);
	
	//$path     = __DIR__ . DIRECTORY_SEPARATOR;
	//$url      = dirname($_SERVER['PHP_SELF']) . '/';
	//$savePath = realpath($path . '../uploads/') . DIRECTORY_SEPARATOR;
	//$saveURL  = $url . '../uploads/';

	$formats  = array(
		'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp')
	);

    $name = 'editormd-image-file';

    if (isset($_FILES[$name])) {        
        $imageUploader = new EditorMdUploader($savePath, $saveURL, $formats['image'], false); 
        
        $imageUploader->config(array(
            'maxSize' => $max_size,        // 允许上传的最大文件大小，以KB为单位，默认值为1024
            'cover'   => true         // 是否覆盖同名文件，默认为true
        ));
        
        if ($imageUploader->upload($name)) {
            $imageUploader->message('上传成功！', 1);
        } else {
            $imageUploader->message('上传失败！', 0);
        }
    }
?>
