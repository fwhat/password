#!/usr/bin/env bash
#set init way default 0
function validArg()
{
    if [ ${1:0:6} == "--way=" ] ;then
        way=${1:6:${#1}}
        return
    fi
    if [ ${1:0:13} == "--completion=" ] ;then
            if [ ${1:13:${#1}} == 'false' ]; then
                completion=0
            fi
        return
    fi
    if [ ${1:0:10} == "--default=" ] ;then
            if [ ${1:10:${#1}} == 'false' ]; then
                default=0
            fi
        return
    fi
    if [ ${1:0:9} == "--alfred=" ] ;then
            if [ ${1:9:${#1}} == 'true' ]; then
                alfred=1
            fi
        return
    fi
    if [ ${1:0:8} == "--uname=" ] ;then
        uname=${1:8:${#1}}
        return
    fi
}
#defalut pass init db way = 1 (yamlFile)
way=1
#defalut use init default arg
default=1
#defalut no alfred
alfred=0
#defalut use completion
completion=1
#set pass user name default timestamp
uname=$4:-`date +%s`
for i in $*
do
    validArg ${i}
done
if [ ${way} -gt 2 ] || [ ${way} -lt 0 ]; then
    way=0
fi
#get install.sh dir
installPath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd ${installPath}
composer install --no-dev
#completion
if [ ${completion} -eq 1 ]; then
    case ${SHELL} in
        "/bin/zsh") echo source ${installPath}"/pass-cli.bash" >> ~/.zshrc
                    `source ~/.zshrc`
        ;;
        "/bin/bash") echo source ${installPath}"/pass-cli.bash" >> ~/.bashrc
                    `source ~/.bashrc`
        ;;
    esac
fi
#pass init
if [ default -eq 1 ]; then
    pass init -w ${way} --default
    else
    pass init -w ${way}
fi
#pass create user
pass user -u uname
#set into env command
ln -s ${installPath}"/bin/pass" /usr/local/bin/pass
#pass alfred init
if [ alfred -eq 1 ]; then
    pass alfred --init
fi