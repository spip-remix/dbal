# spip-remix/dbal

Spip Database Abstraction Layer

## TODO

- [x] : Import fichiers historiques
- [x] : Composerisation
- [x] : Pluginisation
- [ ] : Documentation
- [/] : Nettoyage fichiers
- [ ] : Producteurs de reqête SQL (
    Builder Définition: ALTER, CREATE, DROP, ...,
    Administration: SET, SHOW, ...,
    Manipulation: SELECT, UPDATE, INSERT, DELETE, ...)
- [/] : Connecteurs "réseaux" TCP/Socket UNIX/Fichier/Autres
- [ ] : Exécuteurs de requêtes SQL (Connector), transactions
- [y] : Descripteurs de schéma (~~Schema~~, ~~Table~~, ~~Field~~, Constraint, autres paramètres, ...)
- [x] : Détecteur d'extensions PHP
- [ ] : Détecteur de versions serveurs, extensions & clients lib-c
- [ ] : Vérifier autres extensions PHP utiles & config composer
- [x] : Convertisseurs (Tableaux de définition, Formats historiques)
- [/] : Gestion des exceptions PHP
- [ ] : Spécificités des serveurs
- [ ] : Versions de Schéma et Migrateurs
- [ ] : Seeds & Seeders
- [ ] : Backups & Restaurations
- [ ] : Autres (Réparations, Optimisations)
- [ ] : ORM
- [ ] : Description du CMS minimal

## Nettoyage de fichiers

- [X] : Suppression de `ecrire/base/index.php`.
- [X] : Suppression de `ecrire/base/serial.php` et `ecrire/base/auxiliaires.php`.
- [X] : Changement de coding standards
  - au profit de [PER-CS](https://www.php-fig.org/per/coding-style/)
  - et de l'outil [php-cs-fixer](https://cs.symfony.com/)
  - phpstan, level max
- [ ] : Type Hinting et Return Type Hinting
- [ ] : Isolation du code d'affichage à l'écran (appels à minipres, ...)
- [X] : Suppression du code :

```php
/**
 * @package ...
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
```

- [X] : Allègement du commentaire d'en-tête
- [X] : Suppression du code commenté
- [ ] : Dossier `definitions/` pour les tableaux déscripteurs de schéma
- [ ] : Dossier `inc/` pour l'@api PHP legacy

## Connecteurs "réseaux" TCP/Socket UNIX/Fichier
