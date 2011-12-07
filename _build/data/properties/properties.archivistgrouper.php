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
 * @package archivist
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'mode',
        'desc' => 'prop_archivistgrouper.mode_desc',
        'type' => 'list',
        'options' => array(
            array('text' => 'prop_arc.month','value' => 'month'),
            array('text' => 'prop_arc.year','value' => 'year'),
        ),
        'value' => 'month',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'itemTpl',
        'desc' => 'prop_archivistgrouper.itemtpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'itemBrief',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'groupTpl',
        'desc' => 'prop_archivistgrouper.grouptpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'target',
        'desc' => 'prop_archivistgrouper.target_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'parents',
        'desc' => 'prop_archivistgrouper.parents_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'depth',
        'desc' => 'prop_archivistgrouper.depth_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortBy',
        'desc' => 'prop_archivistgrouper.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'publishedon',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortDir',
        'desc' => 'prop_archivistgrouper.sortdir_desc',
        'type' => 'list',
        'options' => array(
            array('text' => 'prop_arc.ascending','value' => 'ASC'),
            array('text' => 'prop_arc.descending','value' => 'DESC'),
        ),
        'value' => 'DESC',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'limitGroups',
        'desc' => 'prop_archivistgrouper.limitgroups_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 12,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'dateFormat',
        'desc' => 'prop_archivistgrouper.dateformat_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'cls',
        'desc' => 'prop_archivistgrouper.cls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'altCls',
        'desc' => 'prop_archivistgrouper.altcls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row-alt',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'prop_archivistgrouper.toplaceholder_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'setLocale',
        'desc' => 'prop_archivistgrouper.setlocale_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'locale',
        'desc' => 'prop_archivistgrouper.locale_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'filterPrefix',
        'desc' => 'prop_archivistgrouper.filterprefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc_',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'useFurls',
        'desc' => 'prop_archivistgrouper.usefurls',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'persistGetParams',
        'desc' => 'prop_archivistgrouper.persistgetparams_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'extraParams',
        'desc' => 'prop_archivistgrouper.extraparams_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'hideContainers',
        'desc' => 'prop_archivistgrouper.hidecontainers_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
);
return $properties;