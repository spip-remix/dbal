<?php

declare(strict_types=1);

namespace Spip\Test\Sql;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SqlSchemaTableTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		find_in_path('base/abstract_sql.php', '', true);
	}

	protected function setUp(): void
	{

	}

	#[DataProvider('providerTablesData')]
	public function testDropTablesSetup($table, $desc, $data): void
	{
		$this->assertTrue(sql_drop_table($table, true));
		$tables = test_sql_datas();
		$essais = [];
		$err = [];
		foreach ($tables as $t=>$v){
			$essais["Suppression table $t si existe"] = [true, $t, true]; // reponse, arguments
		}
		$err = tester_fun('sql_drop_table', $essais); // DROP IF EXISTS
		if ($err) {
			return '<b>Suppression de table en echec</b><dl>' . join('', $err) . '</dl>';
		}
	}



	/**
	 * Description des tables & données de tests
	 */
	protected function providerTablesData(): array
	{
		return [
			[
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
					'key' => [

					],
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
			[
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
			[
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
