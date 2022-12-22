#!/bin/bash
# This script takes about 4 minutes to run with 14 themes installed.

cd vimeography-themes/

for themeDir in */ ; do
  cd "$themeDir" && yarn upgrade vimeography-blueprint && git add yarn.lock
  cd ../
done

git commit -m "Update Vimeography Blueprint to latest version"
