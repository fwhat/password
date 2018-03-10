# GenerateCommand

## Main

- generate a random password string

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
#### Params info

```
-H Hidden the generate result
-l The length of new string
-L The level of new string(default 3) 
    1: only numbers; 
    2: numbers and lower characters
    3: numbers, lower characters and upper characters
    4: numbers, lower characters, upper characters and special characters
```

##### generate

![generate](http://assest.dowte.com/imgs/pass-cli/generate.jpg)
