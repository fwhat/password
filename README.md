### Password-cli
___
[![Build Status](https://travis-ci.org/Dowte/password.svg?branch=master)](https://travis-ci.org/Dowte/password)
[![Latest Stable Version](https://poser.pugx.org/Dowte/password/v/stable.svg)](https://packagist.org/packages/Dowte/password)
[![Total Downloads](https://poser.pugx.org/Dowte/password/downloads.svg)](https://packagist.org/packages/Dowte/password) 
[![Latest Unstable Version](https://poser.pugx.org/Dowte/password/v/unstable.svg)](https://packagist.org/packages/Dowte/password) 
[![License](https://poser.pugx.org/Dowte/password/license.svg)](https://packagist.org/packages/Dowte/password)
#### A command-line tool to help you manage your password

### 一、安装及配置

##### 1.1 下载

+ git clone https://github.com/Dowte/password.git

#### 1.2 配置
+ cp pass-cli/bin/pass /usr/local/bin/pass


### 二、初始化

```php
pass init
```

![init](http://assest.dowte.com/imgs/pass-cli/init.jpg)

#### 配置命令行提示工具
echo "source {{pass-cli-path}}pass-cli.bash" >> ~/.zshrc ({{pass-cli-path}}用真实路径替换)
source ~/.zshrc

### 三、创建一个用户

```php
pass user -u dowte
//将会创建一个用户名为dowte密码库, 会提示设置主密码(此密码将被加密, 查询密码库时必须提供)
```
![user](http://assest.dowte.com/imgs/pass-cli/user-u.jpg)

### 四、存入密码

```php
pass password -g
//-g 生成新的密码(如保存之前的密码，则无需提供该参数)
```

![password](http://assest.dowte.com/imgs/pass-cli/password-g.jpg)

### 五、查找

```php
pass find -a  | pass -a //列出所有存入的密码的名称
pass find dowte  | pass dowte //dowte的密码
```
![find](http://assest.dowte.com/imgs/pass-cli/find-a.jpg)
![find-N](http://assest.dowte.com/imgs/pass-cli/find-N.jpg)

### 六、ext

#### 6.1 使用 alfred

双击使用Pass.alfredworkflow

```
pass alfred init
```
![alfred-init](http://assest.dowte.com/imgs/pass-cli/alfred-init.jpg)

#### 6.2 列表

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred.jpeg)

+ enter|cmd+enter 将选中密码复制到剪贴板
+ cmd+4 将第4项密码复制到剪贴板
+ alt+enter 将描述复制到剪贴板

#### 6.3 其他命令
```
//在alfred中
pass -c 
//显示可执行的命令
tab 键选择一个
```

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-c.jpg)

##### 6.3.1 generate 随机生成密码串

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-generate.jpg)

cmd+enter 将密码复制到剪贴板
