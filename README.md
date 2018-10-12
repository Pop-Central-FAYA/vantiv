# FAYA APP
Faya is a software platform ,that enables brands and agencies
to plan, buy and optimize their advertising.

## Setup of Dev Environment

The environment is setup using docker, so everybody can just get started immediately without jumping through hoops to setup environments. Plus we use the exact same template for staging and production, so remove issues with *this works on my machine*

The tools you need installed in your environment are the latest version of `docker` and `docker-compose`

Note, the instructions assume you are working from a unix/linux based system.

To get the docker environment to run locally, the following steps need to be taken.

1. rename the `env.example` file to `.env`
2. change permissions on certain folders in the root directory. (They need to be make writable by the world). We probably need to fix this, but it works for now. The folders and commands needed are below.
    * `sudo chmod -R 777 ./storage`
    * `sudo chmod -R 777 ./bootstrap/cache`
3. while in the root directory, run the following command, which will start all the services and set them to the background:
    * `docker-compose up --build -d`
4. go into the running app container and run `composer install`. You only need to run these whenever composer.json changes, or you delete the vendor directory. As a matter of principle, I try to run this everytime I do a pull and start the app. To go into the container and user composer do the following:
    * `docker-compose exec app bash`
    * `composer install`
5. the webserver and mysql database can be accessed at the following endpoints and ports:
    * webserver: __localhost:9000__
    * database:
        * location: __localhost:33061__
        * username: __faya__
        * password: __faya__
    note, that these are default values and are actually set as environment variables that are picked up by both the app and the database. See `docker-compose.yaml` for more information about all of these.
6. Enjoy testing.

As you make changes, the changes are reflected, while testing.

If you want to go into a running container, whether te webserver or the database, the following commands using docker-compose will help.

```bash
docker-compose exec app bash # this is for the php app
docker-compose exec database bash # this is for the database
```

There are many reasons you might want to go into the containers, i.e run some commands, check logs etc

### Updating seed database

The seed database is in the following location:

`./database/docker-entrypoint-initdb.d/02-full-faya.sql.gz`

If db schema changes, this will need to be updated for new users, so do a dump of your current __dev__ db., the following command should work.

```bash
mysqldump --host=localhost --port=33061 -ufaya -pfaya --databases --add-drop-database --events --routines --triggers faya api_db | gzip > ./database/docker-entrypoint-initdb.d/02-full-faya.sql.gz
```
Check that into version control and we are good to go

## Accessing AWS Environment

The aws environment is somewhat locked down. The only way in the environment is through a jumpbox, and from the jumpbox you can ssh into other instances.

The following commands help you get into the dev workbox inside the aws environment.

A custom `~/.bash_profile` or `~/.zshrc` profile will make life much easier, the following config will help, with some modifications if needed. Add the following code to your custom profile file

```bash
export $AWS_USERNAME=your.user #change 'your.user' to your aws username
export $DEV_JUMPBOX_IP=jumpbox.ip #change 'jumpbox.ip' to be the ip address or dns of the jumpbox

#The FAYA_ID_RSA should be whatever the location of your faya ssh key is
export $FAYA_ID_RSA=$HOME/.ssh/faya
export $FAYA_DEVBOX_URL=dev-workbox.fayamedia.int
export $FAYA_APP_PORT=8080
export $FAYA_DB_PORT=33061

#Note the `dev-faya` in the ssh, we will add that to the ssh config later
alias dev-faya-tunnel=ssh -f -N dev-faya -L $FAYA_APP_PORT:$FAYA_DEVBOX_URL:9000 -L $FAYA_DB_PORT:$FAYA_DEVBOX_URL:33061

function close_tunnel(){
    tunnel_pid=$(ps aux | grep "faya" | grep "ssh -f -N" | awk '{print $2}')
    if [ -z "$tunnel_pid" ]; then
        echo "cannot find open connection"
    else
        echo "closing faya tunnels"
        kill $tunnel_pid
    fi
}
```

Put the following inside your `~/.ssh/config`

```conf

#NOTE ssh configs do not use environment variables, replace the environment variables
#with their values.
Host dev-faya
    HostName $DEV_JUMPBOX_IP
    User $AWS_USERNAME
    ForwardAgent yes
    IdentityFile $FAYA_ID_RSA
    AddKeysToAgent yes
    StrictHostKeyChecking ask
    VisualHostKey yes
    LogLevel INFO
```

Now, to setup a tunnel to the devbox (so you can access services you are running remotely), do `dev-faya-tunnel`, and now through the localhost:8080 or localhost:33061, you can access the app and the db (if you are running it remotely)

To go to the bastion, just run `ssh dev-faya`. This should get you to the bastion, if you have your user setup properly, and from the dev bastion, you can go to various dev environments, most especially the dev box.

In the dev bastion, you can modify the `~/.ssh/config` to have the following.

```conf
#NOTE ssh configs do not use environment variables, replace the environment variables
#with their values.
Host dev-workbox
    HostName dev-workbox.fayamedia.int
    User $USER
    ForwardAgent yes
```

then to get to the workbox, just do `ssh dev-workbox`


