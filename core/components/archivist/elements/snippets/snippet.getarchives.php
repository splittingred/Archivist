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
/**
 * getArchives
 *
 * Used to display a list of Resources for a given archive state.
 *
 * Written by Shaun McCormick <shaun@modxcms.com>. Based on getResources by Jason Coward <jason@modxcms.com>
 *
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup some getArchives-specific properties */
$filterPrefix = $modx->getOption('filterPrefix',$scriptProperties,'arc_');
$filterField = $modx->getOption('filterField',$scriptProperties,'publishedon');
$tagsIndex = $modx->getOption('tagsIndex',$scriptProperties,'tags');

/* first off, let's sync the archivist.archive_ids setting */
$archivist->makeArchive($modx->resource->get('id'),$filterPrefix);

/* get filter by year, month, and/or day. Sanitize to prevent injection. */
$where = array();
$year = $modx->getOption($filterPrefix.'year',$_REQUEST,$modx->getOption('year',$scriptProperties,''));
$year = (int)$archivist->sanitize($year);
if (!empty($year)) {
    $modx->setPlaceholder($filterPrefix.'year',$year);
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%Y") = "'.$year.'"';
}
$month = $modx->getOption($filterPrefix.'month',$_REQUEST,$modx->getOption('month',$scriptProperties,''));
$month = (int)$archivist->sanitize($month);
if (!empty($month)) {
    if (strlen($month) == 1) $month = '0'.$month;
    $modx->setPlaceholder($filterPrefix.'month',$month);
    $modx->setPlaceholder($filterPrefix.'month_name',$archivist->translateMonth($month));
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%m") = "'.$month.'"';
}
$day = $modx->getOption($filterPrefix.'day',$_REQUEST,$modx->getOption('day',$scriptProperties,''));
$day = (int)$archivist->sanitize($day);
if (!empty($day)) {
    if (strlen($day) == 1) $day = '0'.$day;
    $modx->setPlaceholder($filterPrefix.'day',$day);
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%d") = "'.$day.'"';
}
$scriptProperties['where'] = $modx->toJSON($where);

/* automatically integrate for tags (fun!) */
if (!empty($tagsIndex) && isset($_REQUEST[$tagsIndex])) {
    $tags = $archivist->sanitize($_REQUEST[$tagsIndex]);
    if (empty($scriptProperties['tvFilters'])) {
        $scriptProperties['tvFilters'] = $tagsIndex.'==%'.$tags.'%';
    } else {
        $scriptProperties['tvFilters'] .= ','.$tagsIndex.'==%'.$tags.'%';
    }
}


/* below this is mostly getResources code, with extra 'where' and 'toPlaceholder' parameters */

/* set default properties */
$output = array();
$tpl = $modx->getOption('tpl',$scriptProperties,'');
$includeContent = $modx->getOption('includeContent',$scriptProperties,false);
$includeTVs = $modx->getOption('includeTVs',$scriptProperties,false);
$processTVs = $modx->getOption('processTVs',$scriptProperties,false);
$parents = explode(',',$modx->getOption('parents',$scriptProperties,$modx->resource->get('id')));
$tvPrefix = $modx->getOption('tvPrefix',$scriptProperties,'tv.');
$depth = (int)$modx->getOption('depth',$scriptProperties,10);
$hideContainers = $modx->getOption('hideContainers',$scriptProperties,true);
$where = $modx->getOption('where',$scriptProperties,false);

/* find children of parents */
$children = array();
foreach ($parents as $parent) {
    $pchildren = $modx->getChildIds($parent, $depth);
    if (!empty($pchildren)) $children = array_merge($children, $pchildren);
}
if (!empty($children)) $parents = array_merge($parents, $children);

$tvFilters = !empty($scriptProperties['tvFilters']) ? explode('||', $scriptProperties['tvFilters']) : array();

$sortby = $modx->getOption('sortby',$scriptProperties,'publishedon');
$sortbyAlias = $modx->getOption('sortbyAlias',$scriptProperties,'modResource');
$sortbyEscaped = $modx->getOption('sortbyEscaped',$scriptProperties,false);
if ($sortbyEscaped) $sortby = "`{$sortby}`";
if (!empty($sortbyAlias)) $sortby = "`{$sortbyAlias}`.{$sortby}";

$sortdir = $modx->getOption('sortdir',$scriptProperties,'DESC');
$limit = (int)$modx->getOption('limit',$scriptProperties,5);
$offset = (int)$modx->getOption('offset',$scriptProperties,0);
$totalVar = $modx->getOption('totalVar',$scriptProperties,'total');

/* build query */
$contextResourceTbl = $modx->getTableName('modContextResource');
$context = empty($context) ? $modx->quote($modx->context->get('key')) : $modx->quote($context);
$criteria = $modx->newQuery('modResource', array(
    'deleted' => '0'
    ,'published' => '1'
    ,"`modResource`.`parent` IN (" . implode(',', $parents) . ")"
    ,"(`modResource`.`context_key` = {$context} OR EXISTS(SELECT 1 FROM {$contextResourceTbl} `ctx` WHERE `ctx`.`resource` = `modResource`.`id` AND `ctx`.`context_key` = {$context}))"
));
if (empty($showHidden)) {
    $criteria->andCondition(array('hidemenu' => '0'));
}
if (!empty($hideContainers)) {
    $criteria->andCondition(array('isfolder' => '0'));
}

/* added for getArchives */
if (!empty($where)) {
    $where = $modx->fromJSON($where);
    if (is_array($where) && !empty($where)) {
        $criteria->where($where);
    }
}
if (!empty($tvFilters)) {
    $tmplVarTbl = $modx->getTableName('modTemplateVar');
    $tmplVarResourceTbl = $modx->getTableName('modTemplateVarResource');
    $conditions = array();
    foreach ($tvFilters as $fGroup => $tvFilter) {
        $filterGroup = count($tvFilters) > 1 ? $fGroup + 1 : 0;
        $filters = explode(',', $tvFilter);
        foreach ($filters as $filter) {
            $f = explode('==', $filter);
            if (count($f) == 2) {
                $tvName = $modx->quote($f[0]);
                $tvValue = $modx->quote($f[1]);
                $conditions[$filterGroup][] = "EXISTS (SELECT 1 FROM {$tmplVarResourceTbl} `tvr` JOIN {$tmplVarTbl} `tv` ON `tvr`.`value` LIKE {$tvValue} AND `tv`.`name` = {$tvName} AND `tv`.`id` = `tvr`.`tmplvarid` WHERE `tvr`.`contentid` = `modResource`.`id`)";
            } elseif (count($f) == 1) {
                $tvValue = $modx->quote($f[0]);
                $conditions[$filterGroup][] = "EXISTS (SELECT 1 FROM {$tmplVarResourceTbl} `tvr` JOIN {$tmplVarTbl} `tv` ON `tvr`.`value` LIKE {$tvValue} AND `tv`.`id` = `tvr`.`tmplvarid` WHERE `tvr`.`contentid` = `modResource`.`id`)";
            }
        }
    }
    if (!empty($conditions)) {
        foreach ($conditions as $cGroup => $c) {
            if ($cGroup > 0) {
                $criteria->orCondition($c, null, $cGroup);
            } else {
                $criteria->andCondition($c);
            }
        }
    }
}

$total = $modx->getCount('modResource', $criteria);
$modx->setPlaceholder($totalVar, $total);

$criteria->sortby($sortby, $sortdir);
if (!empty($limit)) $criteria->limit($limit, $offset);
$columns = $includeContent ? '*' : $modx->getSelectColumns('modResource', 'modResource', '', array('content'), true);
$criteria->select($columns);
if (!empty($debug)) {
    $criteria->prepare();
    $modx->log(modX::LOG_LEVEL_ERROR, $criteria->toSQL());
}
$collection = $modx->getCollection('modResource', $criteria);

$idx = !empty($idx) ? intval($idx) : 1;
$first = empty($first) && $first !== '0' ? 1 : intval($first);
$last = empty($last) ? (count($collection) + $idx - 1) : intval($last);

/* include parseTpl */
include_once $modx->getOption('core_path').'components/getresources/include.parsetpl.php';

foreach ($collection as $resourceId => $resource) {
    $tvs = array();
    if (!empty($includeTVs)) {
        $templateVars =& $resource->getMany('TemplateVars');
        foreach ($templateVars as $tvId => $templateVar) {
            $tvs[$tvPrefix . $templateVar->get('name')] = !empty($processTVs) ? $templateVar->renderOutput($resource->get('id')) : $templateVar->get('value');
        }
    }
    $odd = ($idx & 1);
    $properties = array_merge(
        $scriptProperties
        ,array(
            'idx' => $idx
            ,'first' => $first
            ,'last' => $last
        )
        ,$resource->toArray()
        ,$tvs
    );
    $resourceTpl = '';
    $tplidx = 'tpl_' . $idx;
    if (!empty($$tplidx)) $resourceTpl = parseTpl($$tplidx, $properties);
    switch ($idx) {
        case $first:
            if (!empty($tplFirst)) $resourceTpl = parseTpl($tplFirst, $properties);
            break;
        case $last:
            if (!empty($tplLast)) $resourceTpl = parseTpl($tplLast, $properties);
            break;
    }
    if ($odd && empty($resourceTpl) && !empty($tplOdd)) $resourceTpl = parseTpl($tplOdd, $properties);
    if (!empty($tpl) && empty($resourceTpl)) $resourceTpl = parseTpl($tpl, $properties);
    if (empty($resourceTpl)) {
        $output[] = $archivist->getChunk('ArchivedItem',$properties);
    } else {
        $output[]= $resourceTpl;
    }
    $idx++;
}
$output = implode("\n", $output);

$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}
return $output;