<?php
/*
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup default properties */
$parents = $modx->getOption('parents',$scriptProperties,$modx->resource->get('id'));
$sortField = $modx->getOption('sortField',$scriptProperties,'publishedon');
$target = $modx->getOption('target',$scriptProperties,$modx->resource->get('id'));

$modx->setLogTarget('ECHO');

$c = $modx->newQuery('modResource');
//$sortField = $modx->quote($sortField);
$fields = $modx->getSelectColumns('modResource','','',array('id','pagetitle'));
$c->select($fields);
$c->select('FROM_UNIXTIME('.$sortField.',"%M %Y") AS `date`');
$c->where(array(
    '`parent` IN ('.$parents.')',
));
$c->sortby('FROM_UNIXTIME('.$sortField.',"%Y") DESC, FROM_UNIXTIME('.$sortField.',"%m") DESC','');
$c->groupby('FROM_UNIXTIME('.$sortField.',"%M %Y")');
$resources = $modx->getCollection('modResource',$c);
echo $c->toSql();

$output = '';
foreach ($resources as $resource) {
    $resourceArray = $resource->toArray();
    $resourceArray['url'] = $modx->makeUrl($target);
    //print_r($resourceArray);
    $output .= $archivist->getChunk('row',$resourceArray);
}

return $output;