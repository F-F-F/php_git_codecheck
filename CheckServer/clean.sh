#!/bin/sh
#F-F-F：修改配置物理路径
cd '/data/webapp/default/PHP/CheckServer'
ThisPath=$(pwd)
FindData=$(find data -name gitCheckData)
for Path in $FindData
do
	SaveFile=$ThisPath'/'$Path
	if [ ! -s $SaveFile ]; then
		rm -rf $SaveFile
	fi
done
#log
echo `date` >> '/data/webapp/default/PHP/CheckServer/log.txt'
