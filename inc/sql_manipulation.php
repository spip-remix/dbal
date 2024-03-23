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
 * Effectue une requête de selection
 *
 * Fonction de selection (SELECT), retournant la ressource interrogeable par sql_fetch.
 *
 * @api
 * @see sql_fetch()      Pour boucler sur les resultats de cette fonction
 *
 * @param array|string $select
 *     Liste des champs a recuperer (Select)
 * @param array|string $from
 *     Tables a consulter (From)
 * @param array|string $where
 *     Conditions a remplir (Where)
 * @param array|string $groupby
 *     critere de regroupement (Group by)
 * @param array|string $orderby
 *     Tableau de classement (Order By)
 * @param string $limit
 *     critere de limite (Limit)
 * @param string|array $having
 *     Tableau ou chaine des des post-conditions à remplir (Having)
 * @param string $serveur
 *     Le serveur sollicite (pour retrouver la connexion)
 * @param bool|string $option
 *     Peut avoir 3 valeurs :
 *
 *     - false -> ne pas l'exécuter mais la retourner,
 *     - continue -> ne pas echouer en cas de serveur sql indisponible,
 *     - true|array -> executer la requête.
 *     Le cas array est, pour une requete produite par le compilateur,
 *     un tableau donnnant le contexte afin d'indiquer le lieu de l'erreur au besoin
 *
 *
 * @return mixed
 *     Ressource SQL
 *
 *     - Ressource SQL pour sql_fetch, si la requete est correcte
 *     - false en cas d'erreur
 *     - Chaine contenant la requete avec $option=false
 *
 * Retourne false en cas d'erreur, apres l'avoir denoncee.
 * Les portages doivent retourner la requete elle-meme en cas d'erreur,
 * afin de disposer du texte brut.
 *
 */
function sql_select(
    $select = [],
    $from = [],
    $where = [],
    $groupby = [],
    $orderby = [],
    $limit = '',
    $having = [],
    $serveur = '',
    $option = true
) {
    $f = sql_serveur('select', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }

    $debug = (defined('_VAR_MODE') && _VAR_MODE == 'debug');
    if ($option !== false && !$debug) {
        $res = $f(
            $select,
            $from,
            $where,
            $groupby,
            $orderby,
            $limit,
            $having,
            $serveur,
            is_array($option) ? true : $option
        );
    } else {
        $query = $f($select, $from, $where, $groupby, $orderby, $limit, $having, $serveur, false);
        if (!$option) {
            return $query;
        }
        // le debug, c'est pour ce qui a ete produit par le compilateur
        if (isset($GLOBALS['debug']['aucasou'])) {
            [$table, $id,] = $GLOBALS['debug']['aucasou'];
            $nom = $GLOBALS['debug_objets']['courant'] . $id;
            $GLOBALS['debug_objets']['requete'][$nom] = $query;
        }
        $res = $f($select, $from, $where, $groupby, $orderby, $limit, $having, $serveur, true);
    }

    // en cas d'erreur
    if (!is_string($res)) {
        return $res;
    }
    // denoncer l'erreur SQL dans sa version brute
    spip_sql_erreur($serveur);
    // idem dans sa version squelette (prefixe des tables non substitue)
    $contexte_compil = sql_error_backtrace(true);
    erreur_squelette([sql_errno($serveur), sql_error($serveur), $res], $contexte_compil);

    return false;
}

/**
 * Insère une ligne dans une table
 *
 * @see sql_insertq()
 * @see sql_quote()
 * @note
 *   Cette fonction ne garantit pas une portabilité totale,
 *   et n'est là que pour faciliter des migrations de vieux scripts.
 *   Préférer sql_insertq.
 *
 * @param string $table
 *     Nom de la table SQL
 * @param string $noms
 *     Liste des colonnes impactées,
 * @param string $valeurs
 *     Liste des valeurs,
 * @param array $desc
 *     Tableau de description des colonnes de la table SQL utilisée
 *     (il sera calculé si nécessaire s'il n'est pas transmis).
 * @param string $serveur
 *     Nom du connecteur
 * @param bool|string $option
 *     Peut avoir 3 valeurs :
 *
 *     - false : ne pas l'exécuter mais la retourner,
 *     - true : exécuter la requête
 *     - 'continue' : ne pas échouer en cas de serveur sql indisponible
 *
 * @return bool|string
 *     - int|true identifiant de l'élément inséré (si possible), ou true, si réussite
 *     - texte de la requête si demandé,
 *     - False en cas d'erreur.
 */
function sql_insert($table, $noms, $valeurs, $desc = [], $serveur = '', $option = true)
{
    $f = sql_serveur('insert', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($table, $noms, $valeurs, $desc, $serveur, $option !== false);
    if ($r === false || $r === null) {
        spip_sql_erreur($serveur);
        $r = false;
    }

    return $r;
}

/**
 * Met à jour des enregistrements d'une table SQL
 *
 * Les valeurs ne sont pas échappées, ce qui permet de modifier une colonne
 * en utilisant la valeur d'une autre colonne ou une expression SQL.
 *
 * Il faut alors protéger avec sql_quote() manuellement les valeurs qui
 * en ont besoin.
 *
 * Dans les autres cas, préférer sql_updateq().
 *
 * @api
 * @see sql_updateq()
 *
 * @param string $table
 *     Nom de la table
 * @param array $exp
 *     Couples (colonne => valeur)
 * @param string|array $where
 *     Conditions a remplir (Where)
 * @param array $desc
 *     Tableau de description des colonnes de la table SQL utilisée
 *     (il sera calculé si nécessaire s'il n'est pas transmis).
 * @param string $serveur
 *     Nom de la connexion
 * @param bool|string $option
 *     Peut avoir 3 valeurs :
 *
 *     - false : ne pas l'exécuter mais la retourner,
 *     - true : exécuter la requête
 *     - 'continue' : ne pas échouer en cas de serveur sql indisponible
 * @return array|bool|string
 *     - string : texte de la requête si demandé
 *     - true si la requête a réussie, false sinon
 *     - array Tableau décrivant la requête et son temps d'exécution si var_profile est actif
 */
function sql_update($table, $exp, $where = '', $desc = [], $serveur = '', $option = true)
{
    $f = sql_serveur('update', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($table, $exp, $where, $desc, $serveur, $option !== false);
    if ($r === false) {
        spip_sql_erreur($serveur);
    }

    return $r;
}

/**
 * Supprime des enregistrements d'une table
 *
 * @example
 *     ```
 *     sql_delete('spip_articles', 'id_article='.sql_quote($id_article));
 *     ```
 *
 * @api
 * @param string $table
 *     Nom de la table SQL
 * @param string|array $where
 *     Conditions à vérifier
 * @param string $serveur
 *     Nom du connecteur
 * @param bool|string $option
 *     Peut avoir 3 valeurs :
 *
 *     - false : ne pas l'exécuter mais la retourner,
 *     - true : exécuter la requête
 *     - 'continue' : ne pas échouer en cas de serveur sql indisponible
 *
 * @return bool|string
 *     - int : nombre de suppressions réalisées,
 *     - texte de la requête si demandé,
 *     - false en cas d'erreur.
 */
function sql_delete($table, $where = '', $serveur = '', $option = true)
{
    $f = sql_serveur('delete', $serveur, $option === 'continue' || $option === false);
    if (!is_string($f) || !$f) {
        return false;
    }
    $r = $f($table, $where, $serveur, $option !== false);
    if ($r === false) {
        spip_sql_erreur($serveur);
    }

    return $r;
}
