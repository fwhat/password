# PasswordCommand

## 主要功能

- 创建一个新的密码项

### Help

```
Usage:
  password [options]
  p 

Options:
  -N, --name[=NAME]                Set a name for new password
  -d, --description[=DESCRIPTION]  Set a description for new password
  -D, --no-description             Don't set description for new password
  -g, --generate                   Generate a random string for new password(level 3 length 12)
  -H, --no-hidden                  Whether or not to hidden the generate result.
  -l, --length[=LENGTH]            How length random string you want generate.(max 100) [default: 12]
  -L, --level[=LEVEL]              Which random string level to generate [default: 3]
```
#### 参数详解

```
-N 为新密码项设置一个名称
-d 为新密码项设置一个描述
-D 不设置描述
-g 使用生成器自动生成新的密码(默认12位随机数字大小写字母组合)
-H 不隐藏创建出的字符串, 默认只复制入剪贴板 
-l 生成字符串的长度, 默认12位
-L 字符串的‘等级’(默认3) 1: 数字; 2: 数字+小写字母; 3: 数字+大小写字母; 4: 数字+大小写字母+特殊字符
```

##### create

![create](http://assest.dowte.com/imgs/pass-cli/password-g-D.jpg)
