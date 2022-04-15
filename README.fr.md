# PGD-MMDT
### Management of MetaData Tool

PGD-MMDT est une interface de saisie de métadonnées pour des jeux de données de la recherche, couplée à une base de données MongoDB. Cette interface de saisie des métadonnées s'intègre au Plan de Gestion de Données de Structure d'une unité ou infrastructure de recherche, pour répondre aux questions de l'organisation, de la documentation, du stockage et du partage des données.

Cette interface permet de :

- **Décrire** un jeu de données à l'aide de métadonnées de différents types (Description)
- **Ouvrir** un espace de stockage pour le jeu de données (Stockage)
- **Affecter** des droits pour l'accès au jeu de données (Confidentialité)
- **Rechercher** des jeux de données à partir de leurs métadonnées (Accessibilité)

------
**Maintenance & développement**: Philippe Chaumeil & François Ehrenmann - INRAE - UMR 1202 BIOGECO (2019-2021)

## I. Installation sur un NAS synology avec paquet docker

Merci de consulter le tutoriel [ Install on a NAS synology with docker package](https://github.com/inrae/PGD-MMDT/blob/main/web/docs/NAS_synology_install_pgd_mmdt.pdf).

## II. Installation sur un PC personnel sous linux ou sur un serveur linux / unix

Pré-requis: un OS récent qui supporte Docker (voir https://www.docker.com/)

### Récupération du code
Se placer dans le répertoire destination de votre choix puis cloner le dépôt et `cd` vers votre chemin de clone :

```sh
git clone https://git.renater.fr/anonscm/git/pgd-biogeco/pgd-biogeco.git pgd-mmdt
cd pgd-mmdt
```

## Installation containers Docker

PGD-MMDT utilise 3 containers Docker pour 3 services distincts :

- **pgd-mmdt-db** qui héberge la base de données mongoDB
- **pgd-mmdt-scan** qui se charge de scanner les données et mettre à jour le contenu de la base de données et de l'interface web
- **pgd-mmdt-web** qui héberge le serveur web et les pages de l'interface web

### Configuration

Vous devez configurer les mots de passe pour les utilisateurs admin-mongo, userw-pgd & userr-pgd dans les fichiers suivants :

- dockerdbpart/initialisation/setupdb.js
- dockerscanpart/config.py
- web/config/config.php

Vous devez passer le paramètre **$docker_mode à 1** dans le fichier web/config/config.php

Vous pouvez modifier les valeurs proposées par défaut à l'utilisateur en modifiant le fichier Master_PGD.json

- dockerscanpart/Master_PGD.json

### pgd-mmdt-db

Pour créer l'image **pgd-mmdt-dbpart**, se positionner dans le dossier dockerdbpart et lancer la commande

```sh
cd dockerdbpart
sudo docker build -t pgd-mmdt-dbpart .
```

Pour lancer le container **pgd-mmdt-db**

```sh
sudo docker run -d -v mongodbfiles:/data -v <path to your datas>:/pgd_data --name pgd-mmdt-db pgd-mmdt-dbpart
```

### pgd-mmdt-scan

Pour créer l'image **pgd-mmdt-scanpart** se positionner dans le dossier dockerscanpart et lancer la commande

```sh
cd ../dockerscanpart
sudo docker build -t pgd-mmdt-scanpart .
```

Pour lancer le container **pgd-mmdt-scan**

```sh
sudo docker run -d  -v <path to your datas>:/pgd_data -v $(pwd)/../web/js:/js --link biogeco-pgd-db --name pgd-mmdt-scan pgd-mmdt-scanpart
```

### pgd-mmdt-web

Pour créer l'image **pgd-mmdt-webpart** se positionner dans le dossier dockerscanpart et lancer la commande

```sh
cd ../dockerwebpart
sudo docker build -t pgd-mmdt-webpart .
```

Pour lancer le container **pgd-mmdt-web**

```sh
sudo docker run -d -p 8888:80 -v $(pwd)/../web:/var/www/html --link biogeco-pgd-db --name pgd-mmdt-web pgd-mmdt-webpart
```

### Vérifier que tous les containers tournent correctement

```sh
sudo docker ps -a
```

### Accès à l'application web
Se rendre à cette URL pour afficher l'application web : http://localhost:8888/

## Administration

### Redémarrage des containers après un arrêt de la machine hôte
```sh
sudo docker container start pgd-mmdt-db
sudo docker container start pgd-mmdt-scan
sudo docker container start pgd-mmdt-web
```

### Désinstallation complète de la version pgd Docker

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
### Suppression des données de la base

```sh
sudo docker volume rm mongodbfiles
```

------

### Funded by:

* INRAE UMR 1202 BIOGECO, Biodiversité Gènes et Communautés

### License

GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 - See the included LICENSE file.
