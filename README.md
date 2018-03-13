### Password-cli
___
[![Build Status](https://travis-ci.org/Dowte/password.svg?branch=master)](https://travis-ci.org/Dowte/password)
[![Latest Stable Version](https://poser.pugx.org/Dowte/password/v/stable.svg)](https://packagist.org/packages/Dowte/password)
[![Total Downloads](https://poser.pugx.org/Dowte/password/downloads.svg)](https://packagist.org/packages/Dowte/password) 
[![Latest Unstable Version](https://poser.pugx.org/Dowte/password/v/unstable.svg)](https://packagist.org/packages/Dowte/password) 
[![License](https://poser.pugx.org/Dowte/password/license.svg)](https://packagist.org/packages/Dowte/password)

#### A command-line tool to help you manage your password

#### Show in console

![show](http://assest.dowte.com/imgs/pass-cli/console-q2.gif)

#### Show in browser

![show](http://assest.dowte.com/imgs/pass-cli/browser-q.gif)

### 1、Download and configure

+ git clone https://github.com/Dowte/password.git && password/install.sh 
    + install.sh arguments
        + --way=sqlite (default yamlFile)
        + --default=false (default true)
        + --completion=false (default true)
        + --alfred=false (default false)
        + --uname=dowte (default timestamp)

### 2、Save a password item

```php
pass password -g
//-g auto generate a new password(option)
```

![password](http://assest.dowte.com/imgs/pass-cli/password-g-new.jpg)

### 3、find

```php
pass find -a  | pass -a //Show password list
pass find dowte  | pass dowte //Get a password which keyword is dowte
```
![find](http://assest.dowte.com/imgs/pass-cli/find-list-new.jpg)
![find-N](http://assest.dowte.com/imgs/pass-cli/find-N-new.jpg)

### 4、ext

#### 4.1 Use alfred

+ Import Pass.alfredworkflow to the alfred(Double click on the Pass.alfredworkflow)

```
pass alfred init
```
![alfred-init](http://assest.dowte.com/imgs/pass-cli/alfred-init-new.jpg)

#### 6.2 list on alfred

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-new.jpeg)

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

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-c-new.jpg)

##### 6.3.1 generate: generate random passwords

![alfred-list](http://assest.dowte.com/imgs/pass-cli/alfred-k-generate-new.jpg)

cmd+enter copy password to clipboard

##### more documents ses [docs](./doc)
