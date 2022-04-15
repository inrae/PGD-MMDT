# PGD-MMDT
### Metadata management tool for datasets

**PGD-MMDT** is a metadata entry interface for datasets, coupled with a MongoDB database. This metadata entry interface integrates with the Structure Data Management Plan of a research unit or infrastructure, to address issues of data organisation, documentation, storage and sharing.

This interface allows you to :

- **Describe** a dataset using metadata of various types (Description)
- **Open** a storage space for the dataset (Storage)
- **Assign** rights to access the dataset (Privacy)
- **Search** datasets by their metadata (Accessibility)

------
**Maintainers**: Philippe Chaumeil & François Ehrenmann - INRAE - UMR 1202 BIOGECO (2019-2021)

## I. Install on a NAS synology with docker package

Please consult the tutorial [ Install on a NAS synology with docker package](https://github.com/inrae/PGD-MMDT/blob/main/web/docs/NAS_synology_install_pgd_mmdt.pdf).

## II. Install on your linux computer or linux / unix server

Requirements: a recent OS that support Docker (see https://www.docker.com/)

### Retrieving the code
Go to the destination directory of your choice then clone the repository and `cd` to your clone path:

```sh
git clone https://git.renater.fr/anonscm/git/pgd-biogeco/pgd-biogeco.git pgd-mmdt
cd pgd-mmdt
```

## Installation of Docker containers

PGD-MMDT uses 3 Docker containers for 3 distinct services:

- **pgd-mmdt-db** which hosts the mongoDB database
- **pgd-mmdt-scan** which scans the data and updates the contents of the database and the web interface
- **pgd-mmdt-web** which hosts the web server and the web interface pages

### Configuration

You need to configure the passwords for the users admin-mongo, userw-pgd & userr-pgd in the following files:

- dockerdbpart/initialization/setupdb.js
- dockerscanpart/config.py
- web/config/config.php

You must set the **$docker_mode parameter to 1** in the web/config/config.php file

You can change the default values proposed to the user by modifying the Master_PGD.json file

- dockerscanpart/Master_PGD.json

### pgd-mmdt-db

To create the **pgd-mmdt-dbpart** image, go to the dockerdbpart folder and run the command

```sh
cd dockerdbpart
sudo docker build -t pgd-mmdt-dbpart .
```

To run the **pgd-mmdt-db** container

```sh
sudo docker run -d -v mongodbfiles:/data -v <path to your datas>:/pgd_data --name pgd-mmdt-db pgd-mmdt-dbpart
```

### pgd-mmdt-scan

To create the image **pgd-mmdt-scanpart** go to the dockerscanpart folder and run the command

```sh
cd ../dockerscanpart
sudo docker build -t pgd-mmdt-scanpart .
```

To run the **pgd-mmdt-scan** container

```sh
sudo docker run -d -v <path to your datas>:/pgd_data -v $(pwd)/../web/js:/js --link pgd-mmdt-db --name pgd-mmdt-scan pgd-mmdt-scanpart
```

### pgd-mmdt-web

To create the image **pgd-mmdt-webpart** go to the dockerscanpart folder and run the command

```sh
cd ../dockerwebpart
sudo docker build -t pgd-mmdt-webpart .
```

To launch the **pgd-mmdt-web** container

```sh
sudo docker run -d -p 8888:80 -v $(pwd)/../web:/var/www/html --link pgd-mmdt-db --name pgd-mmdt-web pgd-mmdt-webpart
```

### Check that all containers are running correctly

```sh
sudo docker ps -a
```

### Access to the web application
Go to this URL to view the web application: http://localhost:8888/

## Administration

### Restarting containers after a host machine shutdown
```sh
sudo docker container start pgd-mmdt-db
sudo docker container start pgd-mmdt-scan
sudo docker container start pgd-mmdt-web
```

### Uninstalling the full pgd Docker version

```sh
sudo docker container stop pgd-mmdt-scan
sudo docker container stop pgd-mmdt-web
sudo docker container stop pgd-mmdt-db
sudo docker container rm pgd-mmdt-scan
sudo docker container rm pgd-mmdt-web
sudo docker container rm pgd-mmdt-db
sudo docker image rm pgd-mmdt-dbpart
sudo docker image rm pgd-mmdt-scanpart
sudo docker image rm pgd-mmdt-webpart
```
### Remove data from the database

```sh
sudo docker volume rm mongodbfiles
```

------

### Funded by:

* INRAE UMR 1202 BIOGECO, Biodiversité Gènes et Communautés

### License

GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 - See the included LICENSE file.
