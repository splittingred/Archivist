<?php
/**
 * @package archivist
 */
if ($modx->event->name != 'OnPageNotFound') return;

$archiveIds = $modx->getOption('archivist.archive_ids',null,'');
if (empty($archiveIds)) return;
$archiveIds = explode(',',$archiveIds);

/* handle redirects */
$search = $_SERVER['REQUEST_URI'];
$search = str_replace($modx->getOption('base_url'),'',$search);
$search = trim($search, '/');

/* get resource to redirect to */
$resourceId = false;
$prefix = 'arc_';
foreach ($archiveIds as $archive) {
    $archive = explode(':',$archive);
    $archiveId = $archive[0];
    $alias = array_search($archiveId,$modx->aliasMap);
    if ($alias) {
        $search = str_replace($alias,'',$search);
        $resourceId = $archiveId;
        if (isset($archive[1])) $prefix = $archive[1];
    }
}
if (!$resourceId) return;

/* figure out archiving */
$params = explode('/', $search);
if (count($params) < 1) return;

/* set Archivist parameters */
$_REQUEST[$prefix.'year'] = $params[0];
if (isset($params[1])) $_REQUEST[$prefix.'month'] = $params[1];
if (isset($params[2])) $_REQUEST[$prefix.'day'] = $params[2];

/* forward */
$modx->sendForward($resourceId);
return;