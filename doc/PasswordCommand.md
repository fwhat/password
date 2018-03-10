# PasswordCommand

## Main

- Add a new password item

### Help

```
Usage:
  password [options]
  p (sort command)

Options:
  -k, --keyword[=KEYWORD]  The keyword for password
  -e, --exec[=EXEC]        Choose update(u)|delete(d)|create(c), default create(c) [default: "c"]
  -D, --no-description     Don't set description for new password
  -g, --generate           Generate a random string for new password(level 3 length 12)
  -l, --length[=LENGTH]    How length random string you want generate.(max 100) [default: 12]
  -L, --level[=LEVEL]      Which random string level to generate [default: 3]
```
#### Params info

```
-k The keyword for password
-e Choose update(u)|delete(d)|create(c), default create(c) [default: "c"]
-D No set description ask
-g Generate a random string for new password(level 3 length 12)
-l The length of random string
-L The level of random string(default 3) 
    1: only numbers; 
    2: numbers and lower characters
    3: numbers, lower characters and upper characters
    4: numbers, lower characters, upper characters and special characters
```

##### Other
+ generate: The default value of generate could be configured in .pass-conf.yaml.

##### password

![password-create](http://assest.dowte.com/imgs/pass-cli/password-g.jpg)
![password-update](http://assest.dowte.com/imgs/pass-cli/password-update-g.jpg)
