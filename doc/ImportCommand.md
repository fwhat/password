# ImportCommand

## 主要功能

- 导入密码库

### Help

```
Usage:
  import [options] [--] <file>

Arguments:
  file                  A file use to import

Options:
      --overwrite       Overwrite password item if exists!
```
#### 参数详解

```
file 需要导入的文件
--overwrite 如果keyword已存在则覆盖(默认跳过)
```

#### 其他

+ 用yaml文件，并以对应的格式 [模版](../import.yaml.template)
+ 有需要添加|修改多个时，导入是很有效的

##### import

![create](http://assest.dowte.com/imgs/pass-cli/import.jpg)
