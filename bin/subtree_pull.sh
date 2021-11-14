#!/bin/bash

/usr/bin/git subtree pull --prefix=src/Vankosoft/ApplicationInstalatorBundle ApplicationInstalatorBundle 1.4 --squash
/usr/bin/git subtree pull --prefix=src/Vankosoft/ApplicationBundle ApplicationBundle 1.4 --squash
/usr/bin/git subtree pull --prefix=src/Vankosoft/CmsBundle CmsBundle 1.4 --squash
/usr/bin/git subtree pull --prefix=src/Vankosoft/UsersBundle UsersBundle 1.4 --squash

