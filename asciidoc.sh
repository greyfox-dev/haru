#!/bin/bash
DIRNAME=`pwd`
asciidoc -a encoding="UTF8" --theme=flask -a scriptsdir="$DIRNAME/docs/scripts" -v -a toc -a numbered -o index.html docs/index.txt