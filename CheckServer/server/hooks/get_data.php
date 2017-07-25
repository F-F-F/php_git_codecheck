<?php
/**
 * @Description  git代码规范服务端php文件
 * @author F。 822460782@qq.com
 * @version 2017-4-5
 * @modify 2017-4-5 15:02:46
 */
//F-F-F：修改配置的访问地址
//服务端url地址
echo PHP_EOL . PHP_EOL . PHP_EOL . '代码检测文档地址：' . PHP_EOL . '' . PHP_EOL;
$url = 'http://****/PHP/CheckServer/server/get_server.php';
$refs = empty($argv[4]) ? '' : $argv[4];
$remote = empty($argv[1]) ? '' : $argv[1];
$user_id = empty($argv[2]) ? '' : $argv[2];
if(empty($refs) || empty($remote) || empty($user_id)){
	echo PHP_EOL.PHP_EOL;
	echo '服务端配置错误！';
	echo PHP_EOL.PHP_EOL;
	exit(1);
}
//F-F-F：修改配置的日志
$file_log = '/home/git/gitlab-shell/hooks/log.txt';
$log = basename($remote,'.git') . '_' . $user_id . '_' . $remote . PHP_EOL;
//过滤检测,F-F-F：修改gitlab的项目
$projects_array = array('test');
if(!in_array(basename($remote,'.git'),$projects_array)){
	file_put_contents($file_log,'NO >> ' . $log,FILE_APPEND);
        exit(0);
}
file_put_contents($file_log,'YES >> ' . $log,FILE_APPEND);
//检测
$post_data = array(
	'token' => 'CodeSniffer',
	'refs' => $refs,
	'remote' => $remote,
	'user_id' => $user_id
);
//检测
$server_ch = curl_init();
curl_setopt($server_ch, CURLOPT_URL, $url);
curl_setopt($server_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($server_ch, CURLOPT_POST, 1);
curl_setopt($server_ch, CURLOPT_POSTFIELDS, $post_data);
$result = json_decode(curl_exec($server_ch),true);
curl_close($server_ch);
if(isset($result['status']) && $result['status'] == 0){
	echo PHP_EOL . '提示信息：' . $result['msg'] . PHP_EOL;
	exit(0);
}
echo PHP_EOL.PHP_EOL;
echo '------------------------------------------------------------------';
echo PHP_EOL.PHP_EOL;
echo '错误信息为：' . PHP_EOL . $result['msg'];
echo PHP_EOL.PHP_EOL;
echo '------------------------------------------------------------------';
echo PHP_EOL.PHP_EOL;
exit(1);
