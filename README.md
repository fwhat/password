### Password-cli
___
[![Build Status](https://travis-ci.org/Dowte/password.svg?branch=master)](https://travis-ci.org/Dowte/password)
[![Latest Stable Version](https://poser.pugx.org/Dowte/password/v/stable.svg)](https://packagist.org/packages/Dowte/password)
[![Total Downloads](https://poser.pugx.org/Dowte/password/downloads.svg)](https://packagist.org/packages/Dowte/password) 
[![Latest Unstable Version](https://poser.pugx.org/Dowte/password/v/unstable.svg)](https://packagist.org/packages/Dowte/password) 
[![License](https://poser.pugx.org/Dowte/password/license.svg)](https://packagist.org/packages/Dowte/password)
#### A command-line tool to help you manage your password

#### Show in console

![show](https://github.com/Dowte/imgs/blob/master/pass-cli/console.gif?raw=true)

#### Show in browser

![show](https://github.com/Dowte/imgs/blob/master/pass-cli/browser.gif?raw=true)

### 1、Download and configure

##### 1.1 download

+ git clone https://github.com/Dowte/password.git

#### 1.2 configure
+ cp pass-cli/bin/pass /usr/local/bin/pass


### 2、Init

```php
pass init
```

![init](http://assest.dowte.com/imgs/pass-cli/init.jpg)

#### configure the completion
```php
echo "source {{pass-cli-path}}pass-cli.bash" >> ~/.zshrc 
//{{pass-cli-path}} the real path
source ~/.zshrc
```

### 3、Create a user

```php
pass user -u dowte
//This command will create a password library of dowte.
//And ask your to set the master password(The master password is required)
```
![user](http://assest.dowte.com/imgs/pass-cli/user-u.jpg)

### 4、Save a password item

```php
pass password -g
//-g auto generate a new password(option)
```

![password](http://assest.dowte.com/imgs/pass-cli/password-g.jpg)

### 5、find

```php
pass find -a  | pass -a //Show password list
pass find dowte  | pass dowte //Get a password which keyword is dowte
```
![find](http://assest.dowte.com/imgs/pass-cli/find-list.jpg)
![find-N](http://assest.dowte.com/imgs/pass-cli/find-N.jpg)

### 6、ext

#### 6.1 Use alfred

+ Import Pass.alfredworkflow to the alfred(Double click on the Pass.alfredworkflow)

```
pass alfred init
```
![alfred-init](http://assest.dowte.com/imgs/pass-cli/alfred-init.jpg)

#### 6.2 list on alfred

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred.jpeg)

+ enter|cmd+enter copy password to clipboard
+ cmd+4 copy the 4th password to clipboard
+ alt+enter copy description to clipboard

#### 6.3 call other command
```
//on alfred window
pass -c 
//show command list which can be execute
use tab choose one 
```

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-c.jpg)

##### 6.3.1 generate: generate random passwords

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-generate.jpg)

cmd+enter copy password to clipboard

##### more documents ses [docs](./doc)
