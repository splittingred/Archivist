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
 * Default properties for getArchives snippet
 *
 * @package archivist
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'tpl',
        'desc' => 'prop_getarchives.tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'filterField',
        'desc' => 'prop_getarchives.filterfield_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'publishedon',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'filterPrefix',
        'desc' => 'prop_getarchives.filterprefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc_',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tagsIndex',
        'desc' => 'prop_getarchives.tagsindex_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'tags',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'prop_getarchives.toplaceholder_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tplOdd',
        'desc' => 'prop_getarchives.tplodd_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tplFirst',
        'desc' => 'prop_getarchives.tplfirst_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tplLast',
        'desc' => 'prop_getarchives.tpllast_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortby',
        'desc' => 'prop_getarchives.sortby_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'publishedon',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortbyAlias',
        'desc' => 'prop_getarchives.sortbyalias_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortbyEscaped',
        'desc' => 'prop_getarchives.sortbyescaped_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'sortdir',
        'desc' => 'prop_getarchives.sortdir_desc',
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
        'desc' => 'prop_getarchives.limit_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '5',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'offset',
        'desc' => 'prop_getarchives.offset_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tvFilters',
        'desc' => 'prop_getarchives.tvfilters_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'depth',
        'desc' => 'prop_getarchives.depth_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'parents',
        'desc' => 'prop_getarchives.parents_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'includeContent',
        'desc' => 'prop_getarchives.includecontent_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'includeTVs',
        'desc' => 'prop_getarchives.includetvs_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'processTVs',
        'desc' => 'prop_getarchives.processtvs_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'tvPrefix',
        'desc' => 'prop_getarchives.tvprefix_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'tv.',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'idx',
        'desc' => 'prop_getarchives.idx_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'first',
        'desc' => 'prop_getarchives.first_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'last',
        'desc' => 'prop_getarchives.last_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'archivist:properties',
    ),
    array(
        'name' => 'debug',
        'desc' => 'prop_getarchives.debug_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'archivist:properties',
    ),
);

return $properties;