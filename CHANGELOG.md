# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Composerisation & pluginisation.
- Description minimale d'un schéma de base de données (Schema)
- Description minimale d'une table de base de données (Table)
- Description minimale d'un champ de base de données (Field)
- Convertisseur de tableau de définition
- Factory de schéma
- Convertisseur des globales SPIP<=4
- ConnectionTrait, détecteur d'exensions PHP
- ConnectionInterface, File, Socket, TCP

### Removed

- `base/index.php`
- `base/serial.php`
- `base/auxiliaires.php`

### Changed

- Suppresion d'un `@todo` vieux de 11 ans.

    ```diff
    - include_spip('base/auxiliaires');
    - include_spip('base/serial');
    - include_spip('base/objets');
    + lister_tables_objets_sql();
    ```

- Réorganisation des fonctions d'abstraction
  - `sql_select()`, `sql_insert()`, `sql_update()` et `sql_delete()` dans `inc/sql_manipulation`
  - `sql_create()`, `sql_alter()` et `sql_drop()` dans `inc/sql_definition`
- Réorganisation de la séquance de démarrage du composant
  - `bootstrap/connect_sql.php` et `bootstrap/objets.php`

### Deprecated

- `sql_drop_table()`. Utiliser `sql_drop()`.
