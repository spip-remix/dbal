<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class SqlSchemaTableTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		\find_in_path('base/abstract_sql.php', '', true);
	}

	#[DataProvider('providerTablesData')]
	public function testDropTablesSetup($table, $desc, $data): void
	{
		$this->assertTrue(sql_drop($table, true));
	}

	#[Depends('testDropTablesSetup')]
	#[DataProvider('providerTablesData')]
	public function testCreateTables($table, $desc, $data): void
	{
		$this->assertTrue(sql_create($table, $desc['field'], $desc['key']));
	}

	/**
	 * Creation/suppression/analyse de tables dans la base de donnee
	 *
	 * Permet de verifier que
	 * - tous les champs sont correctement ajoutes
	 * - que les PRIMARY sont pris en compte
	 * - que les KEY sont prises en compte
	 */
	#[Depends('testCreateTables')]
	#[DataProvider('providerTablesData')]
	public function testShowTable($table, $desc, $data)
	{
		// lire la structure de la table
		// la structure doit avoir le meme nombre de champs et de cle
		// attention : la primary key DOIT etre dans les cle aussi
		$_desc = sql_showtable($table);
		$this->assertCount(count($desc['field']), $_desc['field']);
		$this->assertCount($desc['nb_key_attendues'], $_desc['key']);
	}

	#[Depends('testCreateTables')]
	#[DataProvider('providerTablesData')]
	public function testInsertData($table, $desc, $data)
	{
		$this->assertNotFalse(sql_insertq_multi($table, $data));
		$this->assertEquals(count($data), sql_countsel($table));
	}

	/**
	 * Teste que le champ "maj" s'actualise bien sur les update
	 * ainsi que les autres champs !
	 *
	 * utilise sql_quote, sql_getfetsel, sql_update et sql_updateq.
	 */
	#[Depends('testInsertData')]
	public function testMajTimestamp()
	{
		$table = 'spip_test_tintin';
		$where1 = 'id_tintin=' . sql_quote(1);
		$where2 = 'id_tintin=' . sql_quote(2);

		// lecture du timestamp actuel
		$maj1 = sql_getfetsel('maj', $table, $where1);
		$this->assertNotEmpty($maj1, "Le champ 'maj' n'a vraisemblablement pas recu de timestamp à l'insertion");

		$maj2 = sql_getfetsel('maj', $table, $where2);
		$this->assertNotEmpty($maj2, "Le champ 'maj' n'a vraisemblablement pas recu de timestamp à l'insertion");

		// 1s de plus, sinon le timestamp ne change pas !
		sleep(1);

		// update
		$texte = 'nouveau texte';
		sql_update($table, [
			'un_texte' => sql_quote($texte),
		], $where1);

		// comparaison timastamp
		$maj_update = sql_getfetsel('maj', $table, $where1);
		$this->assertNotEmpty($maj_update, "Le champ 'maj' est vide à l’update");
		$this->assertNotFalse(strtotime($maj_update), "Le champ 'maj' est incorrect à l’update");
		$this->assertNotEquals($maj1, $maj_update, "Le champ 'maj' n'a vraisemblablement pas été mis a jour lors de l'update");

		// comparaison texte
		$texte_update = sql_getfetsel('un_texte', $table, $where1);
		$this->assertNotEmpty($texte_update, "Le champ 'un_texte' est vide à l’update");
		$this->assertEquals($texte, $texte_update, "Le champ 'un_texte' n'est pas correctement rempli a l'update");

		// idem avec updateq
		$texte = 'encore un nouveau texte';
		sql_updateq($table, [
			'un_texte' => $texte,
		], $where2);

		// comparaison timastamp
		$maj_updateq = sql_getfetsel('maj', $table, $where2);
		$this->assertNotEmpty($maj_updateq, "Le champ 'maj' est vide à l’updateq");
		$this->assertNotFalse(strtotime($maj_updateq), "Le champ 'maj' est incorrect à l’updateq");
		$this->assertNotEquals($maj1, $maj_updateq, "Le champ 'maj' n'a vraisemblablement pas été mis a jour lors de l'updateq");

		// comparaison texte
		$texte_updateq = sql_getfetsel('un_texte', $table, $where2);
		$this->assertNotEmpty($texte_updateq, "Le champ 'un_texte' est vide à l’updateq");
		$this->assertEquals($texte, $texte_updateq, "Le champ 'un_texte' n'est pas correctement rempli a l'updateq");
	}

	/**
	 * Selections diverses selon criteres
	 */
	#[Depends('testInsertData')]
	public function testSelections()
	{
		$data = $this->providerTablesData()['tintin'][2];

		$res = sql_select('*', 'spip_test_tintin');
		$this->assertNotFalse($res);
		$this->assertEquals(count($data), sql_count($res), 'sql_count() ne renvoie pas la valeur attendue');

		// selection float
		$res = sql_select('*', 'spip_test_tintin', ['un_double>' . sql_quote(3)]);
		$n = count(array_filter($data, fn ($entry) => $entry['un_double'] > 3));
		$this->assertEquals($n, sql_count($res), 'sql_count() ne renvoie pas la valeur attendue sur un float');

		// selection REGEXP
		$res = sql_select('*', 'spip_test_tintin', ['un_varchar REGEXP ' . sql_quote('^De')]);
		$n = count(array_filter($data, fn ($entry) => str_starts_with($entry['un_varchar'], 'De')));
		$this->assertEquals($n, sql_count($res), 'sql_count() ne renvoie pas la valeur attendue sur une REGEXP');

		// selection LIKE
		$res = sql_select('*', 'spip_test_tintin', ['un_varchar LIKE ' . sql_quote('De%')]);
		$this->assertEquals($n, sql_count($res), 'sql_count() ne renvoie pas la valeur attendue sur un LIKE');

		// selection array(champs)
		$res = sql_fetsel(['id_tintin', 'un_varchar'], 'spip_test_tintin');
		$this->assertArrayHasKey('id_tintin', $res);
		$this->assertArrayHasKey('un_varchar', $res);

		// selection array(champs=>alias)
		$res = sql_fetsel(['id_tintin AS id', 'un_varchar AS vchar'], 'spip_test_tintin');
		$this->assertArrayHasKey('id', $res);
		$this->assertArrayHasKey('vchar', $res);
	}


	#[Depends('testInsertData')]
	public function testSelectionsMulti()
	{
		$data = $this->providerTablesData()['milou'][2];

		// selection avec sql_multi
		$res = sql_select(['id_milou', sql_multi('grrrr', 'fr')], 'spip_test_milou', orderby: 'multi');
		$this->assertNotFalse($res);
		$this->assertEquals(count($data), sql_count($res), 'sql_multi mal interprété');
		$this->assertEquals(3, sql_fetch($res)['id_milou'], 'sql_multi order by multi raté');
		$this->assertEquals(2, sql_fetch($res)['id_milou'], 'sql_multi order by multi raté');
		$this->assertEquals(1, sql_fetch($res)['id_milou'], 'sql_multi order by multi raté');

		// le bon texte avec multi
		foreach ([
			'fr' => 'Crac',
			'en' => 'Krack',
		] as $lg => $res) {
			$multi = sql_getfetsel(sql_multi('grrrr', $lg), 'spip_test_milou', 'id_milou=' . sql_quote(2));
			$this->assertEquals($res, $multi, 'sql_multi mal rendu');
		}

		// le bon texte avec multi et accents
		foreach ([
			'fr' => 'Aérien',
			'en' => 'Aérieny',
		] as $lg => $res) {
			$multi = sql_getfetsel(sql_multi('alcool', $lg), 'spip_test_haddock', 'id_haddock=' . sql_quote(2));
			$this->assertEquals($res, $multi, 'sql_multi avec accents, mal rendu');
		}

		// le bon texte avec multi et debut et fin de chaine
		foreach ([
			'fr' => 'Un début de chaine : Vinasse, et [la fin]',
			'en' => 'Un début de chaine : Vinassy, et [la fin]',
			'de' => 'Un début de chaine : Vinasse, et [la fin]',
		] as $lg => $res) {
			$multi = sql_getfetsel(sql_multi('alcool', $lg), 'spip_test_haddock', 'id_haddock=' . sql_quote(4));
			$this->assertEquals($res, $multi, 'sql_multi avec crochets, mal rendu');
		}
	}



	/**
	 * Selections diverses entre plusieurs tables
	 */
	#[Depends('testInsertData')]
	public function testSelectionsEntreTable()
	{
		// selection 2 tables
		// ! nombre en dur !
		$res = sql_select(
			['spip_test_tintin.id_tintin', 'spip_test_milou.id_milou'],
			['spip_test_tintin', 'spip_test_milou'],
			['spip_test_milou.id_tintin=spip_test_tintin.id_tintin']
		);
		$this->assertEquals(3, sql_count($res), 'Echec sélection');

		// selection 2 tables avec alias =>
		// ! nombre en dur !
		$res = sql_select(
			['a.id_tintin AS x', 'b.id_milou AS y'],
			[
				'a' => 'spip_test_tintin',
				'b' => 'spip_test_milou',
			],
			['a.id_tintin=b.id_tintin']
		);
		$this->assertEquals(3, sql_count($res), 'Echec sélection avec alias de colonnes et tables');

		// selection 2 tables avec alias AS
		// ! nombre en dur !
		$res = sql_select(
			['a.id_tintin AS x', 'b.id_milou AS y'],
			['spip_test_tintin AS a', 'spip_test_milou AS b'],
			['a.id_tintin=b.id_tintin']
		);
		$this->assertEquals(3, sql_count($res));

		// selection 2 tables avec INNER JOIN + ON
		// ! nombre en dur !
		$res = sql_select(
			['a.id_tintin AS x', 'b.id_milou AS y'],
			['spip_test_tintin AS a INNER JOIN spip_test_milou AS b ON (a.id_tintin=b.id_tintin)']
		);
		$this->assertEquals(3, sql_count($res), 'Echec sélection avec INNER JOIN + ON');

		// selection 2 tables avec LEFT JOIN + ON
		// ! nombre en dur !
		$res = sql_select(
			['a.id_tintin AS x', 'b.id_milou AS y'],
			['spip_test_tintin AS a LEFT JOIN spip_test_milou AS b ON (a.id_tintin=b.id_tintin)']
		);
		$this->assertEquals(4, sql_count($res), 'Echec sélection avec LEFT JOIN + ON');


		// selection 2 tables avec jointure INNER JOIN + USING
		// ! nombre en dur !
		// SQLite 2 se plante : il ne connait pas USING (enleve de la requete,
		// et du coup ne fait pas correctement la jointure)
		$res = sql_select(
			['a.id_tintin AS x', 'b.id_milou AS y'],
			['spip_test_tintin AS a INNER JOIN spip_test_milou AS b USING (id_tintin)']
		);
		$this->assertEquals(3, sql_count($res), 'Echec sélection avec INNER JOIN + USING');
	}


	/**
	 * Selections mathematiques
	 */
	#[Depends('testInsertData')]
	function testMathFunctions()
	{
		foreach ([
			'COUNT' => 3,
			'SUM' => 9000,
			'AVG' => 3000,
		] as $func => $expected) {
			$nb = sql_getfetsel("{$func}(un_int) AS nb", ['spip_test_tintin']);
			$this->assertEquals($expected, $nb, "Selection {$func} en echec");
		}

		foreach ([
			'EXP(0)' => exp(0),
			'ROUND(3.56)' => round(3.56),
			'ROUND(3.5684,2)' => round(3.5684, 2),
			'SQRT(9)' => 3,
			//'1/2'=>(0), // Le standard SQL : entier divise par entier = division entiere (pas trouve la reference)
			'1.0/2' => (1 / 2), // Le standart SQL : reel divise par entier = reel
			//'4/3'=>1,
			'ROUND(4.0/3,2)' => round(4 / 3, 2),
			'1.5/2' => (1.5 / 2),
			'2.0/2' => (2.0 / 2),
			'2/2' => (2 / 2),
			'md5(8)' => md5('8'),
			'md5(' . sql_quote('a') . ')' => md5('a'),
		] as $func => $expected) {
			$nb = sql_getfetsel("{$func} AS nb", ['spip_test_tintin'], ['id_tintin=' . sql_quote(1)]);
			$this->assertEquals($expected, $nb, "Selection {$func} en echec");
		}
	}

	/**
	 * Selections mathematiques
	 */
	#[Depends('testInsertData')]

	function testStringFunctions()
	{
		foreach ([
			'CONCAT(' . sql_quote('cou') . ',' . sql_quote('cou') . ')' => 'coucou',
			'CONCAT(' . sql_quote('cou,') . ',' . sql_quote('cou') . ')' => 'cou,cou',
		] as $func => $expected) {
			$nb = sql_getfetsel("{$func} AS nb", ['spip_test_tintin'], ['id_tintin=' . sql_quote(1)]);
			$this->assertEquals($expected, $nb, "Selection {$func} en echec");
		}
	}

	/**
	 * retours des fonctions d'erreurs lors d'une requete
	 */
	#[Depends('testCreateTables')]

	function testErrorFunctions()
	{
		// requete sans erreur
		sql_select('*', 'spip_test_tintin');
		$this->assertEquals('', sql_error(), 'sql_error() non vide lors d’une requete sans erreur');
		$this->assertEquals(0, sql_errno(), 'sql_errno() ne retourne pas 0 lors d’une requete sans erreur');

		// requete en erreur
		sql_select('*', 'spip_test_toto');
		$this->assertNotEquals('', sql_error(), 'sql_error() vide lors d’une requete en erreur');
		$this->assertNotEquals(0, sql_errno(), 'sql_errno() retourne 0 lors d’une requete en erreur');
	}

	/**
	 * Update de data
	 */
	#[Depends('testInsertData')]
	public function testUpdateData()
	{
		// ajouter un champ
		$nb = sql_getfetsel('un_bigint', 'spip_test_tintin', 'id_tintin=' . sql_quote(1));
		sql_update('spip_test_tintin', [
			'un_bigint' => 'un_bigint+2',
		]);
		$nb2 = sql_getfetsel('un_bigint', 'spip_test_tintin', 'id_tintin=' . sql_quote(1));
		$this->assertEquals($nb + 2, $nb2, 'sql_update n’a pas fait l’adition !');
	}

	/**
	 * Delete de data
	 */
	#[Depends('testInsertData')]

	public function test_delete_data()
	{
		$nb = sql_countsel('spip_test_tintin');
		// supprimer une ligne
		sql_delete('spip_test_tintin', 'id_tintin=' . sql_quote(1));
		$this->assertEquals($nb - 1, sql_countsel('spip_test_tintin'), "sql_delete n’a pas supprimé la ligne");

		// supprimer tout
		sql_delete('spip_test_tintin');
		$this->assertEquals(0, sql_countsel('spip_test_tintin'), "sql_delete n’a pas vidé la table");
	}

	/**
	 * Alter colonne
	 */
	#[Depends('testCreateTables')]
	function testAlterColumns()
	{
		$table = 'spip_test_tintin';

		// supprimer une colonne
		sql_alter("TABLE {$table} DROP COLUMN un_bigint");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP COLUMN (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('un_bigint', $desc['field']);
		$this->assertArrayHasKey('un_smallint', $desc['field']);

		// supprimer une colonne (sans COLUMN)
		sql_alter("TABLE {$table} DROP un_smallint");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP sans COLUMN (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('un_smallint', $desc['field']);

		// renommer une colonne
		sql_alter("TABLE {$table} CHANGE un_varchar deux_varchars VARCHAR(30) NOT NULL DEFAULT ''");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate CHANGE (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('deux_varchars', $desc['field']);
		$this->assertArrayNotHasKey('un_varchar', $desc['field']);

		// changer le type d'une colonne
		$table = 'spip_test_milou';
		sql_alter("TABLE {$table} MODIFY schtroumf TEXT NOT NULL DEFAULT ''");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate MODIFY (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('schtroumf', $desc['field'], 'sql_alter rate MODIFY varchar en text');
		$this->assertStringContainsStringIgnoringCase('TEXT', $desc['field']['schtroumf'], 'sql_alter rate MODIFY varchar en text');

		// ajouter des colonnes
		sql_alter("TABLE {$table} ADD COLUMN houba BIGINT(21) NOT NULL DEFAULT '0'");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD COLUMN (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('houba', $desc['field'], 'sql_alter rate ADD COLUMN');
		$this->assertStringContainsStringIgnoringCase('INT', $desc['field']['houba'], 'sql_alter rate ADD COLUMN');

		// ajouter des colonnes avec "AFTER"
		sql_alter("TABLE {$table} ADD COLUMN hop BIGINT(21) NOT NULL DEFAULT '0' AFTER id_tintin");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD COLUMN avec AFTER (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('hop', $desc['field'], 'sql_alter rate ADD COLUMN avec AFTER');
		$this->assertStringContainsStringIgnoringCase('INT', $desc['field']['hop'], 'sql_alter rate ADD COLUMN avec AFTER');
	}


	/**
	 * Renomme table
	 */
	#[Depends('testCreateTables')]

	public function testAlterRenameTable()
	{

		$table_before = 'spip_test_tintin';
		$table_after = 'spip_test_castafiore';
		sql_drop($table_after, true);
		$this->assertEmpty(sql_showtable($table_after));
		$this->assertIsArray(sql_showtable($table_before));

		// renommer une table
		sql_alter("TABLE {$table_before} RENAME {$table_after}");
		$this->assertEmpty(sql_showtable($table_before));
		$this->assertIsArray(sql_showtable($table_after));

		sql_alter("TABLE {$table_after} RENAME {$table_before}");
		$this->assertEmpty(sql_showtable($table_after));
		$this->assertIsArray(sql_showtable($table_before));
	}


	/**
	 * pointer l'index
	 */
	#[Depends('testCreateTables')]

	public function testAlterIndex()
	{
		$table = 'spip_test_milou';

		// supprimer un index
		sql_alter("TABLE {$table} DROP INDEX sons");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP INDEX sons (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('KEY sons', $desc['key'], 'sql_alter rate DROP INDEX sons');

		// ajouter un index simple
		sql_alter("TABLE {$table} ADD INDEX (wouaf)");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD INDEX (wouaf) (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('KEY wouaf', $desc['key'], 'sql_alter rate ADD INDEX (wouaf)');

		// ajouter un index nomme
		sql_alter("TABLE {$table} ADD INDEX pluie (grrrr)");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD INDEX pluie (grrrr) (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('KEY pluie', $desc['key'], 'sql_alter rate ADD INDEX pluie (grrrr)');

		// supprimer un index
		sql_alter("TABLE {$table} DROP INDEX pluie");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP INDEX pluie (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('KEY pluie', $desc['key'], 'sql_alter rate DROP INDEX pluie');

		// ajouter un index nomme double
		sql_alter("TABLE {$table} ADD INDEX dring (grrrr, wouaf)");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD INDEX dring (grrrr, wouaf) (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('KEY dring', $desc['key'], 'sql_alter rate ADD INDEX dring (grrrr, wouaf)');
	}


	/**
	 * dezinguer la primary
	 */
	#[Depends('testCreateTables')]

	public function testAlterPrimary()
	{
		$table = 'spip_test_kirikou';
		sql_drop($table, true);

		// creer une table pour jouer
		sql_create(
			$table,
			[
				'un' => 'INTEGER NOT NULL',
				'deux' => 'INTEGER NOT NULL',
				'trois' => 'INTEGER NOT NULL',
			],
			[
				'PRIMARY KEY' => 'un',
			]
		);

		// supprimer une primary
		$desc = sql_showtable($table);
		sql_alter("TABLE {$table} DROP PRIMARY KEY");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP PRIMARY KEY (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('PRIMARY KEY', $desc['key'], 'sql_alter rate DROP PRIMARY KEY');

		// ajouter une primary
		$desc = sql_showtable($table);
		sql_alter("TABLE {$table} ADD PRIMARY KEY (deux, trois)");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD PRIMARY KEY (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('PRIMARY KEY', $desc['key'], 'sql_alter rate ADD PRIMARY KEY');

		sql_drop($table, true);
	}

	/**
	 * Alter colonne
	 */
	#[Depends('testAlterColumns')]
	#[Depends('testAlterIndex')]

	function testAlterMultiple()
	{
		$table = 'spip_test_milou';

		// supprimer des colonnes
		sql_alter("TABLE {$table} DROP INDEX dring, DROP COLUMN wouaf, DROP COLUMN grrrr");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate DROP multiples (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayNotHasKey('waouf', $desc['field'], 'sql_alter rate DROP multiples');
		$this->assertArrayNotHasKey('grrrr', $desc['field'], 'sql_alter rate DROP multiples');
		$this->assertArrayNotHasKey('KEY dring', $desc['key'], 'sql_alter rate DROP multiples');

		// ajouter des colonnes
		sql_alter("TABLE {$table} ADD COLUMN a INT, ADD COLUMN b INT, ADD COLUMN c INT, ADD INDEX abc (a,b,c)");
		$desc = sql_showtable($table);
		$this->assertIsArray($desc, 'sql_alter rate ADD multiples (plus de table ou sql_showtable en erreur?)');
		$this->assertArrayHasKey('a', $desc['field'], 'sql_alter rate ADD multiples');
		$this->assertArrayHasKey('b', $desc['field'], 'sql_alter rate ADD multiples');
		$this->assertArrayHasKey('c', $desc['field'], 'sql_alter rate ADD multiples');
		$this->assertArrayHasKey('KEY abc', $desc['key'], 'sql_alter rate ADD multiples');
	}

	#[Depends('testCreateTables')]
	#[DataProvider('providerTablesData')]
	public function testDropTables($table, $desc, $data): void
	{
		$this->assertTrue(sql_drop($table, false));
	}

	/**
	 * Description des tables & données de tests
	 */
	public static function providerTablesData(): array
	{
		return [
			'tintin' => [
				'spip_test_tintin',
				[
					'field' => [
						"id_tintin" => "INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY",
						"un_bigint" => "BIGINT(21) NOT NULL DEFAULT '0'",
						"un_int" => "BIGINT(21) NOT NULL DEFAULT '0'",
						"un_smallint" => "SMALLINT(3) NOT NULL DEFAULT '0'",
						"un_double" => "DOUBLE NOT NULL DEFAULT '0'",
						"un_tinyint" => "TINYINT(2) NOT NULL DEFAULT '0'",
						"un_varchar" => "VARCHAR(30) NOT NULL DEFAULT ''",
						"un_texte" => "TEXT NOT NULL DEFAULT ''",
						"maj" => "TIMESTAMP"
					],
					'key' => [],
					'nb_key_attendues' => 1 // attention : la primary key DOIT etre dans les cle aussi
				],
				[
					[
						"id_tintin" => 1,
						"un_bigint" => 30000,
						"un_int" => 2000,
						"un_smallint" => 40,
						"un_double" => 2.58,
						"un_tinyint" => 8,
						"un_varchar" => "Premier varchar",
						"un_texte" => "Premier texte",
						//"maj" => "" // doit se remplir automatiquement
					],
					[
						"id_tintin" => 2,
						"un_bigint" => 40000,
						"un_int" => 3000,
						"un_smallint" => 50,
						"un_double" => 3.58,
						"un_tinyint" => 9,
						"un_varchar" => "Deuxieme varchar",
						"un_texte" => "Second texte",
						//"maj" => "" // doit se remplir automatiquement
					],
					[
						"id_tintin" => 3,
						"un_bigint" => 60000,
						"un_int" => 4000,
						"un_smallint" => 70,
						"un_double" => 8.58,
						"un_tinyint" => 3,
						"un_varchar" => "Troisieme varchar",
						"un_texte" => "Troisieme texte",
						//"maj" => "" // doit se remplir automatiquement
					],
				],

			],
			'milou' => [
				'spip_test_milou',
				[
					'field' => [
						"id_milou" => "INTEGER NOT NULL AUTO_INCREMENT",
						"id_tintin" => "INTEGER NOT NULL",
						"un_enum" => "ENUM('blanc','noir') NOT NULL DEFAULT 'blanc'",
						"wouaf" => "VARCHAR(80) NOT NULL DEFAULT ''",
						"grrrr" => "VARCHAR(80) NOT NULL DEFAULT ''",
						"schtroumf" => "VARCHAR(80) NOT NULL DEFAULT ''",
						"maj" => "TIMESTAMP"
					],
					'key' => [
						"PRIMARY KEY" => "id_milou",
						"KEY id_tintin" => "id_tintin",
						"KEY sons" => "wouaf, grrrr",
					],
					'nb_key_attendues' => 3 // attention : la primary key DOIT etre dans les cle aussi
				],
				[
					[
						"id_milou" => 1,
						"id_tintin" => 1,
						"un_enum" => "blanc",
						"wouaf" => "Warf !!",
						"grrrr" => "Grogne !",
						// "maj" => "" // doit se remplir automatiquement
					],
					[
						"id_milou" => 2,
						"id_tintin" => 1,
						"un_enum" => "noir",
						"wouaf" => "Wouf",
						"grrrr" => "<multi>[fr]Crac[en]Krack</multi>",
						// "maj" => "" // doit se remplir automatiquement
					],
					[
						"id_milou" => 3,
						"id_tintin" => 2,
						"un_enum" => "blanc",
						"wouaf" => "Wif",
						"grrrr" => "Ahrg",
						// "maj" => "" // doit se remplir automatiquement
					],
				],

			],
			'haddock' => [
				'spip_test_haddock',
				[
					'field' => [
						"id_haddock" => "INTEGER NOT NULL AUTO_INCREMENT",
						"alcool" => "VARCHAR(80) NOT NULL DEFAULT ''",

					],
					'key' => [
						"PRIMARY KEY" => "id_haddock",
					],
					'nb_key_attendues' => 1 // attention : la primary key DOIT etre dans les cle aussi
				],
				[
					[
						"id_haddock" => 1,
						"alcool" => "<multi>[fr]Agile[en]Agily</multi>",
					],
					[
						"id_haddock" => 2,
						"alcool" => "<multi>[fr]Aérien[en]Aérieny</multi>",
					],
					[
						"id_haddock" => 3,
						"alcool" => "<multi>[fr]Vinasse[en]Vinassy</multi>",
					],
					[
						"id_haddock" => 4,
						"alcool" => "Un début de chaine : <multi>[fr]Vinasse[en]Vinassy</multi>, et [la fin]",
					],
				],

			],
		];
	}
}
