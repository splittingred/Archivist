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
 * Properties Default Topic for Archivist
 *
 * @package archivist
 * @subpackage lexicon
 * @language en
 */

/* Archivist Snippet */
$_lang['prop_archivist.tpl_desc'] = 'Name of a Chunk that will be used for each result.';
$_lang['prop_archivist.target_desc'] = 'The target Resource to point the archive links to. Will default to the current Resource.';
$_lang['prop_archivist.parents_desc'] = 'Optional. Comma-delimited list of ids serving as parents. Will default to the current Resource otherwise.';
$_lang['prop_archivist.depth_desc'] = 'Integer value indicating depth to search for resources from each parent. Defaults to 10.';
$_lang['prop_archivist.sortby_desc'] = 'Field to sort by. Defaults to publishedon.';
$_lang['prop_archivist.sortdir_desc'] = 'Order which to sort by. Defaults to DESC.';
$_lang['prop_archivist.limit_desc'] = 'Limits the number of resources returned. Defaults to 10.';
$_lang['prop_archivist.start_desc'] = 'Optional. An offset of resources returned by the criteria to skip.';
$_lang['prop_archivist.usemonth_desc'] = 'If true, will use the month in the archive list.';
$_lang['prop_archivist.useday_desc'] = 'If true, will use the day in the archive list.';
$_lang['prop_archivist.dateformat_desc'] = 'Optional. The date format, according to MySQL DATE_FORMAT() syntax, for each row. If blank, Archivist will calculate this automatically.';
$_lang['prop_archivist.usefurls_desc'] = 'If true, will generate links in pretty Friendly URL format.';
$_lang['prop_archivist.persistgetparams_desc'] = 'If true, links generated will persist the GET params of the page they are on. Not recommended.';
$_lang['prop_archivist.extraparams_desc'] = 'Optional. If specified, will attach this to the URL of each row.';
$_lang['prop_archivist.cls_desc'] = 'A CSS class to add to each row.';
$_lang['prop_archivist.altcls_desc'] = 'A CSS class to add to each alternate row.';
$_lang['prop_archivist.firstcls_desc'] = 'Optional. A CSS class to add to the first row. If empty will ignore.';
$_lang['prop_archivist.lastcls_desc'] = 'Optional. A CSS class to add to the last row. If empty will ignore.';
$_lang['prop_archivist.filterprefix_desc'] = 'The prefix to use for GET parameters with the Archivist links. Make sure this is the same as the filterPrefix parameter on the getArchives snippet call.';
$_lang['prop_archivist.toplaceholder_desc'] = 'If set, will set the output of this snippet to this placeholder rather than output it.';
$_lang['prop_archivist.setlocale_desc'] = 'If true, Archivist will run the setlocale function with your cultureKey setting if your cultureKey is not "en".';

/* getArchives snippet */
$_lang['prop_getarchives.tpl_desc'] = 'Name of a chunk serving as a resource template.';
$_lang['prop_getarchives.filterfield_desc'] = 'The field to use to filter by when filtering by archives. Make sure this is the same as the sortBy parameter on the Archivist snippet call.';
$_lang['prop_getarchives.filterprefix_desc'] = 'The prefix to use for GET parameters with the Archivist links. Make sure this is the same as the filterPrefix parameter on the Archivist snippet call.';
$_lang['prop_getarchives.tagsindex_desc'] = 'If set, and a REQUEST var of this index is found, will automatically filter results by the value.';
$_lang['prop_getarchives.toplaceholder_desc'] = 'If set, will set the output of this snippet to this placeholder rather than output it.';
$_lang['prop_getarchives.tplodd_desc'] = 'Name of a chunk serving as resource template for resources with an odd idx value (see idx property).';
$_lang['prop_getarchives.tplfirst_desc'] = 'Name of a chunk serving as resource template for the first resource (see first property).';
$_lang['prop_getarchives.tpllast_desc'] = 'Name of a chunk serving as resource template for the last resource (see last property).';
$_lang['prop_getarchives.sortby_desc'] = 'Field to sort by. Defaults to publishedon.';
$_lang['prop_getarchives.sortbyalias_desc'] = 'Query alias for sortby field. Defaults to an empty string.';
$_lang['prop_getarchives.sortbyescaped_desc'] = 'Determines if the field name specified in sortby should be escaped. Defaults to 0.';
$_lang['prop_getarchives.sortdir_desc'] = 'Order which to sort by. Defaults to DESC.';
$_lang['prop_getarchives.limit_desc'] = 'Limits the number of resources returned. Defaults to 5.';
$_lang['prop_getarchives.offset_desc'] = 'An offset of resources returned by the criteria to skip.';
$_lang['prop_getarchives.tvfilters_desc'] = 'Delimited-list of TemplateVar values to filter resources by. Supports two delimiters and two value search formats. THe first delimeter || represents a logical OR and the primary grouping mechanism.  Within each group you can provide a comma-delimited list of values. These values can be either tied to a specific TemplateVar by name, e.g. myTV==value, or just the value, indicating you are searching for the value in any TemplateVar tied to the Resource. An example would be &tvFilters=`filter2==one,filter1==bar%||filter1==foo`. <br />NOTE: filtering by values uses a LIKE query and % is considered a wildcard. <br />ANOTHER NOTE: This only looks at the raw value set for specific Resource, i. e. there must be a value specifically set for the Resource and it is not evaluated.';
$_lang['prop_getarchives.depth_desc'] = 'Integer value indicating depth to search for resources from each parent. Defaults to 10.';
$_lang['prop_getarchives.parents_desc'] = 'Optional. Comma-delimited list of ids serving as parents.';
$_lang['prop_getarchives.includecontent_desc'] = 'Indicates if the content of each resource should be returned in the results. Defaults to false.';
$_lang['prop_getarchives.includetvs_desc'] = 'Indicates if TemplateVar values should be included in the properties available to each resource template. Defaults to false.';
$_lang['prop_getarchives.processtvs_desc'] = 'Indicates if TemplateVar values should be rendered as they would on the resource being summarized. Defaults to false.';
$_lang['prop_getarchives.tvprefix_desc'] = 'The prefix for TemplateVar properties. Defaults to: tv.';
$_lang['prop_getarchives.idx_desc'] = 'You can define the starting idx of the resources, which is an property that is incremented as each resource is rendered.';
$_lang['prop_getarchives.first_desc'] = 'Define the idx which represents the first resource (see tplFirst). Defaults to 1.';
$_lang['prop_getarchives.last_desc'] = 'Define the idx which represents the last resource (see tplLast). Defaults to the number of resources being summarized + first - 1';
$_lang['prop_getarchives.debug_desc'] = 'If true, will send the SQL query to the MODx log. Defaults to false.';