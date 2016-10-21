Ono
===

A Symfony project created on September 15, 2016, 10:30 am.



## Développement Front :

### Page à intégrer
- Profil
- Contact
- Mention légale (travail de rédaction)
- Thème (décrit chaque variable)
- A propos


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

#### LIKE RESPONSE (OneToOne Entity Response, OneToOne Entity User)
- id_response
- id_user

#### LIKE QUESTION  (OneToOne Entity Question, OneToOne Entity User)
- id_question
- id_user

#### RESPONSE (ManyToOne : question) (ManyToOne : User) (ManyToMany : theme)(ManyToOne : Country)
- nblike (A rajouter)



### Vue

- Créer/Récupérer les icones pour chaque thèmes / les intégré dans les assets avec des nom pertinent (qui seront repris à l'aide du cdTheme)
- Costumizer les pages d'erreur, Erreur 500, Erreur 404, Access Denied




## Rappel pour les developpeurs :

Pour récupérer code :
- git stash //met de coté ton code
- git pull //récupère le dernier commit
- git stash apply //récupère ton code
- git add . //rajoute tes modification
- git commit…
- git push

Pour gérer les utilisateurs :
- Promouvoire : php app/console fos:user:promote monutilisateur ROLE_ADMIN
- Rétrograder : php app/console fos:user:demote monutilisateur ROLE_ADMIN
- Créer : php app/console fos:user:create monutilisateur test@example.com motdepasse
