#!/bin/sh
source #phing:paths.scripts#/setenv.sh

#phing:system.bin.python# #phing:libs.caterjs.deploy.dst#/run.py --dir="#phing:paths.public#/static/jslib" $@