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

#### COUNTRY
- cdCountry (A rajouter) 


### Controlleur : 

- Rajouter une action permettant de renvoyer une question en particulière 
- Séparer l'action indexAction en deux action afin d'en créer une seulement pour la mise à jour des question ayant lieux en Ajax 

## Rappel pour les developpeurs : 

Pour récupérer code : 
- git stash //met de coté ton code
- git pull //récupère le dernier commit
- git stash apply //récupère ton code
- git add . //rajoute tes modification
- git commit…
- git push

