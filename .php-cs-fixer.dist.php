<?php

$banner = "SPIP, Système de publication pour l'internet

Copyright © avec tendresse depuis 2001
Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James

Ce programme est un logiciel libre distribué sous licence GNU/GPL.";

/**
 * @see https://cs.symfony.com/doc/config.html
 */

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src/Sqlite', __DIR__ . '/base', __DIR__ . '/req', __DIR__ . '/inc', __DIR__ . '/bootstrap'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        'header_comment' => ['header' => $banner, 'comment_type' => 'PHPDoc'],
    ])
    ->setFinder($finder)
;
