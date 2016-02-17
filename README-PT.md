# Hands-on Docker

Os seguintes passos foram adaptados do site oficial [Docker Training](https://training.docker.com).

## Índice
1. [Introdução](#introduction)
2. [Primeiros Passos](#firststeps)
3. [Containers](#containers)
4. [Rede](#networking)
5. [Dockerfile](#dockerfile)
6. [Docker Compose](#dockercompose)

## <a name='introduction'></a> Introdução

Este guia foi escrito para que através da execução de um conjunto de comandos possas aprender de forma rápida um pouco mais sobre Docker.

Segue todos os passos descritos neste guia. Se tiveres alguma dúvida pede ajuda aos monitores que estão a acompanhar a sessão.

Primeiro passo: Instala o Docker na Máquina Virtual fornecida - Ubuntu Server.

### <a name="install-docker"></a> Instalar o Docker

Usa o Putty, ou qualquer outro cliente SSH, para te ligares à Máquina Virtual. Na máquina, executa os seguintes comandos:

```sh
$ wget -qO- https://get.docker.com | sh
```

Depois de terminada a instalação adiciona o teu utilizador ao group `docker` (o objetivo é não teres de usar o comando `sudo` em todos os comandos):
```
$ sudo usermod -aG docker user
```

Sai da shell atual:
```sh
$ exit
```
e volta a fazer login.

## <a name='firststeps'></a> Primeiros Passos

A partir deste momento vamos apenas trabalhar com o client docker. Como sempre, o primeiro exemplo é um Hello World ;).
```sh
$ docker run hello-world
```
Verificar a versão do Docker Client and Server
```sh
$ docker version
```

### Docker Hub
O Docker Hub é um serviço cloud que proporciona um repositório central para a descoberta, distribuição e gestão das versões das imagens. Além disso permite a colaboração entre utilizadores (equipas) e a automação de tarefas relacionadas com o processo de desenvolvimento.

Abre um web browser e acede a [Docker Hub](https://hub.docker.com) para procurares no repositório público.

![Docker Hub Search Header](https://cld.pt/dl/download/00ec6459-02b9-49fd-af2a-a1b1150df808/DockerHubSearch.png)

#### Exercício
Encontra a barra de procura (imagem acima) e procura pelo repositório tomcat. Aproveita para conheceres um pouco melhor o Docker Hub.

### Imagens Locais
As imagens são mantidas na tua máquina local no daemon docker. Para veres as imagens docker disponíveis na máquina local (Docker daemon Local), executa o seguinte comando:

```sh
$ docker images
```
Quantas imagens tens disponíveis localmente?

#### Exercício
Executa o comando `docker search` para encontrares todas as imagens relacionadas com tomcat.

## <a name="containers"></a> Containers

Vamos criar um container com a imagem do `ubuntu:14.04` que vai executar o comando `echo` :

```sh
$ docker run ubuntu:14.04 echo "hello world"
```
Vamos experimentar mais alguns comandos (repara que os comandos estão a ser executados dentro do container):
```sh
## list container processes
$ docker run ubuntu:14.04 ps ax
```
Repara que a segunda execução foi mais rápida, porque a imagem do ubuntu 14.04 já estava presente localmente.

### Opções
- opção: `-i` diz ao docker para ligar ao STDIN do container
- opção: `-t` especifica que se pretende ter um pseudo-terminal
- opção: `-d` diz ao docker para fazer detach do container (i.e. background)

Vamos experimentar as opções `-i` e `-t` com o processo `bash`
```sh
$ docker run -i -t ubuntu:14.04 /bin/bash
```
Depois da execução do comando anterior, estamos dentro container. Ao executares os comandos seguintes, vais estar a adicionar um  novo utilizador (admin) e a instalar o vim.
```sh
# Adicionar um utilizador
$ adduser admin
# Adicionar o utilizador ao grupo sudo
$ adduser admin sudo
# Mudar para o user admin
$ su admin
# Instalar o vim
$ sudo apt-get install vim
# Vamos testar, para garantir que o vim foi corretamente instalado (para sair do vim -> :q!)
$ vim
# Tudo ok? Vamos sair do container (porque é que precisamos de executar duas vezes exit?)
$ exit
$ exit
```

Volta a executar o último comando docker e testa os seguintes comandos.
```sh
# Tenta alterar para o utilizador admin
$ su admin
# O utilizador não existe, porquê?
# Tenta iniciar o vim
$ vim
# O vim não está instalado, porquê?
# Vamos sair do container, outra vez!
$ exit
```
Agora executa um container com a image do `ubuntu:14.04` e o comando `/bin/bash` (dentro do container)
```sh
$ docker run -it ubuntu:14.04 /bin/bash
  $ ps -ef
  # Nota que o processo bash é PID 1. Assim que este processo seja terminado, o container será finalizado.
  # Hint: Para saires sem matares o container pressiona CTRL+P+Q

```
Ao pressionares o `CTRL+P+Q`, o processo não foi terminado, em vez disso foi feito um detached do container.

Ao executares o seguinte comando, verás que o container continuar a ser executado.
```
$ docker ps
```

Usa a opção `-a` para listares todos os containers (incluindo containers que estão parados)
```sh
$ docker ps -a
```
Repara na coluna `STATUS`. Containers que se encontram parados (stopped) estão identificados como `Exited () ...`.

Vamos experimentar a opção de detach `-d`, num container que executa um ping 100 vezes.
```sh
$ docker run -d ubuntu:14.04 ping 127.0.0.1 -c 100
```
Ao executares o comando anterior, obténs um ID, que é o Long ID do container.
Com a ajuda do comando `docker logs`, analisa os logs do docker.
Para encontrar o container correto precisamos dos primeiros 6 digitos do seu ID (obtido no passo anterior).
```sh
$ docker logs <ID>
```
Para simplificar podemos dar um nome ao container, usando a opção `--name`. O próximo comando dá o nome test-ping ao container.
```sh
$ docker run -d --name test-ping ubuntu:14:04 ping 127.0.0.1 -c 100
```
Se quiseres estar sempre a ver o log, podes usar a opção `-f` - tal como com o comando `tail` - (`CTRL+C` para saires)
```sh
$ docker logs -f test-ping
```

Para leres mais sobre o comando `docker run `segue o link https://docs.docker.com/engine/reference/run/ .

#### Exercício
Aproveita para conheceres melhor `docker run`,  `docker stop`, `docker start`, `docker ps ` e `docker logs`.

## <a name="networking"></a> Rede
Nesta secção vamos usar o imagem tomcat:7

Executa o container tomcat com a opção `-P` (esta opção vai mapear todos os portos do container para o host)
```sh
$ docker run -d --name tomcat -P tomcat:7
```
Usa o comando `docker ps` para verificares qual é o porto mapeado para o porto `8080` do container - coluna `PORTS`
```sh
$ docker ps
```
Abre o browser na máquina host e vai a `http://VM_IP:PORT` (substitui o VM_IP pelo IP da máquina virtual Ubuntu Server e PORT pelo porto que obtiveste no comando anterior)
Pára o container
```sh
$ docker stop tomcat
```
Remove o container
```sh
$ docker rm tomcat
```
Vamos remover todos os containers que estejam parados (stopped).
```sh
# Magia!!
$ docker rm `docker ps -aq`
```

### Linking containers
Re-executa o container tomcat.
```sh
$ docker run -d --name tomcat -P tomcat:7
```
Arranca um novo container e tenta fazer um ping para o container tomcat.
```sh
$ docker run -it ubuntu:14.04 /bin/bash
    $ ping tomcat
    # Falhou!! Sai do container
    $ exit
```
Executa o container tomcat com a opção -P (lembra-te que há três passos removemos o container tomcat)
```sh
$ docker run -d --name tomcat -P tomcat:7
```

A opção `--link` permite conectar dois containers. A sintaxe é `<name or id>:<alias>` .
```sh
$ docker run -it --link tomcat:tomcat ubuntu:14.04 /bin/bash
    $ ping tomcat
    # Sucesso!! :D Nota que estás dentro do container ubuntu!!
```

## <a name="dockerfile"></a> Dockerfile
Dockerfile é um ficheiro de texto que contém as instruções (comandos) necessários para criar uma imagem Docker. O comando `docker build` lê o dockerfile, de forma sequêncial, e inicia o processo automático de criação da imagem.

Vai para a pasta que tem o Dockerfile e executa o comando:
```sh
$ docker build -t <name>:<tag> .
```

Ao usar a opção `-t` faz a tag ser `latest` e a opção `-f` permite escolher o ficheiro Dockerfile (vamos omitir a opção `-f`). O `.` significa que o `docker build` deverá usar o diretório corrente como contexto.

A tabela seguinte apresenta as principais instruções que se podem encontrar no `Dockerfile`. Um `Dockerfile` começa sempre com a instrução `FROM` (a instrução `MAINTAINER` não conta!).

| Instrução   |                   Descrição                      |
| ----------- | ------------------------------------------------ |
| FROM        | Define a base da imagem para as instruções seguintes. |
| RUN         | Executa um comando numa nova camada sobre a base atual. |
| ADD         | Copia novos ficheiros, diretórios ou URLs remotos e adiciona-os ao sistema de ficheiros do container. |
| COPY        | Copia novos ficheiros ou diretórios e adiciona-os ao sistema de ficheiros do container. |
| CMD         | Só pode haver uma instrução CMD por Dockerfile. `CMD` proporciona defaults para a execução do container. |
| ENTRYPOINT  | Permite configurar um container que deverá ser executado como um eecutável. |

O próximo exemplo ilustra um Dockerfile com uma imagem `ubuntu:14.04` de base que cria um utilizador admin, adiciona-o ao grupo `sudo` e define-o como utilizador base. O ficheiro `src/binary` é copiado para a raíz da imagem e é definido como comando inicial de execução.

```docker
MAINTAINER Tiago Pires <tiago-a-pires@telecom.pt>
FROM ubuntu:14.04

RUN adduser --system --shell /bin/bash --group --disabled-password admin && \
    adduser admin sudo

USER admin

ADD src/binary /binary

CMD ["/binary"]
```

Para leres mais sobre Dockerfile segue o link https://docs.docker.com/engine/reference/builder/

### Exercício
Cria um Dockerfile de uma qualquer imagem (escolhe uma qualquer). Cria um utilizador e define o comando `sh` ou `bash` como default

## <a name="dockercompose"></a> Docker Compose
O Docker Compose é um ficheiro YAML que ajuda a definir serviços, redes e volumes. O ficheiro de configuração do Docker Compose (by default) é o `docker-compose.yml`.
Cada continer tem o seu próprio serviço de configuração, tal como os parâmetros command-line `docker run`.

O Docker Compose não é parte do Docker Engine, portanto é necessário uma instalação extra.

Executa `sudo -i` seguido dos seguintes comandos:
```sh
$ curl -L https://github.com/docker/compose/releases/download/1.6.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
$ chmod +x /usr/local/bin/docker-compose
# O Docker Compose está instalado!
```

No exemplo anterior do Dockerfile copiaste o ficheiro `src/binary` para a imagem. Isso significa que cada vez que recompiles o ficheiro, também irás necessitar de re-construir a imagem.

O exemplo a seguir é um `docker-compose.yml` que cria a imagem a partir do mesmo Dockerfile, mas em alternativa a copiar o ficheiro `src/binary`,  vai montá-lo em `/binary`. Esta diferença permite fazer recompilar o mesmo e usá-lo sem que seja necessário reconstruir a imagem.
```compose
myimage:
  build: .
  volumes:
    - ./src/binary:/binary
  command: ./binary
```

Informação mais detalhada sobre o Docker Compose pode ser encontrada aqui: https://docs.docker.com/compose/compose-file/

### Exercício
Transforma o Dockerfile do exercício anterior num ficheiro `docker-compose.yml` e em vez de copiar o ficheiro binário, partilha um volume.
