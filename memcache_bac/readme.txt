Xampp v3.2.1 ��װ Memcached for windows

1.����PHP��Ӧ�汾��php_memcache.dll,�ҵ�PHP 5.6.3 

�������� ������phpinfo�������Ϣ���ҳ�ƥ��İ汾��

��1����Architecture,�Ƕ��٣�x86������x86��ģ������ʾ�Ͳ���ϵͳ�Ķ���λû��ëǮ��ϵ����Ҫ���ݲ���ϵͳ�Ķ���λ�����أ����Ǹ������Ҿͱ����ˡ�

��2����thread safe �������enable ,���ڵľ���ts��ģ��������nts��ġ�

��3����Compiler���ĺ�׺��һ�����vc11�����������ض�Ӧ�İ汾


php_memcache-3.0.8-5.6-ts-vc11-x86.zip

http://windows.php.net/downloads/pecl/releases/memcache/3.0.8/
2.�޸�D:/xampp/php/php.ini
��ѹ�������php_memcache.dll��ѹ����D:\xampp\php\ext

��php.ini����

extension=php_memcache.dll
����ĩ�����

[Memcache]
memcache.allow_failover = 1
memcache.max_failover_attempts=20
memcache.chunk_size =8192
memcache.default_port = 11211

3.����memcached

http://blog.couchbase.com/memcached-windows-64-bit-pre-release-available
�Թ���Ա�������cmd������d:\xampp\memcached

memcached.exe �Cd install //��װ
memcached.exe �Cd start //����