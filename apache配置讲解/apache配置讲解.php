1、缺省apache不允许访问http目录(没有定义，就没有访问权限)
例如：访问目录http://192.168.1.12/test/
会显示：
Forbidden
You don't have permission to access /test/ on this server.

2、<Directory ></Directory>中指令的含义
例如：
 <Directory "/home/macg/www/test">
        Options All
        AllowOverride all
</Directory>

Options指令-------目录的访问特性
option  none    禁止对目录的所有操作
option all      允许对目录的所有操作，ALL---所有操作
option ExecCGI    对该目录，可以执行cgi脚本
option Indexes    允许访问该目录（而该目录没有index.html）时，返回目录下的文件列表
option FollowSymLinks       只允许对目录的FollowSymLinks操作

AllowOverride指令
None    不读取.htaccess
all    all----允许.htaccess所有指令，缺省是all
Limit    .htaccess函盖具体限定的主机(allow,deny)
AuthConfig    .htaccess函盖跟认证有关指令(AuthType,AuthName)

注意：
<Directory ></Directory> 对其下面的所有子目录也生效




3、httpd.conf中先对根目录/进行配置，等于是设置缺省配置

<Directory />
    Options FollowSymLinks  #禁止对目录的访问（option只允许对目录FollowSymLinks操作）
    AllowOverride None      #不读取.htaccess
    Order deny,allow        #deny all
    Deny from all
</Directory>


<Directory "E:/phpspace">
    Options Indexes FollowSymLinks   只允许访问index和连接
    AllowOverride none
    Order allow,deny       承接父目录（/）的deny all,这里也是deny all
    Allow from all
</Directory>

注意：
order allow deny ————-httpd.conf中封IP之类的操作

Apache模块 mod_authz_host
<Directory /docroot>
    Order Deny,Allow
    Deny from ...
    Allow from ...
</Directory>
注意顺序：
除了后面allow的，其他都deny

典型的封IP
Order Allow,Deny
Allow from all
Deny from 205.252.46.165
注意顺序：
除了后面deny的，其他都allow

Deny from 192.168.2       典型的封网段

小结：
    Allow和Deny可以用于apache的conf文件或者.htaccess文件中（配合
    Directory, Location, Files等），用来控制目录和文件的访问授权。
    所以，最常用的是：
    Order Deny,Allow
    Allow from All

    注意“Deny,Allow”中间只有一个逗号，也只能有一个逗号，有空格都会出
    错；单词的大小写不限。上面设定的含义是先设定“先检查禁止设定，没有
    禁止的全部允许”，而第二句没有Deny，也就是没有禁止访问的设定，直
    接就是允许所有访问了。这个主要是用来确保或者覆盖上级目录的设置，开
    放所有内容的访问权。

    按照上面的解释，下面的设定是无条件禁止访问：
    Order Allow,Deny
    Deny from All

    如果要禁止部分内容的访问，其他的全部开放：
    Order Deny,Allow
    Deny from ip1 ip2
    或者
    Order Allow,Deny
    Allow from all
    Deny from ip1 ip2


下面是测试过的例子：

--------------------------------
Order deny,allow
allow from all
deny from 219.204.253.8
全部都可以通行

-------------------------------
Order deny,allow
deny from 219.204.253.8
allow from all
全部都可以通行

-------------------------------
Order allow,deny
deny from 219.204.253.8
allow from all
只有219.204.253.8不能通行

-------------------------------
Order allow,deny
allow from all
deny from 219.204.253.8
只有219.204.253.8不能通行

-------------------------------

-------------------------------
Order allow,deny
deny from all
allow from 219.204.253.8
全部都不能通行

-------------------------------
Order allow,deny
allow from 219.204.253.8
deny from all
全部都不能通行

-------------------------------
Order deny,allow
allow from 219.204.253.8
deny from all
只允许219.204.253.8通行

-------------------------------
Order deny,allow
deny from all
allow from 219.204.253.8
只允许219.204.253.8通行

-------------------------------

--------------------------------
Order deny,allow
全部都可以通行（默认的）

-------------------------------
Order allow,deny
全部都不能通行（默认的）

-------------------------------
Order allow,deny
deny from all
全部都不能通行

-------------------------------
Order deny,allow
deny from all
全部都不能通行

-------------------------------

对于上面两种情况，如果换成allow from all，则全部都可以通行！

-------------------------------
Order deny,allow
deny from 219.204.253.8
只有219.204.253.8不能通行

-------------------------------
Order allow,deny
deny from 219.204.253.8
全部都不能通行

-------------------------------
Order allow,deny
allow from 219.204.253.8
只允许219.204.253.8通行

-------------------------------
Order deny,allow
allow from 219.204.253.8
全部都可以通行

-------------------------------

-------------------------------
order deny,allow
allow from 218.20.253.2
deny from 218.20
代表拒绝218.20开头的IP，但允许218.20.253.2通过；而其它非218.20开头的IP也都允许通过。

-------------------------------


4、配置实例：
Listen 80
ServerName localhost:80
<Directory />
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    allow from all
</Directory>
DocumentRoot "E:/phpspace"
<Directory "E:/phpspace">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Order deny,allow
    allow from all
</Directory>


附：在apach文件中配置监听不同端口的虚拟主机
listen 81
NameVirtualHost *:81
<VirtualHost *:81>
ServerName localhost:81
DocumentRoot "E:/phpspace/nvshenlaile"
</VirtualHost>


