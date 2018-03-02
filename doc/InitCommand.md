# InitCommand

## 主要功能

- 初始化Application

### Help

```
Usage:
  init [options]

Options:
  -w, --way[=WAY]        Which way for save password records.
  -G, --generate-secret  Generate new openssl secret keys.
```
#### 参数详解

```
-w 选择一个数据存储方式 0:sqlite (不提供将以询问方式选择)
-G 是否生成新的加密对 (不提供则需自行配置pass-conf.php 秘钥对选项)
```

#### 其他
+ 改指令将生成命令行补全工具 pass-cli.bash
  + 1. 在~/.zshrc 文件最后添加 source {{application-path}}pass-cli.bash
  + 2. 执行source ~/.zshrc

![init](http://assest.dowte.com/imgs/pass-cli/init-G.jpg)





