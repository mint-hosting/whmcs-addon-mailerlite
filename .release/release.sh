#!/bin/sh

cd .release && cp .releaserc.json .releaserc

if [ "$MODE" = "predict" ]; then
  echo 'Running semantic release in dry mode...'

  (semantic-release -d || exit 1) | grep 'Published release' | sed -E 's/.*([0-9]+.[0-9]+.[0-9]+)/\1/' > .release-version
else
  semantic-release  || exit 1
fi

cd .release && rm .releaserc

FILE=.release-version

if [ -f "$FILE" ]; then
  VERSION_NUMBER=$(cat $FILE)

  if [ -n "$VERSION_NUMBER" ]; then
    if [ -n "$PLUGIN_VERSION_FILE" ]; then mv .release-version $PLUGIN_VERSION_FILE; fi

    echo "Successfully released $VERSION_NUMBER"
  else
    echo "There is no new version found (file is empty), skipping release..."
    exit 1
  fi
else
  echo "There is no new version found (no release file generated), skipping release..."
  exit 1
fi
