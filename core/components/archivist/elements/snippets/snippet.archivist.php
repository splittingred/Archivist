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
 * Display an archived result filter list
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var Archivist $archivist
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
$groupByYear = $modx->getOption('groupByYear',$scriptProperties,false);
$sortYear = $modx->getOption('sortYear',$scriptProperties,'DESC');
$depth = $modx->getOption('depth',$scriptProperties,10);
$where = $modx->getOption('where',$scriptProperties,'');

$cls = $modx->getOption('cls',$scriptProperties,'arc-row');
$altCls = $modx->getOption('altCls',$scriptProperties,'arc-row-alt');
$lastCls = $modx->getOption('lastCls',$scriptProperties,'');
$firstCls = $modx->getOption('firstCls',$scriptProperties,'');

$filterPrefix = $modx->getOption('filterPrefix',$scriptProperties,'arc_');
$useMonth = $modx->getOption('useMonth',$scriptProperties,true);
$useDay = $modx->getOption('useDay',$scriptProperties,false);
$dateFormat = !empty($scriptProperties['dateFormat']) ? $scriptProperties['dateFormat'] : '';
$limit = $modx->getOption('limit',$scriptProperties,12);
$start = $modx->getOption('start',$scriptProperties,0);
$hideContainers = $modx->getOption('hideContainers',$scriptProperties,true);
$useFurls = $modx->getOption('useFurls',$scriptProperties,true);
$persistGetParams = $modx->getOption('persistGetParams',$scriptProperties,false);

/* handle existing GET params */
$extraParams = $modx->getOption('extraParams',$scriptProperties,array());
$extraParams = $archivist->mergeGetParams($extraParams,$persistGetParams,$filterPrefix);

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

/* get filter format */
$dateEmpty = empty($dateFormat);
$sqlDateFormat = '%Y';
if ($dateEmpty) $dateFormat = '%Y';
if ($useMonth) {
    if ($dateEmpty) $dateFormat = '%B '.$dateFormat;
    $sqlDateFormat = '%b '.$sqlDateFormat;
}
if ($useDay) {
    if ($dateEmpty) $dateFormat = '%d '.$dateFormat;
    $sqlDateFormat = '%d '.$sqlDateFormat;
}
/* build query */
$c = $modx->newQuery('modResource');
$fields = $modx->getSelectColumns('modResource','','',array('id',$sortBy));
$c->select($fields);
$c->select(array(
    'FROM_UNIXTIME('.$sortBy.',"'.$sqlDateFormat.'") AS '.$modx->escape('date'),
    'FROM_UNIXTIME('.$sortBy.',"'.$sqlDateFormat.'") AS '.$modx->escape('date'),
    'FROM_UNIXTIME('.$sortBy.',"%D") AS '.$modx->escape('day_formatted'),
    'COUNT('.$modx->escape('id').') AS '.$modx->escape('count'),
));
$c->where(array(
    'parent:IN' => $parents,
    'published' => true,
    'deleted' => false,
));
/* don't grab unpublished resources */
$c->where(array(
    'published' => true,
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
$exclude = $modx->getOption('exclude',$scriptProperties,'');
if (!empty($exclude)) {
    $c->where(array(
        'id:NOT IN' => is_array($exclude) ? $exclude : explode(',',$exclude),
    ));
}
$c->sortby('FROM_UNIXTIME(`'.$sortBy.'`,"%Y") '.$sortDir.', FROM_UNIXTIME(`'.$sortBy.'`,"%m") '.$sortDir.', FROM_UNIXTIME(`'.$sortBy.'`,"%d") '.$sortDir,'');
$c->groupby('FROM_UNIXTIME(`'.$sortBy.'`,"'.$sqlDateFormat.'")');
/* if limiting to X records */
if (!empty($limit)) { $c->limit($limit,$start); }
$resources = $modx->getIterator('modResource',$c);

/* iterate over resources */
$output = array();
$groupByYearOutput = array();
$idx = 0;
$count = count($resources);
/** @var modResource $resource */
foreach ($resources as $resource) {
    $resourceArray = $resource->toArray();

    $date = $resource->get($sortBy);
    $dateObj = strtotime($date);

    $resourceArray['date'] = strftime($dateFormat,$dateObj);
    $resourceArray['month_name_abbr'] = strftime('%b',$dateObj);
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

    if ($groupByYear) {
        $groupByYearOutput[$resourceArray['year']][] = $resourceArray;
    } else {
        $output[] = $archivist->getChunk($tpl,$resourceArray);
    }

    $idx++;
}

if ($groupByYear) {
    $wrapperTpl = $modx->getOption('yearGroupTpl',$scriptProperties,'yeargroup');
    $wrapperRowSeparator = $modx->getOption('yearGroupRowSeparator',$scriptProperties,"\n");
    if (strtolower($sortYear) === 'asc') {
        ksort($groupByYearOutput);
    } else {
        krsort($groupByYearOutput);
    }
    foreach ($groupByYearOutput as $year => $row) {
        $wrapper['year'] = $year;

        $params = array();
        $params[$filterPrefix.'year'] = $year;

        if ($useFurls) {
            $params = implode('/',$params);
            if (!empty($extraParams)) $params .= '?'.$extraParams;
            $wrapper['url'] = $modx->makeUrl($target).$params;
        } else {
            $params = http_build_query($params);
            if (!empty($extraParams)) $params .= '&'.$extraParams;
            $wrapper['url'] = $modx->makeUrl($target,'',$params);
        }

        $wrapper['row'] = array();
        foreach ($row as $month) {
            $wrapper['row'][] = $archivist->getChunk($tpl,$month);
        }
        $wrapper['row'] = implode($wrapperRowSeparator,$wrapper['row']);
        $output[] = $archivist->getChunk($wrapperTpl,$wrapper);
    }
}

/* output or set to placeholder */
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$output = implode($outputSeparator,$output);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;