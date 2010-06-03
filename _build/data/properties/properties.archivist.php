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
        'desc' => 'Name of a Chunk that will be used for each result.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'row',
    ),
    array(
        'name' => 'target',
        'desc' => 'The target Resource to point the archive links to. Will default to the current Resource.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'parents',
        'desc' => 'Optional. Comma-delimited list of ids serving as parents. Will default to the current Resource otherwise.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'depth',
        'desc' => 'Integer value indicating depth to search for resources from each parent. Defaults to 10.',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
    ),
    array(
        'name' => 'sortBy',
        'desc' => 'Field to sort by. Defaults to publishedon.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'publishedon',
    ),
    array(
        'name' => 'sortDir',
        'desc' => 'Order which to sort by. Defaults to DESC.',
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC','vaue' => 'ASC'),
            array('text' => 'DESC','value' => 'DESC'),
        ),
        'value' => 'DESC',
    ),
    array(
        'name' => 'limit',
        'desc' => 'Limits the number of resources returned. Defaults to 10.',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
    ),
    array(
        'name' => 'start',
        'desc' => 'Optional. An offset of resources returned by the criteria to skip.',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
    ),
    array(
        'name' => 'useMonth',
        'desc' => 'If true, will use the month in the archive list.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
    ),
    array(
        'name' => 'useDay',
        'desc' => 'If true, will use the day in the archive list.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
    ),
    array(
        'name' => 'dateFormat',
        'desc' => 'Optional. The date format, according to MySQL DATE_FORMAT() syntax, for each row. If blank, Archivist will calculate this automatically.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'useFurls',
        'desc' => 'If true, will generate links in pretty Friendly URL format.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
    ),
    array(
        'name' => 'persistGetParams',
        'desc' => 'If true, links generated will persist the GET params of the page they are on. Not recommended.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
    ),
    array(
        'name' => 'extraParams',
        'desc' => 'Optional. If specified, will attach this to the URL of each row.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'cls',
        'desc' => 'A CSS class to add to each row.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row',
    ),
    array(
        'name' => 'altCls',
        'desc' => 'A CSS class to add to each alternate row.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc-row-alt',
    ),
    array(
        'name' => 'firstCls',
        'desc' => 'Optional. A CSS class to add to the first row. If empty will ignore.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'lastCls',
        'desc' => 'Optional. A CSS class to add to the last row. If empty will ignore.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'filterPrefix',
        'desc' => 'The prefix to use for GET parameters with the Archivist links. Make sure this is the same as the filterPrefix parameter on the getArchives snippet call.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'arc_',
    ),
    array(
        'name' => 'toPlaceholder',
        'desc' => 'If set, will set the output of this snippet to this placeholder rather than output it.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
);
return $properties;