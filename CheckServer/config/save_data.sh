#!/bin/sh
# @Description  git代码规范服务端sh文件，存放数据
# @author F。 822460782@qq.com
# @version 2017-3-27
# @modify 2017-3-27 17:02:46
FileName=$1
Status=$2
Files=$3
SaveFile='gitCheckData'
cd ../data
if [ ! -d $FileName ]; then
        mkdir $FileName
fi
cd $FileName
if [ ! -f $SaveFile ]; then
	touch $SaveFile
fi
CheckStr='<'$Files'>'
ReplaceStr=$CheckStr':'$Status
if 
	#检测是否存在该字符串
	grep $CheckStr $SaveFile
then
	if [ $Status != '0:0' ]; then 
		#修改
		sed -i "s#.*$CheckStr.*#$ReplaceStr#" $SaveFile
	else
		#删除
		sed -i 's#'$CheckStr'#DELETEVALUE#;/DELETEVALUE/d' $SaveFile
	fi
else
	if [ $Status != '0:0' ]; then
		echo $ReplaceStr >> $SaveFile
	fi
fi

