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
if (!function_exists('parseTplElement')) {
    function parseTplElement(& $_cache, $_validTypes, $type, $source, $properties = null) {
        global $modx;
        $output = null;
        if (!is_string($type) || !in_array($type, $_validTypes)) $type = $modx->getOption('tplType', $properties, '@CHUNK');
        $content = false;
        switch ($type) {
            case '@FILE':
                $path = $modx->getOption('tplPath', $properties, $modx->getOption('assets_path', $properties, MODX_ASSETS_PATH) . 'elements/chunks/');
                $key = $path . $source;
                if (!isset($_cache['@FILE'])) $_cache['@FILE'] = array();
                if (!array_key_exists($key, $_cache['@FILE'])) {
                    if (file_exists($key)) {
                        $content = file_get_contents($key);
                    }
                    $_cache['@FILE'][$key] = $content;
                } else {
                    $content = $_cache['@FILE'][$key];
                }
                if (!empty($content) && $content !== '0') {
                    $chunk = $modx->newObject('modChunk', array('name' => $key));
                    $chunk->setCacheable(false);
                    $output = $chunk->process($properties, $content);
                }
                break;
            case '@INLINE':
                $uniqid = uniqid();
                $chunk = $modx->newObject('modChunk', array('name' => "{$type}-{$uniqid}"));
                $chunk->setCacheable(false);
                $output = $chunk->process($properties, $source);
                break;
            case '@CHUNK':
            default:
                $chunk = null;
                if (!isset($_cache['@CHUNK'])) $_cache['@CHUNK'] = array();
                if (!array_key_exists($source, $_cache['@CHUNK'])) {
                    if ($chunk = $modx->getObject('modChunk', array('name' => $source))) {
                        $_cache['@CHUNK'][$source] = $chunk->toArray('', true);
                    } else {
                        $_cache['@CHUNK'][$source] = false;
                    }
                } elseif (is_array($_cache['@CHUNK'][$source])) {
                    $chunk = $modx->newObject('modChunk');
                    $chunk->fromArray($_cache['@CHUNK'][$source], '', true, true, true);
                }
                if (is_object($chunk)) {
                    $chunk->setCacheable(false);
                    $output = $chunk->process($properties);
                }
                break;
        }
        return $output;
    }
}
if (!function_exists('parseTpl')) {
    function parseTpl($tpl, $properties = null) {
        global $modx;
        static $_tplCache;
        $_validTypes = array(
            '@CHUNK'
            ,'@FILE'
            ,'@INLINE'
        );
        $output = '';
        $prefix = $modx->getOption('tplPrefix', $properties, '');
        if (!empty($tpl)) {
            $bound = array(
                'type' => '@CHUNK'
                ,'value' => $tpl
            );
            if (strpos($tpl, '@') === 0) {
                $endPos = strpos($tpl, ' ');
                if ($endPos > 2 && $endPos < 10) {
                    $tt = substr($tpl, 0, $endPos);
                    if (in_array($tt, $_validTypes)) {
                        $bound['type'] = $tt;
                        $bound['value'] = substr($tpl, $endPos + 1);
                    }
                }
            }
            if (is_array($bound) && isset($bound['type']) && isset($bound['value'])) {
                $output = parseTplElement($_tplCache, $_validTypes, $bound['type'], $bound['value'], $properties);
            }
        }
        if (empty($output) && $output !== '0') { /* print_r the object fields that were returned if no tpl is provided */
            $chunk = $modx->newObject('modChunk');
            $chunk->setCacheable(false);
            $output = $chunk->process(array("{$prefix}output" => print_r($properties, true)), "<pre>[[+{$prefix}output]]</pre>");
        }
        return $output;
    }
}