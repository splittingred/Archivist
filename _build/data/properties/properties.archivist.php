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
 * @package archivist
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'tpl',
        'desc' => 'prop_archivist.tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'row',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'target',
        'desc' => 'prop_archivist.target_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'parents',
        'desc' => 'prop_archivist.parents_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'depth',
        'desc' => 'prop_archivist.depth_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortBy',
        'desc' => 'prop_archivist.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'publishedon',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortDir',
        'desc' => 'prop_archivist.sortdir_desc',
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC','vaue' => 'ASC'),
            array('text' => 'DESC','value' => 'DESC'),
        ),
        'value' => 'DESC',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'limit',
        'desc' => 'prop_archivist.limit_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'start',
        'desc' => 'prop_archivist.start_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'useMonth',
        'desc' => 'prop_archivist.usemonth_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'useDay',
        'desc' => 'prop_archivist.useday_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'dateFormat',
        'desc' => 'prop_archivist.dateformat_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'useFurls',
        'desc' => 'prop_archivist.usefurls',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'persistGetParams',
        'desc' => 'prop_archivist.persistgetparams_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'extraParams',
        'desc' => 'prop_archivist.extraparams_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'cls',
        'desc' => 'prop_archivist.cls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'altCls',
        'desc' => 'prop_archivist.altcls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row-alt',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'firstCls',
        'desc' => 'prop_archivist.firstcls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'lastCls',
        'desc' => 'prop_archivist.lastcls_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'filterPrefix',
        'desc' => 'prop_archivist.filterprefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc_',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'prop_archivist.toplaceholder_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'setLocale',
        'desc' => 'prop_archivist.setlocale_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'archivist:properties',
    ),
);
return $properties;