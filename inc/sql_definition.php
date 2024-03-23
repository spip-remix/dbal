<?php

/**
 * SPIP, Système de publication pour l'internet
 *
 * Copyright © avec tendresse depuis 2001
 * Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James
 *
 * Ce programme est un logiciel libre distribué sous licence GNU/GPL.
 */

/**
 * Crée une table dans la base de données
 *
 * @api
 * @example
 *     ```
 *     sql_create("spip_tables",
 *       array(
 *           "id_table" => "bigint(20) NOT NULL default '0'",
 *           "colonne1"=> "varchar(3) NOT NULL default 'oui'",
 *           "colonne2"=> "text NOT NULL default ''"
 *        ),
 *        array(
 *           'PRIMARY KEY' => "id_table",
 *           'KEY colonne1' => "colonne1"
 *        )
 *     );
 *     ```
 *
 * @param string $nom
 *     Nom de la table
 * @param array $champs
 *     Couples (colonne => description)
 * @param array $cles
 *     Clé (nomdelaclef => champ)
 * @param bool $autoinc
 *     Si un champ est clef primaire est numérique alors la propriété
 *     d’autoincrémentation sera ajoutée
 * @param bool $temporary
 *     true pour créer une table temporaire (au sens SQL)
 * @param string $serveur
 *     Nom du connecteur
 * @param bool|string $option
 *     Peut avoir 3 valeurs :
 *
 *     - false : ne pas l'exécuter mais la retourner,
 *     - 'continue' : ne pas échouer en cas de serveur SQL indisponible,
 *     - true : exécuter la requete.
 * @return bool
 *     true si succès, false en cas d'echec
 */
function sql_create(
    $nom,
    $champs,
    $cles = [],
    $autoinc = false,
    $temporary = false,
    $serveur = '',
    $option = true
) {
    $f = sql_serveur('create', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($nom, $champs, $cles, $autoinc, $temporary, $serveur, $option !== false);
    if ($r === false) {
        spip_sql_erreur($serveur);
    }

    return $r;
}

/**
 * Modifie la structure de la base de données
 *
 * Effectue une opération ALTER.
 *
 * @example
 *     ```
 *     sql_alter('DROP COLUMN supprimer');
 *     ```
 *
 * @api
 * @param string $q
 *     La requête à exécuter (sans la préceder de 'ALTER ')
 * @param string $serveur
 *     Le serveur sollicite (pour retrouver la connexion)
 * @param bool|string $option
 *     Peut avoir 2 valeurs :
 *
 *     - true : exécuter la requete
 *     - 'continue' : ne pas échouer en cas de serveur sql indisponible
 * @return mixed
 *     2 possibilités :
 *
 *     - Incertain en cas d'exécution correcte de la requête
 *     - false en cas de serveur indiponible ou d'erreur
 *
 *     Ce retour n'est pas pertinent pour savoir si l'opération est correctement réalisée.
 */
function sql_alter($q, $serveur = '', $option = true)
{
    $f = sql_serveur('alter', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($q, $serveur, $option !== false);
    if ($r === false) {
        spip_sql_erreur($serveur);
    }

    return $r;
}

/**
 * Supprime une table SQL (structure et données).
 *
 * @api
 *
 * @param string $table Nom de la table
 * @param bool $exist true pour ajouter un test sur l'existence de la table
 * @param string $serveur Nom du connecteur
 * @param bool $option false : ne pas exécuter mais retourner laa requête, 'continue' : ne pas échouer en cas de serveur sql indisponible
 *
 * @return bool|string true en cas de succès, texte de la requête si demandé, false en cas d'erreur.
 */
function sql_drop(string $table, bool $exist = false, string $serveur = '', bool|string $option = true): bool|string
{
    $f = sql_serveur('drop_table', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($table, $exist, $serveur, $option !== false);
    if ($r === false) {
        spip_sql_erreur($serveur);
    }

    return $r;
}

/**
 * @see ::sql_drop()
 *
 * @deprecated 5.0
 */
function sql_drop_table($table, $exist = false, $serveur = '', $option = true)
{
    return sql_drop($table, $exist, $serveur, $option);
}
