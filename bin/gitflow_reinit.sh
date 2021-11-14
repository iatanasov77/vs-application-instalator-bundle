#!/bin/bash

#Those commands remove all the sections of the git config file related to gitflow.
/usr/bin/git config --remove-section "gitflow.prefix"
/usr/bin/git config --remove-section "gitflow.branch"

#Then you can re-init gitflow as usual.
/usr/bin/git flow init
