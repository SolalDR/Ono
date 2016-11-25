Ono
===

A Symfony project created on September 15, 2016, 10:30 am.



## Développement Front :

### Page à intégrer
- Profil
- Contact
- Mentions légales (travail de rédaction)
- Thèmes (décrit chaque variable)
- À propos


## Développement Back :

### Modèle

#### TRADUCTION QUESTION : (ManyToOne : Question)
- idQuestion
- libQuestion
- cdLanguage

#### USER : (OneToMany Entity responses, OneToOne Entity Country, OneToOne Entity Language)
- FOS USER Bundle attributes : nom ; prénom ; datesign ; datelast ; mail ; status ; etc...
- description
- country
- cdLanguage
- responses
- dtnaissance

#### RESPONSE :
- add language attributes

#### RESPONSE (ManyToOne : question) (ManyToOne : User) (ManyToMany : theme)(ManyToOne : Country)
- nblike (A rajouter)


### Vue

- Créer/Récupérer les icônes pour chaque thème / les intégrer dans les assets avec des noms pertinents (qui seront repris à l'aide du cdTheme)
- Customiser les pages d'erreur, Erreur 500, Erreur 404, Access Denied




## Rappel pour les développeurs :

### Installation :

#### Pré-requis

Assurez-vous d'avoir votre serveur web en cours de fonctionnement (MySQL inclus)

#### Alias-Symfony

- Installer Alias-Symfony (créé par SolalDR) à l'aide de son README pour simplifier les commandes
  https://github.com/SolalDR/alias-symfony

#### Configuration de PHP (Timezone)

- Trouver le répertoire de votre php.ini (configuration PHP) avec la commande :
```
php -i | grep php.ini
```

- Placez-vous dans le dossier trouvé (par exemple /etc)
- Si vous n'avez qu'un fichier php.ini.default, copiez-le en php.ini avec la commande cp
- Ouvrez php.ini avec un éditeur de texte (ex: nano), recherchez la ligne avec date.timezone et modifiez là ainsi :
```
date.timezone = Europe/Paris
```

#### Récupération du repo & Composer

- Dans le répertoire de votre serveur web, clonez le repository
```
git clone https://github.com/SolalDR/Ono.git
```

- Allez dans le dossier Ono et téléchargez Composer
```
wget https://getcomposer.org/composer.phar
```

#### Configuration locale du projet

- Installer les dépendances d'Ono avec Composer
```
php composer.phar install
```

Pour les paramètres :

- Laissez par défaut :
  - database_host (127.0.0.1)
  - database_port (null)
  - database_name (symfony)
  - database_user (root)
  - database_password (null)
  - mailer_transport (smtp)
  - mailer_host (127.0.0.1)
  - mailer_user (null)
  - mailer_password (null)
  - secret (51c22993d139d305998644299354a849fbdd16dd)
- unix_socket : selon votre OS,
  - Linux : laissez par défaut (null)
  - Mac : /Applications/MAMP/tmp/mysql/mysql.sock

- Une erreur apparaît car la base de données "db-ono" n'existe pas. Créez-là :
```
Symfony db create
```

- Finissez l'installation des dépendances d'Ono
```
php composer.phar install
```

- Installer la structure de la base de données
```
Symfony update -f
```

- Installer les fixtures pour la base de données
```
Symfony load fixtures
```

### Pour récupérer code avant de push ses modifications :

- git stash // Met ses modifications locales de côté
- git pull // Récupère les éventuelles mises à jour sur le repo
- git stash apply // Récupère ses modifications locales
- Résoudre les éventuels conflits
- git add . // Ajoute les modifications
- git commit…
- git push

### Pour gérer les utilisateurs :

- Promouvoire : php bin/console fos:user:promote monutilisateur ROLE_ADMIN
- Rétrograder : php bin/console fos:user:demote monutilisateur ROLE_ADMIN
- Créer : php bin/console fos:user:create monutilisateur test@example.com motdepasse

### Pour installation CKEditor :

- Lancer cette commande après mise en place

```
php bin/console assets:install web --symlink
```

### Liens externes :

Pour plus d'informations sur CKEditor :
http://symfony.com/doc/current/bundles/IvoryCKEditorBundle/index.html

Lien utile pour upload de vidéo
https://openclassrooms.com/forum/sujet/upload-de-video-symfony2-85865
