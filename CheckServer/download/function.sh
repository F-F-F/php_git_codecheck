#!/bin/sh
# @Description  git代码规范客户端sh文件，初始化脚本文件
# @author F。 822460782@qq.com
# @version 2017-3-27
# @modify 2017-3-27 17:02:46
#F-F-F：修改配置的下载地址
function init(){
	GlobelHooks='hooks'
	GlobelDownSh='http://****/PHP/CheckServer/download/pre-commit'
	GlobelDownPHP='http://****/PHP/CheckServer/download/phpcs-git-client-pre-commit'
	ThisPath=$(pwd)
	FindGit=$(find . -name .git)
	i=0
	NewPathArr=()
	php=${php//'\'/'/'}
	Return=0
	for Path in $FindGit
	do
		((i++))
		NewPath=${ThisPath}'/'${Path#./}
		NewPathArr[$i]=$NewPath
		cd $NewPath;
		if [ ! -d $GlobelHooks ]; then
			mkdir $GlobelHooks
		fi
		cd $GlobelHooks
		if [ -f 'pre-commit' ]; then
			rm -rf 'pre-commit'
		fi
		if [ -f 'phpcs-git-client-pre-commit' ]; then
			rm -rf 'phpcs-git-client-pre-commit'
		fi
		curl -Os $GlobelDownSh
		if [ -f 'pre-commit' ]; then
			chmod 777 'pre-commit'
		fi
		curl -Os $GlobelDownPHP
		if [ -f 'phpcs-git-client-pre-commit' ]; then
			
			sed -i '1c\#!'$php 'phpcs-git-client-pre-commit'
		fi
		Return=1
	done
	for CheckPath in ${NewPathArr[*]}
	do
		cd ${CheckPath}'/'${GlobelHooks}
		if [ ! -f 'pre-commit' ]; then
			Return=0
			echo '-----------------------------------------------------------------------------'
			echo 'this path :'${CheckPath#.git}' ,initialization failed!(.sh)'
			echo '-----------------------------------------------------------------------------'
		fi
		if [ ! -f 'phpcs-git-client-pre-commit' ]; then
			Return=0
			echo '------------------------------------------------------------------------------'
			echo 'this path :'${CheckPath#.git}' ,initialization failed!(.php)'
			echo '------------------------------------------------------------------------------'
		fi
	done
	if [ $Return == 1 ] 
	then
		echo '///////////////////////'
		echo '/CodeDetection/for/PHP/'
		echo '///////////////////////'
		echo '///////Initial Success/'
		echo '///////////////////////'
		echo '//////////////By-LiBei/'
		echo '///////////////////////'
	else
		echo '///////////////////////'
		echo '/CodeDetection/for/PHP/'
		echo '///////////////////////'
		echo '///////Initial Failure/'
		echo '///////////////////////'
		echo '//////////////By-LiBei/'
		echo '///////////////////////'
	fi
	cd $ThisPath;rm -rf 'function.sh'
}
function uninstall(){
	GlobelHooks='hooks'
	ThisPath=$(pwd)
        FindGit=$(find . -name .git)
        for Path in $FindGit
        do
                NewPath=${ThisPath}'/'${Path#./}
                cd $NewPath;
                if [ -d $GlobelHooks ]; then
                	cd $GlobelHooks
	                if [ -f 'pre-commit' ]; then
	                        rm -rf 'pre-commit'
	                fi
	                if [ -f 'phpcs-git-client-pre-commit' ]; then
	                        rm -rf 'phpcs-git-client-pre-commit'
	                fi
		fi
        done
	echo '///////////////////////'
        echo '/CodeDetection/for/PHP/'
        echo '///////////////////////'
        echo '/////Uninstall Success/'
        echo '///////////////////////'
        echo '//////////////By-LiBei/'
        echo '///////////////////////'
	cd $ThisPath;rm -rf 'function.sh'
}
echo "--Please enter your php.exe path! Do not bring Chinese!!"
echo "--OR enter an empty exit!"
echo "--OR enter 'uninstall' to uninstall it!"
read -r php

if [ ! $php ]
then
	echo "Nothing to do,Exit!"
else
	if [ $php == 'uninstall' ]; then
                uninstall
        else
                init
        fi
fi

