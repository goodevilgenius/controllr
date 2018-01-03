#!/bin/bash

ENV=server/lumen/.env.prod

VARS=( )

if [ ! -f "$ENV" ]; then
	echo "$ENV not found" >&2
	ENV=server/lumen/.env
fi

if [ ! -f "$ENV" ]; then
	echo "$ENV not found" >&2
	ENV=server/lumen/.env.example
	if [ -f "$ENV" ]; then
		echo "Using $ENV. These may not be the values you intend" >&2
	fi
fi

echo "Reading from $ENV"

while read line; do
	export VARS=( "${VARS[@]}" "$line" )
done < <(egrep '^[^#][^=]*=[^[:blank:]]+' "$ENV")

heroku config:set "${VARS[@]}"
