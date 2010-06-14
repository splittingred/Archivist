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
    $dateFormat = $sqlDateFormat = '%Y';
    if ($useMonth) {
        $dateFormat = '%B '.$dateFormat;
        $sqlDateFormat = '%b '.$sqlDateFormat;
    }
    if ($useDay) {
        $dateFormat = '%d '.$dateFormat;
        $sqlDateFormat = '%d '.$sqlDateFormat;
    }
}
/* build query */
$c = $modx->newQuery('modResource');
$fields = $modx->getSelectColumns('modResource','','',array('id',$sortBy));
$c->select($fields);
$c->select(array(
    'FROM_UNIXTIME(`'.$sortBy.'`,"'.$sqlDateFormat.'") AS `date`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"'.$sqlDateFormat.'") AS `date`',
    'FROM_UNIXTIME(`'.$sortBy.'`,"%D") AS `day_formatted`',
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
$c->groupby('FROM_UNIXTIME(`'.$sortBy.'`,"'.$sqlDateFormat.'")');
/* if limiting to X records */
if (!empty($limit)) { $c->limit($limit,$start); }
$resources = $modx->getCollection('modResource',$c);

/* set culture key */
$cultureKey = $modx->getOption('cultureKey',null,'en');
if ($cultureKey != 'en' && $modx->getOption('setLocale',$scriptProperties,true)) {
    setlocale(LC_ALL,$cultureKey);
}

/* iterate over resources */
$output = '';
$idx = 0;
$count = count($resources);
foreach ($resources as $resource) {
    $resourceArray = $resource->toArray();

    $date = $resource->get($sortBy);
    $dateObj = strtotime($date);
    
    $resourceArray['date'] = strftime($dateFormat,$dateObj);
    $resourceArray['month_name_abbr'] = strftime('%h',$dateObj);
    $resourceArray['month_name'] = strftime('%B',$dateObj);
    $resourceArray['month'] = strftime('%m',$dateObj);
    $resourceArray['year'] = strftime('%Y',$dateObj);
    $resourceArray['year_two_digit'] = strftime('%y',$dateObj);
    $resourceArray['day'] = strftime('%d',$dateObj);
    $resourceArray['weekday'] = strftime('%A',$dateObj);
    $resourceArray['weekday_abbr'] = strftime('%a',$dateObj);
    $resourceArray['weekday_idx'] = strftime('%w',$dateObj);


    /* css classes */
    $resourceArray['cls'] = $cls;
    if ($idx % 2) { $resourceArray['cls'] .= ' '.$altCls; }
    if ($idx == 0 && !empty($firstCls)) { $resourceArray['cls'] .= ' '.$firstCls; }
    if ($idx+1 == $count && !empty($lastCls)) { $resourceArray['cls'] .= ' '.$lastCls; }

    /* setup GET params */
    $params = array();
    $params[$filterPrefix.'year'] = $resourceArray['year'];

    /* if using month filter */
    if ($useMonth) {
        $params[$filterPrefix.'month'] = $resourceArray['month'];
    }
    /* if using day filter (why you would ever is beyond me...) */
    if ($useDay) {
        $params[$filterPrefix.'day'] = $resourceArray['day'];
        if (empty($scriptProperties['dateFormat'])) {
            $resourceArray['date'] = $resourceArray['month_name'].' '.$resourceArray['day'].', '.$resourceArray['year'];
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