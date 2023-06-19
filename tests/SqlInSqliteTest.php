<?php

declare(strict_types=1);

namespace Spip\Test\Sql;

use PHPUnit\Framework\TestCase;

class SqlInSqliteTest extends TestCase
{
	public static function setUpBeforeClass(): void {
		find_in_path('base/abstract_sql.php', '', true);
	}

	protected function setUp(): void {
		if ($this->getSqlType() !== 'sqlite3') {
			$this->markTestSkipped('Needs a Sqlite database');
		}
	}

	/**
	 * @dataProvider providerSqliteSqliIn
	 */
	public function testSqliteSqlIn($expected, ...$args): void {
		$this->assertEquals($expected, sql_in(...$args));
	}

	public static function providerSqliteSqliIn(): array {
		return [
			0 =>
			[
				0 => '(id_rubrique  IN (1,2,3))',
				1 => 'id_rubrique',
				2 => '1,2,3',
			],
			1 =>
			[
				0 => '(id_rubrique  IN (1,2,3))',
				1 => 'id_rubrique',
				2 =>
				[
					0 => 1,
					1 => 2,
					2 => 3,
				],
			],
			2 =>
			[
				0 => '(id_rubrique NOT IN (1,2,3))',
				1 => 'id_rubrique',
				2 => '1,2,3',
				3 => 'NOT',
			],
			3 =>
			[
				0 => '(id_rubrique NOT IN (1,2,3))',
				1 => 'id_rubrique',
				2 =>
				[
					0 => 1,
					1 => 2,
					2 => 3,
				],
				3 => 'NOT',
			],
			4 =>
			[
				0 => '0=1',
				1 => 'id_rubrique',
				2 =>
				[],
			],
			5 =>
			[
				0 => '(id_rubrique  IN (\'\',0,\'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->https://www.spip.net] https://www.spip.net\',\'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;\',\'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot;\',\'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot;\',\'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot;\',\'Un texte sans entites &<>"\'\'\',\'{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>\',\'Un modele <modeleinexistant|lien=[->https://www.spip.net]>\',\'Un texte avec des retour
a la ligne et meme des

paragraphes\'))',
				1 => 'id_rubrique',
				2 =>
				[
					0 => '',
					1 => '0',
					2 => 'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->https://www.spip.net] https://www.spip.net',
					3 => 'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;',
					4 => 'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot;',
					5 => 'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot;',
					6 => 'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot;',
					7 => 'Un texte sans entites &<>"\'',
					8 => '{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>',
					9 => 'Un modele <modeleinexistant|lien=[->https://www.spip.net]>',
					10 => 'Un texte avec des retour
a la ligne et meme des

paragraphes',
				],
			],
			6 =>
			[
				0 => '(id_rubrique  IN (0,-1,1,2,3,4,5,6,7,10,20,30,50,100,1000,10000))',
				1 => 'id_rubrique',
				2 =>
				[
					0 => 0,
					1 => -1,
					2 => 1,
					3 => 2,
					4 => 3,
					5 => 4,
					6 => 5,
					7 => 6,
					8 => 7,
					9 => 10,
					10 => 20,
					11 => 30,
					12 => 50,
					13 => 100,
					14 => 1000,
					15 => 10000,
				],
			],
			7 =>
			[
				0 => '0=1',
				1 => 'id_rubrique',
				2 =>
				[
					0 =>
					[],
					1 =>
					[
						0 => '',
						1 => '0',
						2 => 'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->https://www.spip.net] https://www.spip.net',
						3 => 'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;',
						4 => 'Un texte avec des entit&amp;eacute;s echap&amp;eacute; &amp;amp;&amp;lt;&amp;gt;&amp;quot;',
						5 => 'Un texte avec des entit&#233;s num&#233;riques &#38;&#60;&#62;&quot;',
						6 => 'Un texte avec des entit&amp;#233;s num&amp;#233;riques echap&amp;#233;es &amp;#38;&amp;#60;&amp;#62;&amp;quot;',
						7 => 'Un texte sans entites &<>"\'',
						8 => '{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>',
						9 => 'Un modele <modeleinexistant|lien=[->https://www.spip.net]>',
						10 => 'Un texte avec des retour
a la ligne et meme des

paragraphes',
					],
					2 =>
					[
						0 => 0,
						1 => -1,
						2 => 1,
						3 => 2,
						4 => 3,
						5 => 4,
						6 => 5,
						7 => 6,
						8 => 7,
						9 => 10,
						10 => 20,
						11 => 30,
						12 => 50,
						13 => 100,
						14 => 1000,
						15 => 10000,
					],
					3 =>
					[
						0 => true,
						1 => false,
					],
				],
			],
			8 =>
			[
				0 => '(id_rubrique  IN (2))',
				1 => 'id_rubrique',
				2 => 2,
			],
			9 =>
			[
				0 => '(id_rubrique  IN (1,0))',
				1 => 'id_rubrique',
				2 =>
				[
					0 => true,
					1 => false,
				],
			],
		];
	}

	private function getSqlType(): string {
		return $GLOBALS['connexions'][0]['type'] ?? '';
	}
}
