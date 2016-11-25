# Ono

A Symfony project created on September 15, 2016, 10:30 am.

===

## Initialisation :

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
  - Pour Linux :
  ```
  wget https://getcomposer.org/composer.phar
  ```
  - Pour Mac :
  ```
  curl https://getcomposer.org/composer.phar -o composer.phar
  ```

#### Configuration locale du projet

- Installer les dépendances d'Ono avec Composer
```
php composer.phar install
```

Pour les paramètres :

- Laissez par défaut :
  - database_host (127.0.0.1)
  - database_port (8888)
  - database_name (db-ono)
  - database_user (root)
  - database_password (root)
  - mailer_transport (smtp)
  - mailer_host (127.0.0.1)
  - mailer_user (null)
  - mailer_password (null)
  - secret (51c22993d139d305998644299354a849fbdd16dd)
- unix_socket laissez par défaut (null)
  Sur Mac, si vous obtenez une erreur comme "No such file or directory" :
  - Supprimez le fichier app/config/parameters.yml
  - Recommencez l'installation des dépendances
  - Pour "unix_socket", mettez cette valeur : /Applications/MAMP/tmp/mysql.sock

- Une erreur peut apparaître car la base de données "db-ono" n'existe pas. Dans tous les cas, créez-là :
```
Symfony db create
```

- Finissez l'installation des dépendances d'Ono pour vérifier que vous n'avez plus d'erreur
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

===
## Rappel pour les développeurs :


### Installer Sass
SASS a été implémenter pour gérer les assets. Si vous ne disposer pas de sass, vous pouvez l'installer en lançant :
```
gem install sass
```
Sass utilise ruby, passez ici si vous n'avez pas gem
https://rubygems.org/pages/download

####Afin de lancer le watching des assets et mettre à jour vos css :
Depuis la racine Ono
```
cd src/Ono/MapBundle/Resources/public/
```
Lancer la commande :
```
sass --watch sass/application.sass:css/style.css
```

### Pour récupérer code avant de push ses modifications :

```
git stash // Met ses modifications locales de côté
git pull // Récupère les éventuelles mises à jour sur le repo
git stash apply // Récupère ses modifications locales
git add . // Ajoute les modifications
git commit -m "Votre commentaire de commit"
git push
```

### Pour gérer les utilisateurs :

- Promouvoire :
```
php bin/console fos:user:promote monutilisateur ROLE_ADMIN
```

- Rétrograder :
```
php bin/console fos:user:demote monutilisateur ROLE_ADMIN
```

- Créer :
```
php bin/console fos:user:create monutilisateur test@example.com motdepasse
```

### Pour installation CKEditor :

- Lancer cette commande après mise en place

```
php bin/console assets:install web --symlink
```

===

## Liens externes :

Affichage article à la Pinterest :
http://codepen.io/dudleystorey/pen/yqrhw

Pour plus d'informations sur CKEditor :
http://symfony.com/doc/current/bundles/IvoryCKEditorBundle/index.html

Lien utile pour upload de vidéo
https://openclassrooms.com/forum/sujet/upload-de-video-symfony2-85865
