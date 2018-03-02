# GenerateCommand

## 主要功能

- 创建一个随机字符串

### Help

```
Usage:
  generate [options]
  g

Options:
  -H, --no-hidden        Whether or not to hidden the generate result.
  -l, --length[=LENGTH]  How length string you want generate(max 100) [default: 12]
  -L, --level[=LEVEL]    Which random string level to generate [default: 3]
```
#### 参数详解

```
-H 不隐藏创建出的字符串, 默认只复制入剪贴板 
-l 生成字符串的长度, 默认12位
-L 字符串的‘等级’(默认3) 1: 数字; 2: 数字+小写字母; 3: 数字+大小写字母; 4: 数字+大小写字母+特殊字符
```

##### generate

![generate](http://assest.dowte.com/imgs/pass-cli/generate.jpg)
