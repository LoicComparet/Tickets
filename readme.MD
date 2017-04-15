# Installation de la base de données

La structure de la base de données se trouve sous 'data/sql/install.sql'. Charger le fichier.
J'ai choisi le moteur MyISAM pour ses performances. InnoDb n'est nécessaire dans ce cas.

# Pré-requis

Modifier le fichier 'config/db.php' pour renseigner les bonnes valeurs de connexion à la base de données.

# Informations

Les scripts sont tous en error_reporting (E_ALL | E_STRICT), memory_limit à 128M et max_execution_time à 30 secondes. La micro-application ne traite pas du sujet de l'authentification et des autorisations, du log des lignes erronées du fichier csv (qui peut être un axe d'amélioration par la suite), et ne gère pas les levées d'exceptions. 

# Charger le fichier 'tickets_appels_201202.csv'

Avant de continuer, imaginons comment nous recevons ces données d'un partenaire. Un partenaire nous a transmi ce fichier par flux CFT. Le contrôle de fin de transfert le positionne dans un répertoire pour traitement.

Le répertoire est le suivant : 'data/csv/tickets_appels_201202.csv'.

Un cron inspecte ce répertoire pour savoir si des données sont à traiter. Si oui, il lance le script 'load.php'. Pour des raisons de facilité, ce script est modifié pour prendre en compte directement le fichier au lieu d'inspecter le répertoire. Il ne traite pas de la suppression de ce dernier.

Pour charger le fichier, se positionner à la racine du répertoire :

```sh
$ php load.php
```

Les pré-requis (à savoir conserver la directive memory_limit à 128M et max_execution_time à 30 secondes) sont respectés.

La consommation mémoire et le temps d'exécution sont affichées à la fin du script. Si besoin, les pré-requis peuvent être vu à la baisse pour exécuter le script.

Le script filtre et valide les entrées afin d'éviter toutes injections et/ou XSS, références indirectes, etc... (OWASP Top 10).

NB : ce script peut être amélioré en utilisant un adapteur permettant de rendre abstrait le type de fichier. De cette manière, n'importe quel csv (par exemple, dans notre cas, nous avons des Tickets, mais on peut aussi  bien avoir autre chose)  pourraient être interprété par ce script )

NB2 : aucun tests unitaires n'est fournis.

# Very very micro-application

Pour la gestion des fonctionnalités demandées, se positionner à la racine.

```sh
$ php -S 0.0.0.0:8000 -t public
```

Lancer le navigateur et atteignez la page suivante : http://localhost:8000. 

# Dump de la base de données résultante (Optionnel)

Un dump est fournit sous 'data/sql/dump.sql'.
