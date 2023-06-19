<?php

declare(strict_types=1);

/**
 * Test unitaire de la fonction table_objet_sql du fichier base/connect_sql.php
 */

namespace Spip\Test\Sql\Objets;

use PHPUnit\Framework\TestCase;

class TableObjetSqlTest extends TestCase
{
	public static function setUpBeforeClass(): void {
		find_in_path('base/connect_sql.php', '', true);
	}

	/**
	 * @dataProvider providerConnectSqlTableObjetSql
	 */
	public function testConnectSqlTableObjetSql($expected, ...$args): void {
		$actual = table_objet_sql(...$args);
		$this->assertSame($expected, $actual);
	}

	public static function providerConnectSqlTableObjetSql(): array {
		return [[
			0 => 'spip_articles',
			1 => 'articles',
		], [
			0 => 'spip_articles',
			1 => 'article',
		], [
			0 => 'spip_articles',
			1 => 'spip_articles',
		], [
			0 => 'spip_articles',
			1 => 'id_article',
		], [
			0 => 'spip_rubriques',
			1 => 'rubrique',
		], [
			0 => 'spip_rubriques',
			1 => 'spip_rubriques',
		], [
			0 => 'spip_rubriques',
			1 => 'id_rubrique',
		], [
			0 => 'spip_mots',
			1 => 'mot',
		], [
			0 => 'spip_mots',
			1 => 'spip_mots',
		], [
			0 => 'spip_mots',
			1 => 'id_mot',
		], [
			0 => 'spip_groupes_mots',
			1 => 'groupe_mots',
		], [
			0 => 'spip_groupes_mots',
			1 => 'spip_groupes_mots',
		], [
			0 => 'spip_groupes_mots',
			1 => 'id_groupe',
		], [
			0 => 'spip_groupes_mots',
			1 => 'groupes_mot',
		], [
			0 => 'spip_syndic',
			1 => 'syndic',
		], [
			0 => 'spip_syndic',
			1 => 'site',
		], [
			0 => 'spip_syndic',
			1 => 'spip_syndic',
		], [
			0 => 'spip_syndic',
			1 => 'id_syndic',
		], [
			0 => 'spip_syndic_articles',
			1 => 'syndic_article',
		], [
			0 => 'spip_syndic_articles',
			1 => 'spip_syndic_articles',
		], [
			0 => 'spip_syndic_articles',
			1 => 'id_syndic_article',
		], [
			0 => 'spip_syndic_articles',
			1 => 'syndic_article',
		], ['spip_articles', 'article'], ['spip_auteurs', 'auteur'], ['spip_documents', 'document'], [
			'spip_documents',
			'doc',
		], ['spip_documents', 'img'], ['spip_documents', 'img'], ['spip_forum', 'forum'], [
			'spip_groupes_mots',
			'groupes_mots',
		], ['spip_groupes_mots', 'groupe_mots'], ['spip_groupes_mots', 'groupe_mot'], [
			'spip_groupes_mots',
			'groupe',
		], ['spip_mots', 'mot'], ['spip_rubriques', 'rubrique'], ['spip_syndic', 'syndic'], [
			'spip_syndic',
			'site',
		], ['spip_syndic_articles', 'syndic_article'], ['spip_types_documents', 'type_document']];
	}
}
