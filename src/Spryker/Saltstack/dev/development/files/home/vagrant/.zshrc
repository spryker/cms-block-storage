# This file is maintained by Salt!

ZSH=$HOME/.oh-my-zsh
ZSH_THEME="robbyrussell"
plugins=(git composer spryker)
source $ZSH/oh-my-zsh.sh
export PS1='%n@%m ${ret_status}%{$fg_bold[green]%}%p %{$fg[cyan]%}%c %{$fg_bold[blue]%}$(git_prompt_info)%{$fg_bold[blue]%} % %{$reset_color%}'