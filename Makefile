.PHONY: help build tag run-local fresh-run clean-run

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)


.DEFAULT_GOAL := help
APP_NAME=faya-core
DOCKER_REPO=032415894776.dkr.ecr.us-east-1.amazonaws.com
AWS_CLI_REGION=us-east-1

COMMIT_HASH=$(shell git rev-parse --short HEAD)

# build ALWAYS builds off the master branch, unless the env is dev
# if you want to build anything that is not on master, the dev env variable needs to be set
# what this means though is the service should and will be updated in dev only, (if this makefile is used)
build: ## Build the containers
	@echo "building $(APP_NAME)"
	docker build -t $(APP_NAME) -f app.dockerfile .

tag: ## Tag the containers with the relevant commit hash and add the remote tag
	@if [ -z $(env) ]; then echo "env variable is not set" && exit 1; else echo "env=$(env)"; fi

	@echo "tagging with $(COMMIT_HASH) and $(env)"

	$(eval local_image := $(APP_NAME):latest)
	$(eval remote_image := $(DOCKER_REPO)/$(APP_NAME))

	docker tag $(local_image) $(remote_image):$(COMMIT_HASH)
	docker tag $(local_image) $(remote_image):$(env)

# HELPERS

# generate script to login to aws docker repo
CMD_REPOLOGIN := "eval $$\( aws ecr"
ifdef AWS_CLI_PROFILE
CMD_REPOLOGIN += " --profile $(AWS_CLI_PROFILE)"
endif
ifdef AWS_CLI_REGION
CMD_REPOLOGIN += " --region $(AWS_CLI_REGION)"
endif
CMD_REPOLOGIN += " get-login --no-include-email \)"

repo-login: ## Auto login to AWS-ECR unsing aws-cli
	@eval $(CMD_REPOLOGIN)

push-image: build tag repo-login ## Push the tagged image to ecr
	@if [ -z $(env) ]; then echo "env variable is not set" && exit 1; else echo "env=$(env)"; fi

	$(eval hash_remote_image := $(DOCKER_REPO)/$(APP_NAME):$(COMMIT_HASH))
	$(eval env_remote_image := $(DOCKER_REPO)/$(APP_NAME):$(env))

	@echo "pushing $(hash_remote_image) and $(env_remote_image)"

	docker push $(hash_remote_image)
	docker push $(env_remote_image)

run-local:
	docker-compose up --build -d
	docker-compose exec app composer install
	# docker-compose exec app php artisan migrate

clean-run:
	sudo rm -rf ./dev/mysql

fresh-run: clean-run run-local
### Need to do more work for this to update

# update-service: push-image ## Update the service after pushing
# 	@if [ -z $(env) ]; then echo "env variable is not set" && exit 1; else echo "env=$(env)"; fi
