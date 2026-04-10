# CSS Dependencies and Page Style Responsibilities

## Vue d'ensemble

Ce document explique comment les pages PHP de `ShopKamer` associent les fichiers CSS et quel rôle joue chaque feuille de style.

### Principes généraux

- `assets/css/styles.css` est la feuille de style globale.
- Chaque page PHP charge d'abord `styles.css` puis une feuille CSS spécifique à la page.
- L'ordre de chargement est important : la feuille spécifique peut étendre ou redéfinir des règles globales.
- `includes/header.php` et `includes/footer.php` sont présents sur la plupart des pages, mais n'ont pas leur propre CSS dédié ; ils utilisent les styles globaux.

---

## Pages et feuilles CSS

### `index.php`

- Charge :
  - `assets/css/styles.css`
  - `assets/css/index.css`
- Rôle : page d'accueil avec le hero, les catégories et les produits en vedette.
- Impact des modifications :
  - `styles.css` affecte l'apparence globale, boutons, sections, en-têtes et typographie.
  - `index.css` affecte uniquement le design de la page d'accueil : hero, cartes de catégorie, grille de produits mis en avant.

### `catalogue.php`

- Charge :
  - `assets/css/styles.css`
  - `assets/css/catalogue.css`
- Rôle : page catalogue, filtres de catégorie et cartes produits.
- Impact des modifications :
  - `styles.css` modifie l'apparence générale (boutons, sections, texte commun).
  - `catalogue.css` modifie uniquement le comportement des filtres, la grille de produits, les cartes et les boutons d'ajout.

### `panier.php`

- Charge :
  - `assets/css/styles.css`
  - `assets/css/panier.css`
- Rôle : page panier avec le tableau des articles, le résumé et les totaux.
- Impact des modifications :
  - `styles.css` reste responsable de la base visuelle.
  - `panier.css` est responsable du style du tableau, du bloc de synthèse du panier, des boutons de suppression et des champs de quantité.

### `login.php`

- Charge :
  - `assets/css/styles.css`
  - `assets/css/auth.css`
- Rôle : page d'authentification avec formulaire de connexion.
- Impact des modifications :
  - `styles.css` fournit les styles de base et des éléments partagés.
  - `auth.css` gère le container de connexion, le formulaire, les champs, le bouton de connexion et le comportement responsive spécifique.

---

## Description des feuilles CSS

### `assets/css/styles.css`

Rôle : feuille globale.

- Reset de base et `box-sizing`.
- Typographie, couleurs, arrière-plan global.
- Styles de `header`, `main`, `footer`.
- Styles partagés pour :
  - `.btn`, `.btn-secondary`
  - `.section-title`
  - `.card`, `section`
  - `footer`
- Responsive de base pour les petits écrans.

### `assets/css/index.css`

Rôle : styles spécifiques à la page d'accueil.

- Hero principal (`.hero`, `.hero h1`, `.hero p`).
- Grille de catégories (`.categories-grid`, `.category-card`).
- Grille de produits vedette (`.featured-grid`, `.featured-card`, `.featured-card-footer`).
- Section "Pourquoi ShopKamer".
- Ajustement responsive pour la page d'accueil.

### `assets/css/catalogue.css`

Rôle : styles spécifiques à la page catalogue.

- Barre de filtres (`.filters`, `.filter-btn`).
- Grille de produits (`.product-grid`, `.product-card`).
- Images produit et effet hover.
- Pied de carte et bouton d'ajout (`.add-btn`).
- Boutons/icônes utilitaires partagés.

### `assets/css/panier.css`

Rôle : styles spécifiques à la page panier.

- Bloc de résumé du panier (`.panier-summary`, `.panier-summary-header`).
- Tableau du panier (`.panier-table`, `th`, `td`).
- Champ quantité (`.qty-input`).
- Bouton supprimer (`.btn-delete`).
- Footer du panier (`.panier-footer`, `.panier-total-amount`).
- Responsive spécifique au tableau.

### `assets/css/auth.css`

Rôle : styles spécifiques à la page de connexion.

- Container de la page de connexion (`.login-container`).
- Formulaire et champs (`.login-form`, `.form-group`, `input`).
- Styles du bouton de connexion.
- Liens supplémentaires et animation d'entrée.
- Responsive mobile pour le formulaire.

---

## Règles de bonne pratique pour ajouter une nouvelle page ou un nouveau CSS

1. Toujours charger `assets/css/styles.css` en premier.
2. Ajouter ensuite une feuille CSS spécifique à la page.
3. Mettre dans `styles.css` uniquement les éléments partagés ou réutilisables : boutons, structure générale, header/footer, typographie.
4. Mettre dans le CSS spécifique à une page les règles propres à la mise en page et aux composants uniques à cette page.
5. Si une nouvelle page réutilise des composants existants, essayer d'étendre les classes globales plutôt que de dupliquer les styles.
6. Vérifier l'ordre de chargement : une règle définie dans `styles.css` peut être surchargée par une règle identique dans la feuille spécifique.

---

## Influence des modifications

- Modification de `styles.css` : impact sur toutes les pages qui l'utilisent. C'est le meilleur endroit pour changer la palette de couleurs globale, les boutons réutilisés ou les règles de structure commune.
- Modification d'un CSS spécifique (`index.css`, `catalogue.css`, `panier.css`, `auth.css`) : impact limité à la page qui l'include. C'est le bon endroit pour des ajustements locaux ou des composants uniques.
- Ajout de nouvelles classes globales : les pages existantes peuvent en bénéficier sans modification, à condition que leurs éléments utilisent ces classes.
- Si un style attendu n'apparaît pas sur une page, vérifier d'abord :
  - que le CSS global est bien chargé,
  - que la page charge sa feuille CSS dédiée,
  - que le sélecteur n'est pas écrasé par une règle plus spécifique.
