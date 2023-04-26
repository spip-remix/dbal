<?php

declare(strict_types=1);

/**
 * Test unitaire de la fonction query_echappe_textes du fichier base/connect_sql.php
 */

namespace Spip\Test\Sql\Objets;

use PHPUnit\Framework\TestCase;

class QueryEchappeTextesTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		find_in_path('base/connect_sql.php', '', true);
	}

	protected function setUp(): void
	{
		query_echappe_textes('', 'uniqid');
	}

	/**
	 * @dataProvider providerConnectSqlQueryEchappeTextes
	 */
	public function testConnectSqlQueryEchappeTextes($expected, ...$args): void
	{
		$actual = query_echappe_textes(...$args);
		$this->assertSame($expected, $actual);
	}

	public static function providerConnectSqlQueryEchappeTextes(): array
	{
		$md5 = substr(md5('uniqid'), 0, 4);
		return [
			[
				0 => ['%1$s', ["'guillemets simples'"]],
				1 => "'guillemets simples'",
			],
			[
				0 => ['%1$s', ['"guillemets doubles"']],
				1 => '"guillemets doubles"',
			],
			[
				0 => ['%1$s,%2$s', ["'guillemets simples 1/2'", "'guillemets simples 2/2'"]],
				1 => "'guillemets simples 1/2','guillemets simples 2/2'",
			],
			[
				0 => ['%1$s,%2$s', ['"guillemets doubles 1/2"', '"guillemets doubles 2/2"']],
				1 => '"guillemets doubles 1/2","guillemets doubles 2/2"',
			],
			[
				0 => ['%1$s', ["'guillemets simples \x02@#{$md5}#@\x02 avec un echappement'"]],
				1 => "'guillemets simples \\' avec un echappement'",
			],
			[
				0 => ['%1$s', ["\"guillemets doubles \x03@#{$md5}#@\x03 avec un echappement\""]],
				1 => '"guillemets doubles \\" avec un echappement"',
			],
			[
				0 => ['%1$s', ["'guillemets simples \x02@#{$md5}#@\x02\x03@#{$md5}#@\x03 avec deux echappements'"]],
				1 => "'guillemets simples \\'\\\" avec deux echappements'",
			],
			[
				0 => ['%1$s', ["\"guillemets doubles \x02@#{$md5}#@\x02\x03@#{$md5}#@\x03 avec deux echappements\""]],
				1 => "\"guillemets doubles \\'\\\" avec deux echappements\"",
			],
			[
				0 => ['%1$s', ["'guillemet double \" dans guillemets simples'"]],
				1 => "'guillemet double \" dans guillemets simples'",
			],
			[
				0 => ['%1$s', ["\"guillemet simple ' dans guillemets doubles\""]],
				1 => "\"guillemet simple ' dans guillemets doubles\"",
			],
			// sortie de sqlitemanager firefox
			// (description de table suite a import d'une table au format xml/phpmyadmin v5)
			[
				0 => ['%1$s INTEGER,%2$s VARCHAR', ['"id_objet"', '"objet"']],
				1 => '"id_objet" INTEGER,"objet" VARCHAR',
			],
			[
				0 => [
					'UPDATE spip_truc SET html=%1$s WHERE id_truc=1',
					["'''0'' style=''margin: 0;padding: 0;width: 100\x04@#{$md5}#@\x04;border: 0;height: auto;lin'"],
				],
				1 => "UPDATE spip_truc SET html='''0'' style=''margin: 0;padding: 0;width: 100%;border: 0;height: auto;lin' WHERE id_truc=1",
			],
			[
				0 => [
					'UPDATE spip_truc SET html=%1$s, texte=%2$s WHERE id_truc=1',
					["'''0'' style=''margin: 0;padding: 0;width: 100\x04@#{$md5}#@\x04;border: 0;height: auto;lin'", "'toto'"],
				],
				1 => "UPDATE spip_truc SET html='''0'' style=''margin: 0;padding: 0;width: 100%;border: 0;height: auto;lin', texte='toto' WHERE id_truc=1",
			],
			[
				0 => [
					'UPDATE spip_truc SET texte=%1$s, html=%2$s WHERE id_truc=1',
					["''", "'''0'' style=''margin: 0;padding: 0;width: 100\x04@#{$md5}#@\x04;border: 0;height: auto;lin'"],
				],
				1 => "UPDATE spip_truc SET texte='', html='''0'' style=''margin: 0;padding: 0;width: 100%;border: 0;height: auto;lin' WHERE id_truc=1",
			],
		];
	}
}
