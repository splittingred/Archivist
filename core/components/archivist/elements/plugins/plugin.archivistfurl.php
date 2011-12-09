<?php
/**
 * Archivist
 *
 * Copyright 2010-2011 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Archivist, a simple archive navigation system for MODx
 * Revolution.
 *
 * Archivist is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Archivist is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Archivist; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package archivist
 */
/**
 * @var modX $modx
 * @package archivist
 */
if ($modx->event->name != 'OnPageNotFound') return;

$archiveIds = $modx->getOption('archivist.archive_ids',null,'');
if (empty($archiveIds)) return;
$archiveIds = explode(',',$archiveIds);

/* handle redirects */
$search = $_SERVER['REQUEST_URI'];
$base_url = $modx->getOption('base_url');
if ($base_url != '/') {
    $search = str_replace($base_url,'',$search);
}
$search = trim($search, '/');

/* get resource to redirect to */
$resourceId = false;
$prefix = 'arc_';
foreach ($archiveIds as $archive) {
    $archive = explode(':',$archive);
    $archiveId = $archive[0];
    $alias = array_search($archiveId,$modx->aliasMap);
    if ($alias && strpos($search,$alias) !== false) {
        $search = str_replace($alias,'',$search);
        $resourceId = $archiveId;
        if (isset($archive[1])) $prefix = $archive[1];
    }
}
if (!$resourceId) return;

/* figure out archiving */
$params = explode('/', $search);
if (count($params) < 1) return;

/* tag handling! */
if ($params[0] == 'tags') {
    $_GET['tag'] = $params[1];
} else if ($params[0] == 'user' || $params[0] == 'author') {
    $_GET[$prefix.'author'] = $params[1];
} else {
    /* set Archivist parameters for date-based archives */
    $_GET[$prefix.'year'] = $params[0];
    if (isset($params[1])) $_GET[$prefix.'month'] = $params[1];
    if (isset($params[2])) $_GET[$prefix.'day'] = $params[2];
}

/* forward */
$modx->sendForward($resourceId);
return;