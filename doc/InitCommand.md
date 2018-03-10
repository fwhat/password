# InitCommand

## Main

- Init Application

### Help

```
Usage:
  init [options]

Options:
  -w, --way[=WAY]       Which way for save password records.
  -d, --no-db           No DB config ask.
```
#### Params info

```
-w Choose a way to save password records
	0:sqlite 
	1:yamlFile (without any extension, use yaml file to save)
	2:mysql
-d  no DB config ask (But the DB conf must already in .pass-conf.yaml)
```

#### Other
+ use the auto-completion with pass-cli.bash
  + 1. add source {{application-path}}pass-cli.bash to end of ~/.zshrc
  + 2. execute source ~/.zshrc

![init](http://assest.dowte.com/imgs/pass-cli/init.jpg)





