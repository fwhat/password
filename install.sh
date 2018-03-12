#!/usr/bin/env bash
#set init way default 0
way=${1:-0}
alfred=${2:-'true'}
completion=${3:-'true'}
#set pass user name default timestamp
uname=$4:-`date +%s`
if [ ${way}>2 ] || [ ${way}<0 ]; then
    way=0
fi
#get install.sh dir
installPath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
#set into env command
cp ${installPath}"/bin/pass" /usr/local/bin
#completion
if [ ${completion}=='true' ]; then
    case ${SHELL} in
        "/bin/zsh") echo source ${installPath}"/pass-cli.bash" >> ~/.zshrc
                    source ~/.zshrc
        ;;
        "/bin/bash") echo source ${installPath}"/pass-cli.bash" >> ~/.bashrc
                    source ~/.bashrc
        ;;
    esac
fi
#pass init
pass init -w ${way}
#pass create user
pass user -u uname
#pass alfred init
if alfred=='true'; then
    pass alfred --init
fi