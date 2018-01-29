#!/usr/bin/env bash
echo '{"text":"'`ifconfig |grep ether|awk '{print $2}'|xargs -n8|sed 's/ /,/g'|sed 's/:/-/g'`'"}'