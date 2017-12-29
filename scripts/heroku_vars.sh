#!/bin/bash

ENV=server/lumen/.env.prod

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

egrep '^[^#][^=]*=[^[:blank:]]+' "$ENV" | while read line; do
	heroku config:set "$line"
done
