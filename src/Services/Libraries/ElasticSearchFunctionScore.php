<?php
namespace Tiny\Services\Libraries;

use Elastica\Query\FunctionScore as ElasticaFunctionScore;
use Elastica\Filter\AbstractFilter;

/**
 * Class KrakatauFunctionScore.
 *
 * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
class ElasticSearchFunctionScore extends ElasticaFunctionScore
{
    /**
     * Add a function to the function_score query.
     *
     * @param string         $functionType   valid values are DECAY_* constants and script_score
     * @param array|float    $functionParams the body of the function. See documentation for proper syntax.
     * @param AbstractFilter $filter         optional filter to apply to the function
     * @param float          $weight         function weight
     *
     * @return $this
     */
    public function addFunction($functionType, $functionParams, $filter = null, $weight = null)
    {
        if (!is_null($filter)) {
            $function['filter'] = $filter;
        }
        if ($weight !== null) {
            $function['weight'] = $weight;
        }

        $this->_functions[] = $function;

        return $this;
    }

    /**
     * Add a random_score function to the query.
     *
     * @param number         $seed   the seed value
     * @param AbstractFilter $filter a filter associated with this function
     * @param float          $weight an optional boost value associated with this function
     */
    public function addRandomScoreFunction($seed, $filter = null, $weight = null)
    {
        parent::addFunction('random_score', array('seed' => $seed), $filter, $weight);
    }
}