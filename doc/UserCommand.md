# UserCommand

## 主要功能

- 创建一个使用者

### Help

```
Usage:
  user [options]
  u

Options:
  -u, --username[=USERNAME]  New username for password
  -f, --fix                  Fix user-conf if miss the user-config
```
#### 参数详解

```
-u 新创建的用户名 (不提供将以询问方式输入)
-f *创建的用户名将加密后存本地，用于后续查询，如丢失文件，可提供原用户名重新生成
```

##### create

![create](http://assest.dowte.com/imgs/pass-cli/user-u.jpg)
