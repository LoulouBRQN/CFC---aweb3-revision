# Projet révision horaire CFPT - AWEB3 UE1

**Nom :** Louis Bourquin  
**Projet :** Application web de gestion des horaires des classes du CFPT.

## Description

Cette application permet de gérer :

- les classes ;
- les cours ;
- les créneaux horaires ;
- la consultation de l'horaire d'une classe.

Le projet utilise PHP, MySQL, PDO et une API RESTful en JSON.

## Installation

1. Copier le dossier `horaire-eleve` dans le dossier du serveur web, par exemple `htdocs` avec XAMPP.
2. Démarrer Apache et MySQL.
3. Ouvrir phpMyAdmin ou un client MySQL.
4. Exécuter le fichier `sql/init.sql`.
5. Vérifier les identifiants dans `config/database.php`.
6. Ouvrir :

`http://localhost/horaire-eleve/`

Si le dossier est installé avec un autre nom ou dans un autre emplacement, adapter l'URL du menu dans `includes/header.php`.

## Base de données

Le fichier `sql/init.sql` crée la base `horaire` et les trois tables :

- `classes`
- `cours`
- `creneaux`

Il ajoute également des données d'exemple.

## API

### Classes

- `GET /horaire-eleve/api/classes` : liste des classes
- `POST /horaire-eleve/api/classes` : créer une classe
- `DELETE /horaire-eleve/api/classes/1` : supprimer une classe

Exemple POST :

```json
{
  "nom": "I.DA-P3A",
  "annee_scolaire": "2026-2027"
}
```

### Cours

- `GET /horaire-eleve/api/cours` : liste des cours
- `GET /horaire-eleve/api/cours?classe=I.DA-P3A` : horaire d'une classe
- `POST /horaire-eleve/api/cours` : créer un cours
- `DELETE /horaire-eleve/api/cours/1` : supprimer un cours

Exemple POST :

```json
{
  "code": "AWEB3",
  "nom": "Atelier Web 3e année S1"
}
```

### Créneaux

- `GET /horaire-eleve/api/creneaux` : liste des créneaux
- `GET /horaire-eleve/api/creneaux/1` : détail d'un créneau
- `POST /horaire-eleve/api/creneaux` : créer un créneau
- `PUT /horaire-eleve/api/creneaux/1` : modifier un créneau
- `DELETE /horaire-eleve/api/creneaux/1` : supprimer un créneau

Exemple POST ou PUT :

```json
{
  "classe_id": 1,
  "cours_id": 1,
  "jour": "jeudi",
  "heure_debut": "08:05",
  "heure_fin": "11:40",
  "salle": "R104"
}
```

## Codes HTTP utilisés

- `200` : requête réussie
- `201` : donnée créée
- `204` : donnée supprimée
- `400` : données envoyées incorrectes
- `404` : donnée ou ressource introuvable
- `405` : méthode HTTP non autorisée
- `500` : erreur serveur / base de données

## Structure

```text
horaire-eleve/
├── index.php
├── config/
│   ├── constants.php
│   └── database.php
├── connexion/
│   └── db.php
├── functions/
│   ├── classes.php
│   ├── cours.php
│   └── creneaux.php
├── includes/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── classes.php
│   ├── cours.php
│   ├── creneaux.php
│   └── horaire.php
├── api/
│   ├── index.php
│   └── .htaccess
├── sql/
│   └── init.sql
├── utils/
│   └── utils.php
└── README.md
```

## Séparation du code

La logique de connexion à la base, les fonctions SQL, l'affichage et les pages sont séparés dans différents fichiers. Les fichiers sont réutilisés avec `require_once` et `include`.
