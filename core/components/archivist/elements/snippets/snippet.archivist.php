<?php
/*
 * Display an archived result filter list
 * 
 * @package archivist
 */
$archivist = $modx->getService('archivist','Archivist',$modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/').'model/archivist/',$scriptProperties);
if (!($archivist instanceof Archivist)) return '';

/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'row');
$parents = explode(',',$modx->getOption('parents',$scriptProperties,$modx->resource->get('id')));
$target = $modx->getOption('target',$scriptProperties,$modx->resource->get('id'));
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
$dateFormat = $modx->getOption('dateFormat',$scriptProperties,'');
$limit = $modx->getOption('limit',$scriptProperties,10);
$start = $modx->getOption('start',$scriptProperties,0);
$extraParams = $modx->getOption('extraParams',$scriptProperties,'');

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
    $params = http_build_query($params);
    if (!empty($extraParams)) $params .= '&'.$extraParams;
    $resourceArray['url'] = $modx->makeUrl($target,'',$params);

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