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
 * The base class for Archivist
 *
 * @package archivist
 */
class Archivist {
    /** @var \modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();

    function __construct(modX &$modx,array $config = array()) {
    	$this->modx =& $modx;
        $corePath = $modx->getOption('archivist.core_path',null,$modx->getOption('core_path').'components/archivist/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'chunksPath' => $corePath.'elements/chunks/',
            'snippetsPath' => $corePath.'elements/snippets/',
        ),$config);
        $this->modx->lexicon->load('archivist:default');
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->_getTplChunk($name);
            if (empty($chunk)) {
                $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @param string $postFix
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name,$postFix = '.chunk.tpl') {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     * Clean a string of modx and html tags, and other problem strings
     *
     * @param string $text The string to sanitize
     * @return string The sanitized string
     */
    public function sanitize($text) {
        $text = strip_tags($text);
        $text = preg_replace('/(\[\[\+.*?\]\])/i', '', $text);
        return $this->modx->stripTags($text);
    }

    /**
     * Translate a month numeric to its word equivalent
     *
     * @param int $month The number index of the month
     * @return string The translated string of the month
     */
    public function translateMonth($month) {
        $month = date('Y').'-'.$month.'-01';

        /* set locale for date processing */
        if ($this->modx->getOption('locale',null,true)) {
            $locale = $this->modx->getOption('locale',null,'en');
            if (!empty($locale)) {
                setlocale(LC_ALL,$locale);
            }
        }
        return strftime('%B',strtotime($month));
    }


    /**
     * Merge GET params and any others into a cohesive query string
     *
     * @param array|string $array
     * @param boolean $persistGetParams
     * @param string $prefix
     * @return string
     */
    public function mergeGetParams($array,$persistGetParams = false,$prefix = 'arc_') {
        if ($persistGetParams) {
            $getParams = $_GET;
            unset($getParams[$this->modx->getOption('request_param_alias',null,'q')],$getParams[$prefix.'year'],$getParams[$prefix.'month'],$getParams[$prefix.'day']);
        } else { $getParams = array(); }

        if (is_string($array)) {
            $array = empty($array) ? array() : explode('&',$array);
        }
        $params = array();
        if (!empty($array)) {
            foreach ($array as $nvp) {
                $nvp = explode('=',$nvp);
                if (array_key_exists(1, $nvp)) $params[$nvp[0]] = $nvp[1];
            }
        }
        $params = array_merge($getParams,$params);
        return http_build_query($params);
    }

    /**
     * Setup this resource as an archive so that FURLs can be effectively mapped
     *
     * @param integer $resourceId The ID of the resource to allow as an archive
     * @param string $prefix The filterPrefix used by that archive
     */
    public function makeArchive($resourceId,$prefix = 'arc_') {
        $value = $resourceId.':'.$prefix;
        $isNew = false;
        
        $setting = $this->modx->getObject('modSystemSetting',array(
            'key' => 'archivist.archive_ids',
        ));
        if (!$setting) {
            /** @var modSystemSetting $setting */
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->fromArray(array(
                'key' => 'archivist.archive_ids',
                'namespace' => 'archivist',
                'area' => 'furls',
                'xtype' => 'textfield',
            ),'',true,true);
            $isNew = true;
        } else {
            $oldValue = $setting->get('value');
            if (strpos($oldValue,$resourceId.':') !== false) { /* dont append if already there */
                $value = $oldValue;
            } else {
                $value = $oldValue.','.$value;
            }
        }
        $setting->set('value',$value);
        $saved = $setting->save();
        if ($isNew) {
            $this->_clearCache();
        }
        return $saved;
    }

    /**
     * Assistance method for makeArchive's cache clear
     * @return array
     */
    private function _clearCache() {
        $paths = array(
            'config.cache.php',
            'sitePublishing.idx.php',
            $this->modx->context->get('key').'/',
        );
        $options = array(
            'publishing' => 1,
            'extensions' => array('.cache.php', '.msg.php', '.tpl.php'),
        );
        if ($this->modx->getOption('cache_db')) $options['objects'] = '*';

        return $this->modx->cacheManager->clearCache($paths, $options);
    }
}