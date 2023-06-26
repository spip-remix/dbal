<?php

declare(strict_types=1);

/**
 * Test unitaire de la fonction objet_type du fichier base/connect_sql.php
 */

namespace Spip\Test\Sql\Objets;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ObjetTypeTest extends TestCase
{
	public static function setUpBeforeClass(): void {
		find_in_path('base/connect_sql.php', '', true);
	}

	#[DataProvider('providerConnectSqlObjetType')]
	public function testConnectSqlObjetType($expected, ...$args): void {
		$actual = objet_type(...$args);
		$this->assertSame($expected, $actual);
	}

	public static function providerConnectSqlObjetType(): array {
		return [[
			0 => 'article',
			1 => 'articles',
		], [
			0 => 'article',
			1 => 'article',
		], [
			0 => 'article',
			1 => 'spip_articles',
		], [
			0 => 'article',
			1 => 'id_article',
		], [
			0 => 'rubrique',
			1 => 'rubriques',
		], [
			0 => 'rubrique',
			1 => 'spip_rubriques',
		], [
			0 => 'rubrique',
			1 => 'id_rubrique',
		], [
			0 => 'mot',
			1 => 'mots',
		], [
			0 => 'mot',
			1 => 'spip_mots',
		], [
			0 => 'mot',
			1 => 'id_mot',
		], [
			0 => 'groupe_mots',
			1 => 'groupes_mots',
		], [
			0 => 'groupe_mots',
			1 => 'spip_groupes_mots',
		], [
			0 => 'groupe_mots',
			1 => 'id_groupe',
		], [
			0 => 'groupe_mots',
			1 => 'groupes_mot',
		], [
			0 => 'site',
			1 => 'syndic',
		], [
			0 => 'site',
			1 => 'site',
		], [
			0 => 'site',
			1 => 'spip_syndic',
		], [
			0 => 'site',
			1 => 'id_syndic',
		], [
			0 => 'syndic_article',
			1 => 'syndic_articles',
		], [
			0 => 'syndic_article',
			1 => 'spip_syndic_articles',
		], [
			0 => 'syndic_article',
			1 => 'id_syndic_article',
		], [
			0 => 'syndic_article',
			1 => 'syndic_article',
		], [
			0 => 'site',
			1 => 'racine-site',
		], [
			0 => 'mot',
			1 => 'mot-cle',
		], [
			0 => 'truc_pas_connu',
			1 => 'truc_pas_connu',
		], [
			0 => 'truc_pas_connu',
			1 => 'truc_pas_connus',
		], ['article', 'articles'], ['auteur', 'auteurs'], ['document', 'documents'], ['forum', 'forums'], [
			'forum',
			'forum',
		], ['groupe_mots', 'groupes_mots'], ['mot', 'mots'], [
			'rubrique',
			'rubriques',
		], ['site', 'syndic'], ['syndic_article', 'syndic_articles'], ['types_document', 'types_documents']];
	}
}
