<?php
/**
 * Archivist
 *
 * Copyright 2010 by Shaun McCormick <shaun@modxcms.com>
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
/*
 * Display an archived result filter list
 * 
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'row');
$parents = !empty($scriptProperties['parents']) ? $scriptProperties['parents'] : $modx->resource->get('id');
$parents = explode(',',$parents);
$target = !empty($scriptProperties['target']) ? $scriptProperties['target'] : $modx->resource->get('id');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'publishedon');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'DESC');
$depth = $modx->getOption('depth',$scriptProperties,10);

$cls = $modx->getOption('cls',$scriptProperties,'arc-row');
$altCls = $modx->getOption('altCls',$scriptProperties,'arc-row-alt');
$lastCls = $modx->getOption('lastCls',$scriptProperties,'');
$firstCls = $modx->getOption('firstCls',$scriptProperties,'');

$filterPrefix = $modx->getOption('filterPrefix',$scriptProperties,'arc_');
$useMonth = $modx->getOption('useMonth',$scriptProperties,true);
$useDay = $modx->getOption('useDay',$scriptProperties,false);
$dateFormat = !empty($scriptProperties['dateFormat']) ? $scriptProperties['dateFormat'] : '';
$limit = $modx->getOption('limit',$scriptProperties,10);
$start = $modx->getOption('start',$scriptProperties,0);
$hideContainers = $modx->getOption('hideContainers',$scriptProperties,true);
$useFurls = $modx->getOption('useFurls',$scriptProperties,true);
$persistGetParams = $modx->getOption('persistGetParams',$scriptProperties,false);

/* handle existing GET params */
$extraParams = $modx->getOption('extraParams',$scriptProperties,array());
$extraParams = $archivist->mergeGetParams($extraParams,$persistGetParams,$filterPrefix);

/* find children of parents */
$children = array();
foreach ($parents as $parent) {
    $pchildren = $modx->getChildIds($parent, $depth);
    if (!empty($pchildren)) $children = array_merge($children, $pchildren);
}
if (!empty($children)) $parents = array_merge($parents, $children);

/* get filter format */
if (empty($dateFormat)) {
    $dateFormat = '%Y';
    if ($useMonth) $dateFormat = '%M '.$dateFormat;
    if ($useDay) $dateFormat = '%d '.$dateFormat;
}
/* build query */
$c = $modx->newQuery('modResource');
$fields = $modx->getSelectColumns('modResource','','',array('id'));
$c->select($fields);
$c->select(array(
    'FROM_UNIXTIME(`'.$sortBy.'`,"'.$dateFormat.'") AS `date`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%b") AS `month_name_abbr`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%M") AS `month_name`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%m") AS `month`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%Y") AS `year`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%d") AS `day`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%D") AS `day_formatted`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%W") AS `weekday`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%w") AS `weekday_idx`',
    'COUNT(*) AS `count`',
));
$c->where(array(
    '`parent` IN ('.implode(',',$parents).')',
));
if ($hideContainers) {
    $c->where(array(
        'isfolder' => false,
    ));
}
$c->sortby('FROM_UNIXTIME(`'.$sortBy.'`,"%Y") '.$sortDir.', FROM_UNIXTIME(`'.$sortBy.'`,"%m") '.$sortDir.', FROM_UNIXTIME(`'.$sortBy.'`,"%d") '.$sortDir,'');
$c->groupby('FROM_UNIXTIME(`'.$sortBy.'`,"'.$dateFormat.'")');
/* if limiting to X records */
if (!empty($limit)) { $c->limit($limit,$start); }
$resources = $modx->getCollection('modResource',$c);

/* iterate over resources */
$output = '';
$idx = 0;
$count = count($resources);
foreach ($resources as $resource) {
    $resourceArray = $resource->toArray();

    /* css classes */
    $resourceArray['cls'] = $cls;
    if ($idx % 2) { $resourceArray['cls'] .= ' '.$altCls; }
    if ($idx == 0 && !empty($firstCls)) { $resourceArray['cls'] .= ' '.$firstCls; }
    if ($idx+1 == $count && !empty($lastCls)) { $resourceArray['cls'] .= ' '.$lastCls; }

    /* setup GET params */
    $params = array();
    $params[$filterPrefix.'year'] = $resource->get('year');

    /* if using month filter */
    if ($useMonth) {
        $params[$filterPrefix.'month'] = $resource->get('month');
    }
    /* if using day filter (why you would ever is beyond me...) */
    if ($useDay) {
        $params[$filterPrefix.'day'] = $resource->get('day');
        if (empty($scriptProperties['dateFormat'])) {
            $resourceArray['date'] = $resource->get('month_name').' '.$resource->get('day_formatted').', '.$resource->get('year');
        }
    }

    if ($useFurls) {
        $params = implode('/',$params);
        if (!empty($extraParams)) $params .= '?'.$extraParams;
        $resourceArray['url'] = $modx->makeUrl($target).$params;
    } else {
        $params = http_build_query($params);
        if (!empty($extraParams)) $params .= '&'.$extraParams;
        $resourceArray['url'] = $modx->makeUrl($target,'',$params);
    }

    $output .= $archivist->getChunk($tpl,$resourceArray);
    $idx++;
}

/* output or set to placeholder */
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;