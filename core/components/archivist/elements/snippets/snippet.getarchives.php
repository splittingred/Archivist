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
 * getArchives
 *
 * Used to display a list of Resources for a given archive state.
 *
 * Written by Shaun McCormick <shaun+archivist@modx.com>. Based on getResources by Jason Coward <jason@modxcms.com>
 *
 * @var Archivist $archivist
 * @var modX $modx
 * @var array $scriptProperties
 * 
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup some getArchives-specific properties */
$filterPrefix = $modx->getOption('filterPrefix',$scriptProperties,'arc_');
$filterField = $modx->getOption('filterField',$scriptProperties,'publishedon');

/* first off, let's sync the archivist.archive_ids setting */
if ($modx->getOption('makeArchive',$scriptProperties,true)) {
    $archivist->makeArchive($modx->resource->get('id'),$filterPrefix);
}

/* get filter by year, month, and/or day. Sanitize to prevent injection. */
$where = $modx->getOption('where',$scriptProperties,false);
$where = is_array($where) ? $where : $modx->fromJSON($where);
$parameters = $modx->request->getParameters();

$year = $modx->getOption($filterPrefix.'year',$parameters,$modx->getOption('year',$scriptProperties,''));
$year = (int)$archivist->sanitize($year);
if (!empty($year)) {
    $modx->setPlaceholder($filterPrefix.'year',$year);
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%Y") = "'.$year.'"';
}
$month = $modx->getOption($filterPrefix.'month',$parameters,$modx->getOption('month',$scriptProperties,''));
$month = (int)$archivist->sanitize($month);
if (!empty($month)) {
    if (strlen($month) == 1) $month = '0'.$month;
    $modx->setPlaceholder($filterPrefix.'month',$month);
    $modx->setPlaceholder($filterPrefix.'month_name',$archivist->translateMonth($month));
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%m") = "'.$month.'"';
}
$day = $modx->getOption($filterPrefix.'day',$parameters,$modx->getOption('day',$scriptProperties,''));
$day = (int)$archivist->sanitize($day);
if (!empty($day)) {
    if (strlen($day) == 1) $day = '0'.$day;
    $modx->setPlaceholder($filterPrefix.'day',$day);
    $where[] = 'FROM_UNIXTIME(`'.$filterField.'`,"%d") = "'.$day.'"';
}

/* author handling */
if (!empty($parameters[$filterPrefix.'author'])) {
    /** @var modUser $user */
    $user = $modx->getObject('modUser',array('username' => $parameters[$filterPrefix.'author']));
    if ($user) {
        $where['createdby'] = $user->get('id');
    }
}
$scriptProperties['where'] = $modx->toJSON($where);

/* better tags handling */
$tagKeyVar = $modx->getOption('tagKeyVar',$scriptProperties,'key');
$tagKey = (!empty($tagKeyVar) && array_key_exists($tagKeyVar,$parameters) && !empty($parameters[$tagKeyVar])) ? $parameters[$tagKeyVar] : $modx->getOption('tagKey',$scriptProperties,'tags');
$tagRequestParam = $modx->getOption('tagRequestParam',$scriptProperties,'tag');
$tag = $modx->getOption('tag',$scriptProperties,array_key_exists($tagRequestParam,$parameters) ? urldecode($parameters[$tagRequestParam]) : '');
if (!empty($tag)) {
    $tag = $modx->stripTags($tag);
    $tagSearchType = $modx->getOption('tagSearchType',$scriptProperties,'exact');
    if ($tagSearchType == 'contains') {
        $scriptProperties['tvFilters'] = $tagKey.'==%'.$tag.'%';
    } else if ($tagSearchType == 'beginswith') {
        $scriptProperties['tvFilters'] = $tagKey.'==%'.$tag.'';
    } else if ($tagSearchType == 'endswith') {
        $scriptProperties['tvFilters'] = $tagKey.'=='.$tag.'%';
    } else {
        $scriptProperties['tvFilters'] = $tagKey.'=='.$tag.'';
    }
}

$grSnippet = $modx->getOption('grSnippet',$scriptProperties,'getResources');
/** @var modSnippet $snippet */
$snippet = $modx->getObject('modSnippet', array('name' => $grSnippet));
if ($snippet) {
    $snippet->setCacheable(false);
    $output = $snippet->process($scriptProperties);
} else {
    return 'You must have getResources downloaded and installed to use this snippet.';
}
return $output;