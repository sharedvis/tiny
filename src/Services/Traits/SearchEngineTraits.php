<?php
/**
 * SearchEngine traits
 *
 * @package Tiny
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services\Traits;

trait SearchEngineTraits
{
    /**
     * Search engine whitelist
     * @var array
     */
    protected $_search_engine_whitelist = [];

    /**
     * Search engine mapping
     * @var array
     */
    protected $_search_engine_data_mapping = [];

    /**
     * Search engine object
     * @var object
     */
    protected $_search_engine;

    /**
     * Get current search engine service
     * @return object
     */
    public function getSearchEngine()
    {
        return $this->_search_engine;
    }

    /**
     * Set current search engine service
     * @return object
     */
    public function setSearchEngine($type, $configuration)
    {
        if (empty($this->_search_engine))
        {
            switch ($type) {
                case 'elasticsearch':
                    $this->_search_engine = new \Tiny\Services\Search(env('SEARCH_ENGINE_TYPE', 'elasticsearch'),
                        $configuration
                    );
                    break;

                default:
                    # nothing todo
                    break;
            }
        }

        return $this;
    }

    /**
     * Get search engine data mapping
     * @return array
     */
    public function getSearchEngineMapping()
    {
        return $this->_search_engine_data_mapping;
    }

    /**
     * Get search engine data whitelists
     * @return array
     */
    public function getSearchEngineWhitelist()
    {
        return $this->_search_engine_whitelist;
    }
}
