# ODaCo

> Open Database of Cocktails

## Sommaire

1. [Informations](#informations)

2. [Sur le site](#sur-le-site)  
    2.1 [Fonctionnalités générales](#fonctionnalités-générales)  
    2.2 [Liste *à Faire*](#liste-à-faire)  
    2.3 [Langue, thèmes et personnalisation](#langue-thèmes-et-personnalisation)  

3. [Sur le Code](#sur-le-code)  
    3.1 [Cookies et LocalStorage](#cookies-et-localstorage)  
    3.2 [Base de données](#base-de-données)

## Informations

> [!NOTE]  
> Le site est [en ligne](https://odaco-production.up.railway.app/)

## Sur le site

### Fonctionnalités générales

Le site permet de :  
1. Créer un compte avec un *nom*, *email* et un *mot de passe*  
2. Rechercher dans une liste de cocktails par :  
    - Mots-clés  
    - Ingrédients  
    - Utilisateurs  
3. Ordonner cette recherche par :  
    - Date d'ajout (asc/desc)  
    - A-Z  
    - Plus liké  
4. Ajouter des recettes de cocktails  
5. Liker des recettes de cocktails  
6. Ajouter des recettes à sa liste *à faire*  
7. Liker des utilisateurs  

### Liste *à Faire*

La liste *à faire* est une liste de recettes stockée en local, avec une page dédiée pour cocher les recettes réalisées.

### Langue, Thèmes et Personnalisation

La page réglages permet de changer de langue et de thème.  
Les langues ne sont pas hardcodées et peuvent être ajoutées facilement.  
Le site propose 2 thèmes par défaut : clair et sombre, et permet de créer un thème personnalisé.

## Sur le code

### Cookies et LocalStorage

#### Cookies

Après avoir demandé l'autorisation sur ```index.php```, le site propose lors de l'inscription et de la connexion de se *souvenir de l'utilisateur*, grâce à des cookies.

#### LocalStorage

Le site utilise le LocalStorage pour la liste *à faire* et pour le thème sélectionné ainsi que les couleurs choisies en cas de thème personnalisé.

### Base de données

La base de données est créée si le fichier ```sql/database.sqlite``` n'existe pas.  
Elle contient :

#### Users

Stocke les informations des utilisateurs :  
- `id` : Identifiant unique  
- `username` : Nom d'utilisateur  
- `password` : Mot de passe (hashé)  
- `email` : Email de l'utilisateur  
- `remember_token` : Token pour la fonctionnalité "se souvenir de moi"  
- `created_at` : Date de création du compte  

#### Recipes

Contient les informations générales des recettes de cocktails :  
- `id` : Identifiant unique de la recette  
- `user_id` : Référence vers l'utilisateur créateur  
- `title` : Nom du cocktail  
- `icon` : Icône de la recette  
- `cover` : Image de couverture  
- `description` : Description du cocktail  
- `extra_info` : Informations supplémentaires  
- `created_at` : Date d'ajout de la recette  

#### Ingredients

Liste les ingrédients de chaque recette :  
- `id` : Identifiant unique  
- `recipe_id` : Référence vers la recette  
- `name` : Nom de l'ingrédient  
- `quantity` : Quantité nécessaire  
- `unit` : Unité de mesure  

#### Steps

Détaille les étapes de préparation :  
- `id` : Identifiant unique  
- `recipe_id` : Référence vers la recette  
- `step_order` : Ordre de l'étape  
- `content` : Description de l'étape  
- `image` : Image illustrant l'étape (optionnel)  

#### Recipe_likes

Gère les likes sur les recettes :  
- `user_id` et `recipe_id` forment la clé primaire  
- Relations avec les tables `users` et `recipes`  

#### User_likes

Enregistre les abonnements entre utilisateurs :  
- `id` : Identifiant unique  
- `user_id` : Utilisateur qui suit  
- `liked_user_id` : Utilisateur suivi  
