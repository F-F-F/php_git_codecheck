#!/bin/sh
# @Description  git代码规范客户端sh文件，初始化脚本文件
# @author F。 822460782@qq.com
# @version 2017-3-27
# @modify 2017-3-27 17:02:46
echo "--Which do you want to? Input the number."
echo "1. init this"
read num

if [ $num == 1 ]
then
	if [ -f 'function.sh' ]; then
		rm -rf 'function.sh'
	fi
	#F-F-F：修改配置的下载地址
    curl -Os 'http://****/PHP/CheckServer/download/function.sh';chmod 777 function.sh;sh function.sh;
else
	echo "Nothing to do,Exit!"
fi
