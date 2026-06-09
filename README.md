# MRZ Display Post

Plugin WordPress minimaliste pour **afficher des posts et custom posts avec leurs champs ACF** en **liste**, **grille** ou **slider**. Chaque item est rendu depuis un **template HTML personnalisable** à placeholders (champs ACF, taxonomies, titre, miniature…), avec **filtres et recherche côté client** activables. Tout se configure depuis l'admin ; l'apparence s'adapte au site via le CSS du thème.

- Repo : https://github.com/m0r3z/mrz-display-post-exp
- Auteur : [Morez.co](https://morez.co)
- Licence : GPLv3 or later

## Fonctionnalités

- Blocs d'affichage multiples via un Custom Post Type dédié — chacun a sa config et son shortcode.
- 3 formats : **liste**, **grille**, **slider** natif CSS (sans dépendance).
- Template HTML d'item personnalisable avec placeholders et conditionnels.
- Filtres client-side par taxonomie et champ ACF (dropdown / radio / checkbox, logique OU/ET).
- Recherche texte client-side sur le titre (et champs ACF texte choisis).
- Synchronisation optionnelle des filtres dans l'URL (liens partageables).
- Mise en page responsive ; filtres repliables sur mobile.
- Shortcode : `[mrz_display_post_exp id="X"]` (filtre forçable via `filter_taxonomy` / `filter_term`).

## Prérequis

- WordPress 6.3+, PHP 7.4+.
- Advanced Custom Fields (Pro recommandé).

## Placeholders du template

| Placeholder | Sortie |
|---|---|
| `{post_title}` | Titre |
| `{post_url}` | Permalien |
| `{post_excerpt}` | Extrait |
| `{post_thumbnail}` | Balise `<img>` (taille medium) |
| `{post_thumbnail_url}` | URL de la miniature |
| `{post_id}` | ID du post |
| `{%nom_champ_acf%}` | Valeur ACF (échappement selon le type) |
| `{taxonomy:slug}` | Termes (séparés par virgule) |
| `{taxonomy:slug:first}` | Premier terme |

Conditionnels : `{#if %champ%}…{/if}` et `{#if post_title}…{/if}`.

## Confidentialité

Aucun appel HTTP externe, aucune ressource distante, aucune télémétrie.
