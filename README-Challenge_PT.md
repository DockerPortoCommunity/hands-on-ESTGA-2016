> Desafio

## Software a usar
- Docker Engine
- Docker Compose

## Descrição
Usando o software mencionado constrói o Dockerfile e o `docker-compose.yml` para executar 2 containers:
- frontend container: uma imagem PHP preparada para fazer deploy do código fonte na pasta `src`.
- database container: uma imagem MySQL para guardar a informação produzida pelo frontend container.

Deves inicializar os componentes usando o `docker-compose`.

### Requisitos
- Para o frontend container, deves usar a imagem `kabachook/docker-nginx-php:latest`. A imagem está disponível no Docker Hub.
- No container frontend, tens que usar o código PHP fornecido em `src` no directório `/var/www`.
- A ligação entre os containers de frontend e o de MySQL é feito através do porto 3306/TCP.
- O container MySQL deve ter as seguintes variáveis de ambiente definidas:
  - MYSQL_ROOT_PASSWORD=admin
  - MYSQL_DATABASE=forum

Have fun!
