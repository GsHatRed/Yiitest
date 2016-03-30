Xampp v3.2.1 安装 Memcached for windows

1.下载PHP对应版本的php_memcache.dll,我的PHP 5.6.3 

所以下载 ，根据phpinfo输出的信息来找出匹配的版本：

（1）看Architecture,是多少，x86就下载x86版的，这个显示和操作系统的多少位没半毛钱关系，不要根据操作系统的多少位来下载，这是个炕，我就被坑了。

（2）看thread safe ，如果是enable ,对于的就是ts版的，否则就是nts版的。

（3）看Compiler，的后缀，一般带有vc11的字样，下载对应的版本


php_memcache-3.0.8-5.6-ts-vc11-x86.zip

http://windows.php.net/downloads/pecl/releases/memcache/3.0.8/
2.修改D:/xampp/php/php.ini
将压缩包里的php_memcache.dll解压缩到D:\xampp\php\ext

在php.ini增加

extension=php_memcache.dll
并在末行添加

[Memcache]
memcache.allow_failover = 1
memcache.max_failover_attempts=20
memcache.chunk_size =8192
memcache.default_port = 11211

3.下载memcached

http://blog.couchbase.com/memcached-windows-64-bit-pre-release-available
以管理员身份运行cmd，进入d:\xampp\memcached

memcached.exe –d install //安装
memcached.exe –d start //启动