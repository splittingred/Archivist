<?php
/**
 * @package archivist
 * @subpackage build
 */
$snippets = array();

/* general snippets */
$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'Archivist',
    'description' => '',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.archivist.php'),
),'',true,true);
$properties = include $sources['properties'].'properties.archivist.php';
//$properties = adjustProperties($modx,$properties,$sources['lexicon']);
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'getArchives',
    'description' => '',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.getarchives.php'),
),'',true,true);
$properties = include $sources['properties'].'properties.getarchives.php';
//$properties = adjustProperties($modx,$properties,$sources['lexicon']);
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'ArchivistByMonth',
    'description' => '',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.archivistbymonth.php'),
),'',true,true);
$properties = include $sources['properties'].'properties.archivistbymonth.php';
$snippets[3]->setProperties($properties);
unset($properties);

return $snippets;