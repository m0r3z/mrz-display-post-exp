# MRZ Display Post Exp

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
- Shortcode : `[mrzdpe id="X"]` (filtre forçable via `filter_taxonomy` / `filter_term`).

## Prérequis

- WordPress 6.3+, PHP 7.4+.
- Advanced Custom Fields (Pro recommandé).

## Placeholders du template

| Placeholder | Sortie |
|---|---|
| `{post_title}` | Titre |
| `{post_url}` | Permalien |
| `{post_excerpt}` | Extrait complet |
| `{post_excerpt:N}` | Extrait tronqué à N mots (ex. `{post_excerpt:25}`) |
| `{post_thumbnail}` | Balise `<img>` (taille medium) |
| `{post_thumbnail_url}` | URL de la miniature |
| `{post_id}` | ID du post |
| `{post_date}` | Date de publication (format de date du site) |
| `{%nom_champ_acf%}` | Valeur ACF (échappement selon le type) |
| `{acf_date:champ}` | Date ACF découpée en spans `mrz-dpe-day` / `mrz-dpe-month` / `mrz-dpe-year`. Mois numérique par défaut ; `{acf_date:champ:F}` = nom complet, `:M` = abrégé, `:n` = numéro sans zéro |
| `{taxonomy:slug}` | Tous les termes, chacun dans un `<span class="mrz-dpe-term">` (aucun séparateur imposé, à styliser en CSS) |
| `{taxonomy:slug:first}` | Nom du premier terme (texte brut) |
| `{taxonomy:slug:slug}` | Slug du premier terme (ex. modificateur de classe CSS) |

Conditionnels : `{#if %champ%}…{/if}` et `{#if post_title}…{/if}`.

## Confidentialité

Aucun appel HTTP externe, aucune ressource distante, aucune télémétrie.
