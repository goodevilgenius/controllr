heroku-deploy:
	git subtree push --prefix server/lumen heroku master

heroku-config:
	bash scripts/heroku_vars.sh
