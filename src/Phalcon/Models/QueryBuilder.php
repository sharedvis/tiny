<?php

namespace Tiny\Phalcon\Models;

class QueryBuilder extends \Phalcon\Mvc\Model\Query\Builder
{
    /**
     * Check is join alias exists
     * @param  string  $alias_name Join alias name
     * @return boolean
     */
    public function isJoinAliasExists($alias_name)
    {
        $joins = $this->getJoins();

        if (!empty($joins))
        {
            foreach ($joins as $join)
            {
                if (isset($join[2]) && $join[2] == $alias_name)
                    return true;
            }
        }

        return false;
    }
}
