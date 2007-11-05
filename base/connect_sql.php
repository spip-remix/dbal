<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Utilitaires indispensables autour des serveurs SQL
//

// API d'appel aux bases de donnees:
// on charge le fichier config/$serveur ($serveur='connect' pour le principal)
// qui est cense initaliser la connexion en appelant spip_connect_db
// laquelle met dans la globale db_ok la description de la connexion
// On la memorise dans un tableau pour permettre plusieurs serveurs.
// A l'installation, il faut simuler l'existence de ce fichier

// http://doc.spip.org/@spip_connect
function spip_connect($serveur='', $version='') {
	global $connexions, $spip_sql_version;

	$index = $serveur ? $serveur : 0;
	if (!$version) $version = $spip_sql_version;
	if (isset($connexions[$index][$version])) return $connexions[$index];

	include_spip('base/abstract_sql');
	if (isset($_GET['var_profile'])) include_spip('public/debug');
	$install = (_request('exec') == 'install');

	// Premiere connexion ? 
	if (!($old = isset($connexions[$index]))) {
		$f = (!preg_match('/^\w*$/', $serveur))	? ''
		: (($serveur AND !$install) ?
			( _DIR_CONNECT. $serveur . '.php')
			: (_FILE_CONNECT ? _FILE_CONNECT
			   : ($install ? _FILE_CONNECT_TMP : '')));

		unset($GLOBALS['db_ok']);
		unset($GLOBALS['spip_connect_version']);
		if ($f AND is_readable($f)) include($f);
		if (!isset($GLOBALS['db_ok'])) {
		  // fera mieux la prochaine fois
			if ($install) return false; 
			spip_log("spip_connect: serveur $index mal defini dans '$f'.");
			// ne plus reessayer si ce n'est pas l'install
			return $connexions[$index]=false;
		}
		$connexions[$index] = $GLOBALS['db_ok'];
	}
	// la connexion a reussi ou etait deja faite.
	// chargement de la version du jeu de fonctions
	// si pas dans le fichier par defaut
	$type = $GLOBALS['db_ok']['type'];
	$jeu = 'spip_' . $type .'_functions_' . $version;
	if (!isset($GLOBALS[$jeu])) {
		if (!include_spip($type . '_' . $version, 'req'))
			spip_log("spip_connect: serveur $index version '$version' non defini par $jeu.");
		// ne plus reessayer 
		return $connexions[$index][$version] = array();
	}
	$connexions[$index][$version] = $GLOBALS[$jeu];
	if ($old) return $connexions[$index];

	$connexions[$index]['spip_connect_version'] = isset($GLOBALS['spip_connect_version']) ? $GLOBALS['spip_connect_version'] : 0;

	// initialisation de l'alphabet utilise dans les connexions SQL
	// si l'installation l'a determine.
	// Celui du serveur principal l'impose aux serveurs secondaires
	// s'ils le connaissent

	if (!$serveur) {
		$charset = spip_connect_main($GLOBALS[$jeu]);
		if (!$charset) {
			unset($connexions[$index]);
			spip_log("spip_connect: absence de charset");
			return false;
		}
	} else {
		$charset = isset($GLOBALS['meta']['charset_sql_connexion']) ?
		  $GLOBALS['meta']['charset_sql_connexion'] : 'utf8';
	}
	if ($charset != -1) {
		$f = $GLOBALS[$jeu]['set_charset'];
		if (function_exists($f))
			$f($charset, $serveur);
	}
	return $connexions[$index];
}

// Cette fonction ne doit etre appelee qu'a travers la fonction sql_serveur
// definie dans base/abstract_sql
// Elle existe en tant que gestionnaire de versions,
// connue seulement des convertisseurs automatiques

// http://doc.spip.org/@spip_connect_sql
function spip_connect_sql($version, $ins='', $serveur='', $cont=false) {
	$desc = spip_connect($serveur, $version);
	if (function_exists($f = @$desc[$version][$ins])) return $f;
	if ($ins)
		spip_log("Le serveur '$serveur' version $version n'a pas '$ins'");
	if ($cont) return $desc;
	include_spip('inc/minipres');
	echo minipres(_T('info_travaux_titre'), _T('titre_probleme_technique'));
	exit;
}

// Fonction appelee par le fichier cree dans config/ a l'instal'.
// Il contient un appel direct a cette fonction avec comme arguments
// les identifants de connexion.
// Si la connexion reussit, la globale db_ok memorise sa description.
// C'est un tableau egalement retourne en valeur, pour les appels a l'install'

// http://doc.spip.org/@spip_connect_db
function spip_connect_db($host, $port, $login, $pass, $db='', $type='mysql', $prefixe='', $ldap='') {
	global $db_ok;

	## TODO : mieux differencier les serveurs
	$f = _DIR_TMP . $type . 'out';

	if (@file_exists($f)
	AND (time() - @filemtime($f) < 30)
	AND !defined('_ECRIRE_INSTALL')) {
		return;
	}
	if (!$prefixe) 
		$prefixe = isset($GLOBALS['table_prefix'])
		? $GLOBALS['table_prefix'] : $db;
	$h = charger_fonction($type, 'req', true);
	if (!$h) {
		spip_log("les req�etes $type ne sont pas fournies");
		return;
	}
	  
	if ($g = $h($host, $port, $login, $pass, $db, $prefixe, $ldap)) {

		$g['type'] = $type;
		return $db_ok = $g;
	}
	// En cas d'indisponibilite du serveur, eviter de le bombarder
	if (!defined('_ECRIRE_INSTALL')) {
		@touch($f);
		$err = "Echec connexion $host $port $login $db";
		spip_log($err);
		spip_log($err, $type);
	}
}

// Premiere connexion au serveur principal: 
// retourner le charset donnee par la table principale
// mais verifier que le fichier de connexion n'est pas trop vieux
// Version courante = 0.7 (indication d'un LDAP comme 7e arg)
// La version 0.6 indique le prefixe comme 6e arg
// La version 0.5 indique le serveur comme 5e arg
//
// La version 0.0 (non numerotee) doit etre refaite par un admin
// les autres fonctionnent toujours, meme si :
// - la version 0.1 est moins performante que la 0.2
// - la 0.2 fait un include_ecrire('inc_db_mysql.php3').

// http://doc.spip.org/@spip_connect_main
function spip_connect_main($connexion)
{
	if ($GLOBALS['spip_connect_version']< 0.1 AND _DIR_RESTREINT){
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('upgrade', 'reinstall=oui', true));
	}

	if (!($f = $connexion['select'])) return false;
	if (!$r = $f('valeur','spip_meta', "nom='charset_sql_connexion'"))
		return false;
	if (!($f = $connexion['fetch'])) return false;
	$r = $f($r);
	return ($r['valeur'] ? $r['valeur'] : -1);
}

// http://doc.spip.org/@spip_connect_ldap
function spip_connect_ldap($serveur='') {
	$connexion = spip_connect($serveur);
	if ($connexion['ldap'] AND is_string($connexion['ldap'])) {
		include_once( _DIR_CONNECT . $connexion['ldap']);
		if ($GLOBALS['ldap_link'])
		  $connexion['ldap'] = array('link' => $GLOBALS['ldap_link'],
					'base' => $GLOBALS['ldap_base']);
	}
	return $connexion['ldap'];
}

// 1 interface de abstract_sql a demenager dans base/abstract_sql a terme

// http://doc.spip.org/@_q
function _q ($a) {
	return (is_int($a)) ? strval($a) : 
		(!is_array($a) ? ("'" . addslashes($a) . "'")
		 : join(",", array_map('_q', $a)));
}

// Nommage bizarre des tables d'objets
// http://doc.spip.org/@table_objet
function table_objet($type) {
	static $surnoms = array(
		'article' => 'articles',
		'auteur' => 'auteurs',
		'breve' => 'breves',
		'document' => 'documents',
		'doc' => 'documents', # pour les modeles
		'img' => 'documents',
		'emb' => 'documents',
		'forum' => 'forum', # hum
		'groupe_mots' => 'groupes_mots', # hum
		'groupe' => 'groupes_mots', # hum (EXPOSE)
		'message' => 'messages',
		'mot' => 'mots',
		'petition' => 'petitions',
		'rubrique' => 'rubriques',
		'signature' => 'signatures',
		'syndic' => 'syndic',
		'site' => 'syndic', # hum hum
		'syndic_article' => 'syndic_articles',
		'type_document' => 'types_documents', # hum
		'extension' => 'types_documents' # hum
	);
	return isset($surnoms[$type]) ? $surnoms[$type] : $type."s";
}

// http://doc.spip.org/@table_objet_sql
function table_objet_sql($type) {
	return 'spip_' . table_objet($type);
}

// http://doc.spip.org/@id_table_objet
function id_table_objet($type) {
	$type = preg_replace(',^spip_|s$,', '', $type);
	if ($type == 'forum')
		return 'id_forum';
	else if ($type == 'type')
		return 'extension';
	else {
		if (!$type) return;
		$t = table_objet($type);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($t);
		return @$desc['key']["PRIMARY KEY"];
	}
}

// Recuperer le nom de la table de jointure xxxx sur l'objet yyyy
// http://doc.spip.org/@table_jointure
function table_jointure($x, $y) {
	include_spip('public/interfaces');
	if ($table = $GLOBALS['tables_jointures'][table_objet_sql($y)][id_table_objet($x)]
	OR $table = $GLOBALS['tables_jointures'][table_objet_sql($x)][id_table_objet($y)])
		return $table;
}

// Pour compatibilite. Ne plus utiliser.
// http://doc.spip.org/@spip_query
function spip_query($query, $serveur='') {
	global $spip_sql_version;
	$f = spip_connect_sql($spip_sql_version, 'query', $serveur, true);
	return function_exists($f) ? $f($query, $serveur) : false;
}

?>