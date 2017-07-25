<?php
/**
 * @Description  git代码规范服务端php文件
 * @author F。 822460782@qq.com
 * @version 2017-3-27
 * @modify 2017-3-27 17:02:46
 */
header("Content-type: text/html; charset=utf-8");
if(empty($_POST)){
	if(isset($argv[1]) && isset($argv[2]) && $argv[1] == 'version'){
                echo hxEncryptCode($argv[2]);
        }
        exit();
}
echo PHP_EOL . PHP_EOL . PHP_EOL . '代码检测文档地址：' . PHP_EOL . '' . PHP_EOL;
include_once('../config/function.php');
$version = isset($_POST['version']) ? $_POST['version'] : '';
$version_passwd = isset($_POST['version_passwd']) ? $_POST['version_passwd'] : '';
$this_version = getVersion();
$check_passwd = hxEncryptCode($this_version) == $version_passwd ? true : false;
$check = hxDecryptionCode($version_passwd) == $version ? true : false;
if($check !== true || $check_passwd !== true){
	echo PHP_EOL.PHP_EOL;
	echo '当前版本：V2.1 正式版；' . PHP_EOL.PHP_EOL . '代码检测提交脚本（init.sh）的版本库（V2.2 正式版）已经更新。' .PHP_EOL . '请重新执行该文件进行初始化或者下载（http://******/PHP/CheckServer/download/init.sh）最新版本并且初始化脚本，再进行提交！'.PHP_EOL.PHP_EOL . '更新日志：' . PHP_EOL;
	echo 'v2.2' . PHP_EOL . '1.新增git默认转CRLF文件格式的检测，修改为默认LF格式。' . PHP_EOL;
	echo 'v2.1' . PHP_EOL . '1.修复用户提交时，检测不到当前提交用户信息的bug。' . PHP_EOL;
	echo PHP_EOL.PHP_EOL;
	exit();
}
$token = isset($_POST['token']) ? $_POST['token'] : '';
if($token != 'CodeSniffer'){
	echo PHP_EOL.PHP_EOL;
	echo 'hooks配置错误'.PHP_EOL.PHP_EOL;
	echo PHP_EOL.PHP_EOL;
	exit();
}
$user = isset($_POST['user']) ? str_replace(PHP_EOL, '', $_POST['user']) : '';
$email = isset($_POST['email']) ? str_replace(PHP_EOL, '', $_POST['email']) : '';
$remote = isset($_POST['remote']) ? str_replace(PHP_EOL, '', $_POST['remote']) : '';
if (empty($user) || empty($email) || empty($remote)) {
	echo PHP_EOL.PHP_EOL;
	echo 'ERROR: Please "git config --global user.name" OR "git config --global user.email" OR "git clone"'.PHP_EOL.PHP_EOL;
	echo PHP_EOL.PHP_EOL;
	exit();
}
echo PHP_EOL.PHP_EOL;
echo 'Gitlab PHP 代码检测结果：（单个文件错误数大于0或者警告数大于50请修改！）'.PHP_EOL.PHP_EOL;
echo PHP_EOL.PHP_EOL;
$post_content = isset($_POST['content']) ? json_decode($_POST['content'],true) : [];
$client_content = [];
$special_folder = specialFolder();
foreach($post_content as $post_content_key => $post_content_value) {
	if(preg_match('/\.php$/',$post_content_key)){
		$views_path = explode('/',$post_content_key);
		$insert = true;
		foreach ($views_path as $views_path_key){
			if (in_array($views_path_key,$special_folder)) {
				$insert = false;
				break;
			}
		}
		$client_content[$post_content_key] = ($insert === true) ? $post_content_value : '';
	}
}
$file_name = $user . '_' . $email . '_' . $remote;
include_once('phpcs-git-client-check');
exit();
