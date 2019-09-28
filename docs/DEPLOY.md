# DEPLOYING FAYA
Instructions for deploying FAYA in our environment.

Faya is hosted in AWS ECS. Each environment has its own cluster and service in ECS, for instance, dev will have its own service called `dev-faya-core` in an ECS cluster, and prod will be `prod-faya-core` etc.

Migration of db changes is pretty interesting, for now to migrate db changes, after deploying the service, you will need to ssh into the ecs instance and go into the docker container to run the migration.

Below are instructions for deploying into dev, this should be almost identical to deploying for production.

If you want to understand how this deployment works, take a look at the `Makefile` and see if it confers understanding.

## Steps

1. make sure your access into the environment is setup, since deployment happens from within the environment. If this is not setup, make sure to follow the instructions [here](/README.md)
2. ssh into the bastion `ssh dev-faya` from your local machine's terminal
3. ssh into the dev box, `ssh dev-workbox` from the bastion terminal
4. make sure you have a local copy of the code, this can be cloned anywhere from gitlab.
5. go into the code's directory and make sure to be on the branch you want to deploy. For all other environments apart from dev, this __must__ be the master branch.
6. run the following command from the code's root directory. `env=dev make push-image`. What this will do is build the docker image and push to ECR, to the `faya-core` repository
7. update the service in ecs. You can go to the service, and select update, then select the `force new deployment` checkbox and click through to the end.
8. after the deployment is concluded, ssh into the ecs instance. look for an instance in ec2 console that has `dev-faya-core` as the name, get the ip and ssh from it. For example, assuming the ip of the ecs instance is `192.198.2.8`, the following.
    * ssh dev-faya
    * ssh 192.198.2.8
9. after getting into the ecs instance, do the following to get the container id to enter. `docker ps`
10. get the appropriate docker container and do the following. `docker exec -it xxx bash` where xxx is the container id
11. run the migration. `php artisan migrate --database=api_db`

