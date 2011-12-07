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
/*
 * Display an archived result filter list, nested by month
 *
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup default properties */
$mode = $modx->getOption('mode',$scriptProperties,'month');
$itemTpl = $modx->getOption('itemTpl',$scriptProperties,'itemBrief');
$parents = !empty($scriptProperties['parents']) ? $scriptProperties['parents'] : $modx->resource->get('id');
$parents = explode(',',$parents);
$target = !empty($scriptProperties['target']) ? $scriptProperties['target'] : $modx->resource->get('id');
$depth = $modx->getOption('depth',$scriptProperties,10);
$where = $modx->getOption('where',$scriptProperties,'');
$hideContainers = $modx->getOption('hideContainers',$scriptProperties,true);
$sortBy = $modx->getOption('sortBy',$scriptProperties,'publishedon');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'DESC');
$dateFormat = !empty($scriptProperties['dateFormat']) ? $scriptProperties['dateFormat'] : '';
$limitGroups = $modx->getOption('limitGroups',$scriptProperties,12);
$limitItems = $modx->getOption('limitItems',$scriptProperties,0);
$resourceSeparator = $modx->getOption('resourceSeparator',$scriptProperties,"\n");
$groupSeparator = $modx->getOption('monthSeparator',$scriptProperties,"\n");

$filterPrefix = $modx->getOption('filterPrefix',$scriptProperties,'arc_');
$useFurls = $modx->getOption('useFurls',$scriptProperties,true);
$persistGetParams = $modx->getOption('persistGetParams',$scriptProperties,false);
/* handle existing GET params */
$extraParams = $modx->getOption('extraParams',$scriptProperties,array());
$extraParams = $archivist->mergeGetParams($extraParams,$persistGetParams,$filterPrefix);

$cls = $modx->getOption('cls',$scriptProperties,'arc-resource-row');
$altCls = $modx->getOption('altCls',$scriptProperties,'arc-resource-row-alt');

/* set locale for date processing */
if ($modx->getOption('setLocale',$scriptProperties,true)) {
    $cultureKey = $modx->getOption('cultureKey',null,'en');
    $locale = !empty($scriptProperties['locale']) ? $scriptProperties['locale'] : $cultureKey;
    if (!empty($locale)) {
        setlocale(LC_ALL,$locale);
    }
}

/* find children of parents */
$children = array();
foreach ($parents as $parent) {
    $pchildren = $modx->getChildIds($parent, $depth);
    if (!empty($pchildren)) $children = array_merge($children, $pchildren);
}
if (!empty($children)) $parents = array_merge($parents, $children);

/* build query */
$c = $modx->newQuery('modResource');
$c->where(array(
    'parent:IN' => $parents,
    'published' => true,
    'deleted' => false,
));
if ($hideContainers) {
    $c->where(array(
        'isfolder' => false,
    ));
}
if (!empty($where)) {
    $where = $modx->fromJSON($where);
    $c->where($where);
}
$c->sortby('FROM_UNIXTIME('.$sortBy.',"%Y") '.$sortDir.', FROM_UNIXTIME('.$sortBy.',"%m") '.$sortDir.', FROM_UNIXTIME('.$sortBy.',"%d") '.$sortDir,'');
$resources = $modx->getIterator('modResource',$c);

/* get grouping constraint */
switch ($mode) {
    case 'year':
        $groupConstraint = '%Y-01-01';
        $groupDefaultTpl = 'yearContainer';
        break;
    case 'month':
    default:
        $groupConstraint = '%Y-%m-01';
        $groupDefaultTpl = 'monthContainer';
        break;
}
$groupTpl = !empty($scriptProperties['groupTpl']) ? $scriptProperties['groupTpl'] : $groupDefaultTpl;

/* iterate over resources */
$output = array();
$children = array();
$resourceArray = array();
$groupIdx = 0;
$childIdx = 0;
$countGroups = 0;
foreach ($resources as $resource) {
    $resourceArray = $resource->toArray();
    $date = $resource->get($sortBy);
    $dateObj = strtotime($date);
    $activeTime = strftime($groupConstraint,$dateObj);
    if (!isset($currentTime)) {
        $currentTime = $activeTime;
    }

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
    if ($childIdx % 2) { $resourceArray['cls'] .= ' '.$altCls; }
    $resourceArray['idx'] = $childIdx;

    if ($currentTime != $activeTime) {
        $groupArray = array();
        $timestamp = strtotime($currentTime);
        $groupArray['month_name'] = strftime('%B',$timestamp);
        $groupArray['month'] = strftime('%m',$timestamp);
        $groupArray['year'] = strftime('%Y',$timestamp);
        $groupArray['year_two_digit'] = strftime('%y',$timestamp);
        $groupArray['day'] = strftime('%d',$timestamp);
        $groupArray['weekday'] = strftime('%A',$timestamp);
        $groupArray['weekday_abbr'] = strftime('%a',$timestamp);
        $groupArray['weekday_idx'] = strftime('%w',$timestamp);
        $groupArray['resources'] = implode($resourceSeparator,$children);
        $groupArray['idx'] = $groupIdx;

        /* setup GET params */
        $params = array();
        $params[$filterPrefix.'year'] = $groupArray['year'];
        if ($mode == 'month') {
            $params[$filterPrefix.'month'] = $groupArray['month'];
        }

        if ($useFurls) {
            $params = implode('/',$params);
            if (!empty($extraParams)) $params .= '?'.$extraParams;
            $groupArray['url'] = $modx->makeUrl($target).$params;
        } else {
            $params = http_build_query($params);
            if (!empty($extraParams)) $params .= '&'.$extraParams;
            $groupArray['url'] = $modx->makeUrl($target,'',$params);
        }
        $output[] = $archivist->getChunk($groupTpl,$groupArray);
        $children = array();
        $childIdx = 0;
        $countGroups++;
        $groupIdx++;
        $currentTime = $activeTime;
    }

    if ($limitItems == 0 || $childIdx < $limitItems) {
        $children[] = $archivist->getChunk($itemTpl,$resourceArray);
    }
    $childIdx++;
    if ($countGroups >= $limitGroups) {
        break;
    }
}

$groupArray = array();
$timestamp = strtotime($currentTime);
$groupArray['month_name'] = strftime('%B',$timestamp);
$groupArray['month'] = strftime('%m',$timestamp);
$groupArray['year'] = strftime('%Y',$timestamp);
$groupArray['year_two_digit'] = strftime('%y',$timestamp);
$groupArray['day'] = strftime('%d',$timestamp);
$groupArray['weekday'] = strftime('%A',$timestamp);
$groupArray['weekday_abbr'] = strftime('%a',$timestamp);
$groupArray['weekday_idx'] = strftime('%w',$timestamp);
$groupArray['resources'] = implode($resourceSeparator,$children);
$groupArray['idx'] = $groupIdx;
/* setup GET params */
$params = array();
$params[$filterPrefix.'year'] = $groupArray['year'];
if ($mode == 'month') {
    $params[$filterPrefix.'month'] = $groupArray['month'];
}

if ($useFurls) {
    $params = implode('/',$params);
    if (!empty($extraParams)) $params .= '?'.$extraParams;
    $groupArray['url'] = $modx->makeUrl($target).$params;
} else {
    $params = http_build_query($params);
    if (!empty($extraParams)) $params .= '&'.$extraParams;
    $groupArray['url'] = $modx->makeUrl($target,'',$params);
}
$output[] = $archivist->getChunk($groupTpl,$groupArray);
$children = array();
$childIdx = 0;
$countGroups++;
$groupIdx++;

/* output or set to placeholder */
$output = implode("\n",$output);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;