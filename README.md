# Hands-on Docker

The following steps were taken from the official Self-paced [Docker Training](https://training.docker.com).

## Table of contents
1. [Introduction](#introduction)
2. [First steps](#firststeps)
3. [Containers](#containers)
4. [Networking](#networking)
5. [Dockerfile](#dockerfile)
6. [Docker Compose](#dockercompose)

## <a name='introduction'></a> Introduction

This hands-on guide was written to offer you a quick and simple way to learn Docker.
Please follow every step carefully and if you have any doubt please call for mentor’s help.

First things first! You will first install Docker on the Ubuntu Server VM provided.

### <a name="install-docker"></a> Install Docker

Locate the Putty application, connect to the Ubuntu VM and perform the following commands:

```sh
$ wget -qO- https://get.docker.com | sh
```

After installation, add your user to the docker group to avoid `sudo` on every command:
```
$ sudo usermod -aG docker user
```

Exit the current shell and login again:
```sh
$ exit
```

Login again, or open a new terminal.

## <a name='firststeps'></a> First Steps

From this step and beyond you’ll work with docker client. First let’s use the Hello World example.
```sh
$ docker run hello-world
```
Check Docker Client and Server version
```sh
$ docker version
```

### Docker Hub
Docker Hub is a cloud hosted service for building and shipping application or service containers. It provides a centralized resource for container image discovery, distribution and change management, user and team collaboration, and workflow automation throughout the development pipeline.

Open the web browser and access to [Docker Hub](https://hub.docker.com).

![Docker Hub Search Header](https://cld.pt/dl/download/00ec6459-02b9-49fd-af2a-a1b1150df808/DockerHubSearch.png)

#### Exercise
Locate the search bar (image above) and search for the tomcat repository. Try to get to know Docker Hub a little better.

### Local Docker Images
Images are kept on the docker daemon host machine. To list what docker images are present:
```sh
$ docker images
```

#### Exercise
Use the command `docker search` to find all the images related with tomcat.

## <a name="containers"></a> Containers
Let's create a container with the `ubuntu:14.04` image that will run the echo command:
```sh
$ docker run ubuntu:14.04 echo "hello world"
```
Now you can test some commands by yourself:
```sh
## list container processes
$ docker run ubuntu:14.04 ps ax
```
Note that the 2nd execution was faster, because you already had a local ubuntu 14.04 image.

### Flags
- Flag `-i` tells docker to connect to STDIN on the container
- Flag `-t` specifies to get a pseudo-terminal
- Flag `-d` tells docker to detache from the container (i.e. background)

Let's test `-i` and `-t` flags with a terminal process e.g. `bash`
```sh
$ docker run -i -t ubuntu:14.04 /bin/bash
```
You're now inside the container. Execute the following commands that will add a new user admin and install vim.
```sh
# Add a user
$ adduser admin
# Add admin user to sudo group
$ adduser admin sudo
# Change to user admin
$ su admin
# Install vim
$ sudo apt-get install vim
# Test vim (to quit vim :q!)
$ vim
# After testing, exit the container. Why do you have to run the exit command twice?
$ exit
$ exit
```

Re-run the last docker run command and test the following commands.
```sh
# Try to change to admin user
$ su admin
# User doesn't exist, why?
# Try to start vim
$ vim
# vim not installed, but why?
```
Now run a container with an `ubuntu:14.04` image with the initial process `/bin/bash`
```sh
$ docker run -it ubuntu:14.04 /bin/bash
  $ ps -ef
  # Note that your bash process is PID 1. Once this process is finished container will stop.
  # Hint: To quit without killing the container press CTRL+P+Q
$ docker ps
```
You'll see that the container is still running. `CTRL+P+Q` didn't kill the container, instead it just detached from the container.

Listing your active containers
```
$ docker ps
```

Use flag `-a` to list all containers (includes containers that are stopped)
```sh
$ docker ps -a
```
Take note on the `STATUS` column. See the difference? Stopped
containers have a message like `Exited () ...`.

Let's test detached `-d` flag, by executing a container that runs ping for 100 times.
```sh
$ docker run -d ubuntu:14.04 ping 127.0.0.1 -c 100
```
You'll get a ID, which is the container Long ID.
Check the container output with `docker logs` command.
You'll only need the first 6 digit from the container ID from the previous command output.
```sh
$ docker logs <ID>
```
You can simplify by giving a name the container. For that just use the `--name` flag.
```sh
$ docker run -d --name test-ping ubuntu:14:04 ping 127.0.0.1 -c 100
```
Follow the log if you use the `-f` flag (`CTRL+C` to exit)
```sh
$ docker logs -f test-ping
```

Go to https://docs.docker.com/engine/reference/run/ and learn more about `docker run`.

#### Exercise
Fiddle around with `docker run`, `docker stop`, `docker start`, `docker ps ` and `docker logs`.

## <a name="networking"></a> Networking
In this part we'll use the tomcat:7 image.

Run a tomcat container with `-P` flag (it will map all exposed ports on the containers to the host)
```sh
$ docker run -d --name tomcat -P tomcat:7
```
Check what port is mapped to container `8080` port with `docker ps`, take a look at the `PORTS` column.
```sh
$ docker ps
```
Open your browser and go to `http://VM_IP:PORT`
Stop the container
```sh
$ docker stop tomcat
```
Remove the container
```sh
$ docker rm tomcat
```
Let's clean up, by removing all stopped containers.
```sh
$ docker rm "$(docker ps -aq)"
```

### Linking containers
Re-execute the tomcat container.
```sh
$ docker run -d --name tomcat -P tomcat:7
```
Run a new container and try to ping tomcat container.
```sh
$ docker run -it ubuntu:14.04 /bin/bash
    $ ping tomcat
    # It fails! Exit the container.
    $ exit
```

The `--link` flag allows to connect two containers. Its syntax is `<name or id>:<alias>` .
```sh
$ docker run -it --link tomcat:tomcat ubuntu:14.04 /bin/bash
    $ ping tomcat
    # Success!!
```

## <a name="dockerfile"></a> Dockerfile
Dockerfile is a text document that contains instructions (commands) that will assemble an image. Using `docker build` an automated build is launched that will execute several command-line instructions in succession.

If you're on a Dockerfile folder, you just need to execute
```sh
$ docker build -t <name>:<tag> .
```

Using `-t` flag the default image tag is `latest` and `-f` flag default instruction file is `Dockerfile` (you can ommit `-f`). The `.` means that `docker build` will use the current folder as context folder (if you copy files it will refer to the context path). You can use `-t` flag multiple times to tag the image to different repositories.

The following table has the main `Dockerfile` instructions. A valid `Dockerfile` will have a `FROM` instruction as its first instruction (`MAINTAINER` instruction is not counted).

| Instruction |                   Description                    |
| ----------- | ------------------------------------------------ |
| FROM        | Sets the base image for subsequent instructions. |
| RUN         | Execute any commands in a new layer on top of current base image. |
| ADD         | Copies new files, directories or remote URLs and adds to the filesystem of the container. |
| COPY        | Copies new files or directories and adds to the filesystem of the container. |
| CMD         | There can only be one CMD instruction per Dockerfile. `CMD` provides defaults for executing container. |
| ENTRYPOINT  | Allows to configure a container that will run as an executable. |

The following example provides an `ubuntu:14.04` image that will create an admin user, add it to the `sudo` group and set it as the default user. It also adds the `src/binary` file to the root filesystem and sets it as the image initial execution command.

```docker
MAINTAINER Tiago Pires <tiago-a-pires@telecom.pt>
FROM ubuntu:14.04

RUN adduser --system --shell /bin/bash --group --disabled-password admin && \
    adduser admin sudo

USER admin

ADD src/binary /binary

CMD ["/binary"]
```

Go to https://docs.docker.com/engine/reference/builder/ and learn more about Dockerfile instructions.

### Exercise
Create a Dockerfile from a custom image (you decide which image) that will create an user and set it as default. Initial command should be `sh` or `bash` shell.

## <a name="dockercompose"></a> Docker Compose
Docker Compose is a YAML file that helps to define services, networks and volumes. The default file is `docker-compose.yml`.
Each container has its own service configuration, much like command-line parameters to `docker run`.

Docker Compose isn't part of Docker Engine, so installation is required.

Run `sudo -i` then the two commands below:
```sh
$ curl -L https://github.com/docker/compose/releases/download/1.6.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
$ chmod +x /usr/local/bin/docker-compose
# Compose is now installed!
```

In the previous Dockerfile example you added the `src/binary` file to the image. In result, each time you recompile the file you also will need to re-build the image.

The following `docker-compose.yml` example uses that same Dockerfile, but instead of copying the file it will mount the `src/binary` on `/binary`. That means that you can make changes to the file without having to re-build the image.
```compose
myimage:
  build: .
  volumes:
    - ./src/binary:/binary
  command: ./binary
```

Go to https://docs.docker.com/compose/compose-file/ and learn more about Docker Compose.

### Exercise
Transform the previous Dockerfile exercise to a `docker-compose.yml` and instead of copying the binary, share it as a volume.
