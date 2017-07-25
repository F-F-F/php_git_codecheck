<?php
/**
 * @Description  git代码规范服务端php文件
 * @author F。 822460782@qq.com
 * @version 2017-3-27
 * @modify 2017-3-27 17:02:46
 */
header("Content-type: text/html; charset=utf-8");
include_once('../config/function.php');
if(empty($_POST)){
	if(isset($argv[1]) && isset($argv[2]) && $argv[1] == 'version'){
		echo hxEncryptCode($argv[2]);
	}
	exit();
}
$version = isset($_POST['version']) ? $_POST['version'] : '';
$version_passwd = isset($_POST['version_passwd']) ? $_POST['version_passwd'] : '';
$this_version = getVersion();
$check_passwd = hxEncryptCode($this_version) == $version_passwd ? true : false;
$check = hxDecryptionCode($version_passwd) == $version ? true : false;
if($check !== true || $check_passwd !== true){
	echo 1;
	exit();
}
$token = isset($_POST['token']) ? $_POST['token'] : '';
if($token != 'CodeSniffer'){
	echo 1;
	exit();
}
$user = isset($_POST['user']) ? str_replace(PHP_EOL, '', $_POST['user']) : '';
$email = isset($_POST['email']) ? str_replace(PHP_EOL, '', $_POST['email']) : '';
$remote = isset($_POST['remote']) ? str_replace(PHP_EOL, '', $_POST['remote']) : '';
if (empty($user) || empty($email) || empty($remote)) {
        echo 1;
        exit();
}
$client_content = isset($_POST['content']) ? json_decode($_POST['content'],true) : [];
$file_name = $user . '_' . $email . '_' . $remote;
if(in_array(basename($remote),specialProjects())){
	echo 0;
	exit();
}
$result = getData($file_name,array_keys($client_content));
if(isset($result['result'])){
	foreach($result['result'] as $result_key){
		if($result_key['error'] != 0 || $result_key['warn'] > 50){
			echo 1;
			exit();
		}
	}
	echo 0;
	exit();
}
echo 1;
exit();
