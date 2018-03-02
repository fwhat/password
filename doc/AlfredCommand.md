# AlfredCommand

## 主要功能

- 初始化 application-alfred
- 为pass-alfred-workflow 提供快捷搜索

### Help

```
Usage:
  alfred [options]

Options:
      --init               Init the pass-alfred
  -k, --keyword[=KEYWORD]  Query password by keywords
```
#### 参数详解

```
--init 初始化pass-alfred
-k  1: 按关键词搜索密码项 
	2: 如提供 -c 则将展示可以执行的其他命令 
	3: 执行其他可执行的命令
```

#### 用法
+ 使用command键+enter 复制结果至剪贴板

##### --init

![alfred-init](http://assest.dowte.com/imgs/pass-cli/alfred-init.jpg)

##### -k 
![alfred](http://assest.dowte.com/imgs/pass-cli/alfred.jpg)

###### -c 

![alfred-k-c](http://assest.dowte.com/imgs/pass-cli/alfred-k-c.jpg)

###### generate

![alfred-k-generate](http://assest.dowte.com/imgs/pass-cli/alfred-k-generate.jpg)
