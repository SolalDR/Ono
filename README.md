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

### Formulaire

#### Date de naissance
- Changer la plage des années


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

#### COUNTRY
- cdCountry (A rajouter)

### Controlleur :

- Rajouter une action permettant de renvoyer une question en particulière
- Gerer l'attribut published, le mettre par défaut à false lors de addAction dans le controlleur Map

### Vue

- Créer/Récupérer les icones pour chaque thèmes / les intégré dans les assets avec des nom pertinent (qui seront repris à l'aide du cdTheme)

## Rappel pour les developpeurs :

Pour récupérer code :
- git stash //met de coté ton code
- git pull //récupère le dernier commit
- git stash apply //récupère ton code
- git add . //rajoute tes modification
- git commit…
- git push
