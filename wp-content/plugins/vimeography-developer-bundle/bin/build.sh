#!/bin/bash
# This script takes about 8-9 minutes to run.
cd vimeography-themes/

for themeDir in */ ; do
  cd "$themeDir" && npm run build
  git add dist
  cd ../
done

git commit -m "[build.sh] Rebuild all themes"
