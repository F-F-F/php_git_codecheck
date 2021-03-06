# php_git_codecheck

# 简介
- 最新版本：2.2正式版
- 脚本运行环境：windows（带git命令行）、linux内核
- 代码检测运行环境：php5.5+
- 原理：客户端下载初始化脚本，在本地.git文件的钩子文件中生成pre-commit文件，之后客户端每次提交之前采用curl方式，将文件内容发给服务端，检测完代码之后做相应操作，服务端检测用户是否在客户端检测过并且检测是否通过。

# 目录结构与详解

- [ CheckServer ] ----------------------------代码检测主目录
  - [ client ] ------------------------------客户端获取数据目录
    - [ get_client.php ] ----------------二次获取从客户端发来的用户数据请求，并获取储存数据
    - [ phpcs-git-client-check ] -------客户端的数据通过该文件发送给PHP_CodeSniffer主程序
    - [ save_client.php ] ---------------获取从客户端发来的数据请求，并将数据发送给PHP_CodeSniffer主程序的中间件
  - [ config ] ------------------------------配置文件信息目录
    - [ function.php ] ------------------配置文件，记录配置信息
    - [ save_data.sh ] ------------------存贮检测后的结果数据
  - [ data ] --------------------------------存贮检测后的数据结果目录
  - [ download ] --------------------------附属文件下载目录
    - [ function.sh ] --------------------初始化脚本执行时的关联文件
    - [ go-pear.phar ] ------------------windows端安装pear时的安装包
    - [ init.sh ] --------------------------初始化脚本文件
    - [ phpcs-git-client-pre-commit ] --初始化之后的客户端的用于发送数据的文件
    - [ pre-commit ] --------------------初始化之后的客户端的提交钩子文件
  - [ server ] --------------------------------服务端获取数据目录
    - [ get_server.php ] -----------------获取从服务端发来的用户数据请求，并获取储存数据
    - [ hooks ] ---------------------------服务端所有钩子存放目录
      - [ get_data.php ] --------------服务端根据用户信息获取检测结果
      - [ log.txt ] ----------------------服务端根据用户信息获取检测结果的日志文件
      - [ post-receive ] ---------------服务端的钩子文件
      - [ pre-receive ] ----------------服务端的钩子文件
      - [ update ] ---------------------服务端的钩子文件
  - [ clean.sh ] ------------------------------代码检测时用于清理的定时计划脚本文件，需要加入适当的定时计划
    - [ log.txt ] ---------------------------定时计划的日志文件
- [ CodeSniffer ] -------------------------------PHP_CodeSniffer主程序目录
- [ CodeSniffer.php ] --------------------------PHP_CodeSniffer主程序入口文件



# 附：SVN代码检测文档

地址：http://www.aabiji.com/notes/notes/id/12.html
