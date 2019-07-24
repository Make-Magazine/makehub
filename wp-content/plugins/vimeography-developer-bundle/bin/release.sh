#!/bin/bash
versionRegex="Version: (.+)"

while read line; do
  if [[ $line =~ $versionRegex ]]
    then
      version="${BASH_REMATCH[1]}"
      echo "Version of Developer Bundle is ${version}"
  fi
done <"vimeography-developer-bundle.php"

git-archive-all --prefix vimeography-developer-bundle/ ~/dropbox/vimeography/ZIPs/2.0/vimeography-developer-bundle-$version.zip
cd vimeography-themes/

for themeDir in */ ; do
  noTrailingSlash=${themeDir::${#themeDir}-1}

  cd "$themeDir"

  while read line; do
    if [[ $line =~ $versionRegex ]]
      then
        version="${BASH_REMATCH[1]}"
        echo "Version of ${noTrailingSlash} is ${version}"
    fi
  done <"$noTrailingSlash.php"

  git archive --format zip --output ~/dropbox/vimeography/ZIPs/2.0/$noTrailingSlash-$version.zip --prefix $themeDir master;
  cd ../
done
