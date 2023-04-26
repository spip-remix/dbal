<?php

declare(strict_types=1);

/**
 * Test unitaire de la fonction id_table_objet du fichier base/connect_sql.php
 */

namespace Spip\Test\Sql\Objets;

use PHPUnit\Framework\TestCase;

class IdTableObjetTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		find_in_path('base/connect_sql.php', '', true);
	}

	/**
	 * @dataProvider providerConnectSqlIdTableObjet
	 */
	public function testConnectSqlIdTableObjet($expected, ...$args): void
	{
		$actual = id_table_objet(...$args);
		$this->assertSame($expected, $actual);
	}

	public static function providerConnectSqlIdTableObjet(): array
	{
		return [[
			0 => 'id_article',
			1 => 'articles',
		], [
			0 => 'id_article',
			1 => 'article',
		], [
			0 => 'id_article',
			1 => 'spip_articles',
		], [
			0 => 'id_article',
			1 => 'id_article',
		], [
			0 => 'id_rubrique',
			1 => 'rubriques',
		], [
			0 => 'id_rubrique',
			1 => 'spip_rubriques',
		], [
			0 => 'id_rubrique',
			1 => 'id_rubrique',
		], [
			0 => 'id_mot',
			1 => 'mots',
		], [
			0 => 'id_mot',
			1 => 'spip_mots',
		], [
			0 => 'id_mot',
			1 => 'id_mot',
		], [
			0 => 'id_groupe',
			1 => 'groupes_mots',
		], [
			0 => 'id_groupe',
			1 => 'spip_groupes_mots',
		], [
			0 => 'id_groupe',
			1 => 'id_groupe',
		], [
			0 => 'id_groupe',
			1 => 'groupes_mot',
		], [
			0 => 'id_syndic',
			1 => 'syndic',
		], [
			0 => 'id_syndic',
			1 => 'site',
		], [
			0 => 'id_syndic',
			1 => 'spip_syndic',
		], [
			0 => 'id_syndic',
			1 => 'id_syndic',
		], [
			0 => 'id_syndic_article',
			1 => 'syndic_articles',
		], [
			0 => 'id_syndic_article',
			1 => 'spip_syndic_articles',
		], [
			0 => 'id_syndic_article',
			1 => 'id_syndic_article',
		], [
			0 => 'id_syndic_article',
			1 => 'syndic_article',
		], ['id_article', 'article'], ['id_auteur', 'auteur'], ['id_document', 'document'], ['id_document', 'doc'], [
			'id_document',
			'img',
		], ['id_document', 'img'], ['id_forum', 'forum'], ['id_groupe', 'groupe_mots'], [
			'id_groupe',
			'groupe_mot',
		], ['id_groupe', 'groupes_mots'], ['id_groupe', 'groupe'], ['id_mot', 'mot'], ['id_rubrique', 'rubrique'], [
			'id_syndic',
			'syndic',
		], ['id_syndic', 'site'], ['id_syndic_article', 'syndic_article'], ['extension', 'type_document']];
	}
}
