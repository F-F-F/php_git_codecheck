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
	exit();
}
$result = array('status' => 1,'msg' => '');
$token = isset($_POST['token']) ? $_POST['token'] : '';
if($token != 'CodeSniffer'){
	$result['msg'] = 'hooks配置错误';
	echo json_encode($result);
	exit();
}
$refs = isset($_POST['refs']) ? trim(str_replace(PHP_EOL, '', $_POST['refs'])) : '';
$remote = isset($_POST['remote']) ? trim(str_replace(PHP_EOL, '', $_POST['remote'])) : '';
$user_id = isset($_POST['user_id']) ? trim(str_replace(PHP_EOL, '', $_POST['user_id'])) : '';
$this_user_id = str_replace('user-','',$user_id);
if(!empty($this_user_id) && in_array($this_user_id,getNoUser())){
	$result['status'] = 0;
	$result['msg'] = '非检测用户！';
        echo json_encode($result);
        exit();
}
if (empty($refs) || strtolower(substr($remote,-4)) != '.git') {
	$result['msg'] = '推送信息异常！';
	echo json_encode($result);
	exit();
}
$gitlab_api = getGitlabApi();
$project_id = getGitlabProjectId(basename($remote,'.git'));
if (empty($project_id)) {
        $result['msg'] = basename($remote) . ':项目不存在！请联系管理员添加对应的项目配置！';
        echo json_encode($result);
        exit();
}
$git_project_info_json = shell_exec('curl ' . $gitlab_api['api'] . 'projects/' . $project_id . '/repository/commits/' . $refs. '?private_token=' . $gitlab_api['token']);
$git_project_info = json_decode($git_project_info_json,true);
if (empty($git_project_info['author_name']) || empty($git_project_info['author_email'])) {
        $result['msg'] = basename($remote) . ':项目中，该次提交用户不存在！';
        echo json_encode($result);
        exit();
}
if(in_array(basename($remote),specialProjects())){
        $result['status'] = 0;
        $result['msg'] = '特殊非检测项目！';
        echo json_encode($result);
        exit();
}
$file_name = $git_project_info['author_name'] . '_' . $git_project_info['author_email'] . '_' . basename($remote);
$data_path = getDataPath();
$save_file = '/gitCheckData';
if(is_file($data_path . $file_name . $save_file) !== true){
	$result['msg'] = '请重新提交并检测你要推送的所有文件！你的信息为：用户id：' .$user_id . '_' . $file_name;
	echo json_encode($result);
        exit();
}
$f = fopen($data_path . $file_name . $save_file,'r');
while(!feof($f)){
	$line = fgets($f);
	if(strlen($line) > 0){
		preg_match('(<(.*)>:(.*):(.*))',$line,$line_info);
		if(empty($line_info[1]) || !isset($line_info[2]) || !isset($line_info[3])){
	                $result['msg'] = '请重新提交并检测你要推送的所有文件！';
		        echo json_encode($result);
			exit();
	        }
	        if($line_info[2] > 0){
	                $result['msg'] .= $line_info[1] . ' 文件检查不通过：错误数为：' . $line_info[2] . '，请修改并重新提交！' . PHP_EOL;
	        }else{
			$result['msg'] .= '';
		}
	        if($line_info[3] > 50){
	                $result['msg'] .= $line_info[1] . ' 文件检查不通过：警告数为：' . $line_info[3] . '，请修改并重新提交！' . PHP_EOL;
        	}else{
			$result['msg'] .= '';
		}
	}else{
		$result['msg'] .= '';
	}
}
if(empty(trim($result['msg']))){
	$result['status'] = 0;
	$result['msg'] = $user_id;
}
echo json_encode($result);
exit();
