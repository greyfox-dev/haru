#!/bin/sh
source #phing:paths.scripts#/setenv.sh

#phing:system.bin.php# #phing:libs.Miao.deploy.dst#/scripts/console.php --bootstrap=#phing:libs.${born-properties.lib_name}.deploy.dst#/scripts/bootstrap.php $@