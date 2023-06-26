<?php

declare(strict_types=1);

/**
 * Test unitaire de la fonction table_objet du fichier base/connect_sql.php
 */

namespace Spip\Test\Sql\Objets;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TableObjetTest extends TestCase
{
	public static function setUpBeforeClass(): void {
		find_in_path('base/connect_sql.php', '', true);
	}

	#[DataProvider('providerConnectSqlTableObjet')]
	public function testConnectSqlTableObjet($expected, ...$args): void {
		$actual = table_objet(...$args);
		$this->assertSame($expected, $actual);
	}

	public static function providerConnectSqlTableObjet(): array {
		return [[
			0 => 'articles',
			1 => 'articles',
		], [
			0 => 'articles',
			1 => 'article',
		], [
			0 => 'articles',
			1 => 'spip_articles',
		], [
			0 => 'articles',
			1 => 'id_article',
		], [
			0 => 'rubriques',
			1 => 'rubrique',
		], [
			0 => 'rubriques',
			1 => 'spip_rubriques',
		], [
			0 => 'rubriques',
			1 => 'id_rubrique',
		], [
			0 => 'mots',
			1 => 'mot',
		], [
			0 => 'mots',
			1 => 'spip_mots',
		], [
			0 => 'mots',
			1 => 'id_mot',
		], [
			0 => 'groupes_mots',
			1 => 'groupe_mots',
		], [
			0 => 'groupes_mots',
			1 => 'spip_groupes_mots',
		], [
			0 => 'groupes_mots',
			1 => 'id_groupe',
		], [
			0 => 'groupes_mots',
			1 => 'groupes_mot',
		], [
			0 => 'syndic',
			1 => 'syndic',
		], [
			0 => 'syndic',
			1 => 'site',
		], [
			0 => 'syndic',
			1 => 'spip_syndic',
		], [
			0 => 'syndic',
			1 => 'id_syndic',
		], [
			0 => 'syndic_articles',
			1 => 'syndic_article',
		], [
			0 => 'syndic_articles',
			1 => 'spip_syndic_articles',
		], [
			0 => 'syndic_articles',
			1 => 'id_syndic_article',
		], [
			0 => 'syndic_articles',
			1 => 'syndic_article',
		], ['articles', 'article'], ['auteurs', 'auteur'], ['documents', 'document'], ['documents', 'doc'], [
			'documents',
			'img',
		], ['documents', 'img'], ['forums', 'forum'], [
			'groupes_mots',
			'groupe_mots',
		], ['groupes_mots', 'groupe_mot'], ['groupes_mots', 'groupe'], ['mots', 'mot'], ['rubriques', 'rubrique'], [
			'syndic',
			'syndic',
		], ['syndic', 'site'], ['syndic_articles', 'syndic_article'], ['types_documents', 'type_document']];
	}
}
