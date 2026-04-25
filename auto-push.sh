#!/bin/bash
cd /www/wwwroot/snowmanblog
if [ -n "$(git status --porcelain)" ]; then
    git add -A
    git commit -m "auto update: $(date '+%Y-%m-%d %H:%M:%S')"
    git push origin dev
fi
