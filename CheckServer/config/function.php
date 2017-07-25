<?php
/**
 * @Description  git代码规范服务端php文件，对称加密
 * @author F。 822460782@qq.com
 * @version 2017-3-27
 * @modify 2017-3-27 17:02:46
 */

/**
 * 全局，获取gitlab API地址与最高权限的token，F-F-F：修改gitlab地址
 * @return String
 */
function getGitlabApi(){
    return ['api' => 'http://******/api/v3/', 'token' => 'kMVxGft54Pbn5oL5H8Wd'];
}

/**
 * 全局，获取版本
 * @return String
 */
function getVersion(){
    return 'v2.2';
}

/**
 * 全局，根据gitlab成员id，获取非检测人员列表，F-F-F：修改gitlab对应的成员id
 * @return String
 */
function getNoUser(){
    return [];
}

/**
 * 全局，特殊项目只提示错误与警告，不做拦截，F-F-F：修改gitlab对应的项目
 * @return String
 */
function specialProjects(){
    return ['test.git'];
}

/**
 * 全局，忽略的文件夹
 * @return String
 */
function specialFolder(){
    return ['views','Views','Plugins','plugins','Qrcode'];
}

/**
 * 全局，获取gitlab项目id，F-F-F：修改gitlab对应的项目id
 * @param String $name 项目名称
 * @return String
 */
function getGitlabProjectId($name){
    $list = [
		'test' => '245', 
	];
	return empty($list[$name]) ? '' : $list[$name];
}

/**
 * 全局，获取数据存储路径，F-F-F：修改储存文件对应的物理路径
 * @return String
 */
function getDataPath(){
	return '/data/webapp/default/PHP/CheckServer/data/';
}

/**
 * 通用加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @return String
 */
function hxEncryptCode($string = '', $skey = 'CodeSnifferCheck') {
    $skey = array_reverse(str_split($skey));
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach ($skey as $key => $value) {
        $key < $strCount && $strArr[$key].=$value;
    }
    return str_replace('=', 'O0O0O', join('', $strArr));
}

/**
 * 通用解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String
 */
function hxDecryptionCode($string = '', $skey = 'CodeSnifferCheck') {
    $skey = array_reverse(str_split($skey));
    $strArr = str_split(str_replace('O0O0O', '=', $string), 2);
    $strCount = count($strArr);
    foreach ($skey as $key => $value) {
        $key < $strCount && $strArr[$key] = $strArr[$key][0];
    }
    return base64_decode(join('', $strArr));
}

/**
 * 获取文件信息
 * @param String $user 用户名
 * @param String $email 邮箱
 * @param array  $file 文件名
 * @return array
 */
function getFileArray($file){
    $file_array = [];
	$special_folder = specialFolder();
	if(is_array($file)){
		foreach ($file as $file_key){
         	if(preg_match('/\.php$/',$file_key)){
				$views_path = explode('/',$file_key);
				foreach ($views_path as $views_path_key){
					if (!in_array($views_path_key,$special_folder)) {
						$file_array[] = $file_key;
					}
				}
			}
		}
	}
    return $file_array;
}

/**
 * 取数据
 * @param String $file_name 用户文件名
 * @param array  $file 文件名
 * @return array
 */
function getData($file_name,$file){
	$data_path = getDataPath();
	$result = [];
	$save_flie = '/gitCheckData';
	$file_array = getFileArray($file);
	if (file_exists($data_path . $file_name) === true) {
		if(empty($file_array)){
			$result['result'] = [];
		}else{
			if (is_file($data_path . $file_name . $save_flie) === true) {
				foreach($file_array as $file_key){
					$sh = [];
					exec('grep "<' . $file_key . '>" ' . $data_path . $file_name . $save_flie,$sh);
					if(!empty($sh[0])){
						preg_match('(:(.*):(.*))',$sh[0],$sh_res);
							$result['result'][$file_key] = ['error' => $sh_res[1], 'warn' => $sh_res[2]];
					}else{
						$result['result'][$file_key] = ['error' => 0, 'warn' => 0];
					}
				}
			}else{
				$result['error_file'] = true;
			}
		}
	}else{
		$result['error_path'] = true;
	}
    return $result;
}
