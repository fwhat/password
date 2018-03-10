# PasswordCommand

## 主要功能

- 创建一个新的密码项

### Help

```
Usage:
  password [options]
  p (短命令)

Options:
  -k, --keyword[=KEYWORD]  The keyword for password
  -e, --exec[=EXEC]        Choose update(u)|delete(d)|create(c), default create(c) [default: "c"]
  -D, --no-description     Don't set description for new password
  -g, --generate           Generate a random string for new password(level 3 length 12)
  -l, --length[=LENGTH]    How length random string you want generate.(max 100) [default: 12]
  -L, --level[=LEVEL]      Which random string level to generate [default: 3]
```
#### 参数详解

```
-k 密码的关键词
-e 执行创建|更新|删除 默认创建
-D 不设置描述
-g 使用生成器自动生成新的密码(默认12位随机数字大小写字母组合)
-l 生成字符串的长度, 默认12位
-L 字符串的‘等级’(默认3) 1: 数字; 2: 数字+小写字母; 3: 数字+大小写字母; 4: 数字+大小写字母+特殊字符
```

##### 其他
+ generate 的默认值可由.pass-conf.yaml 中配置

##### password

![password-create](http://assest.dowte.com/imgs/pass-cli/password-g.jpg)
![password-update](http://assest.dowte.com/imgs/pass-cli/password-update-g.jpg)
