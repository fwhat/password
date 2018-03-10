# InitCommand

## 主要功能

- 初始化Application

### Help

```
Usage:
  init [options]

Options:
  -w, --way[=WAY]       Which way for save password records.
  -d, --no-db           No DB config ask.
```
#### 参数详解

```
-w 选择一个数据存储方式(不提供将以询问方式选择)
	0:sqlite 
	1:yamlFile (无需其他扩展, 以yaml格式的文件存储)
	2:mysql
-d 不进行db配置的询问(必须已经配置好.pass-conf.yaml)
```

#### 其他
+ 改指令将生成命令行补全工具 pass-cli.bash
  + 1. 在~/.zshrc 文件最后添加 source {{application-path}}pass-cli.bash
  + 2. 执行source ~/.zshrc

![init](http://assest.dowte.com/imgs/pass-cli/init.jpg)





