> Challenge

## Software to use
- Docker Engine
- Docker Compose

## Description
Using the software mentioned above you should be able to build a Dockerfile and a `docker-compose.yml` that will run 2 containers:
- frontend container: a PHP image ready to deploy the source code on `src` folder.
- database container: a MySQL image to save the data produced by the frontend container.

You should initialize your software stack with `docker-compose`.

### Requirements
- For the frontend container you must use `kabachook/docker-nginx-php:latest` image. You can find it in the Docker Hub.
- On the frontend container, you must use the PHP code provided on the `src` folder in the folder `/var/www`. 
- You'll connect the frontend to the MySQL container through  port 3306/TCP.
- MySQL container must have the following environment variables initialized:
  - MYSQL_ROOT_PASSWORD=admin
  - MYSQL_DATABASE=forum

Have fun!
