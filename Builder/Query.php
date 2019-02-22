<?php
namespace L3\Bundle\LdapOrmBundle\Builder;


use L3\Bundle\LdapOrmBundle\Exception\BadQueryException;
use L3\Bundle\LdapOrmBundle\Repository\Repository;

class Query
{
    const CAND = 0, COR = 1;
    private $query = [];
    private static $operator = [Query::CAND => '&', Query::COR => '|'];
    private $queryOperator;

    public function __construct($operator)
    {
        if (!array_key_exists($operator, self::$operator)) {
            throw new BadQueryException('Bad operator');
        }
        $this->queryOperator = $operator;
    }

    /**
     * @param Query|Condition[] $query
     */
    public function cAnd($query)
    {
        $this->addQuery($query, Query::CAND);
    }

    /**
     * @param Query|Condition[] $query
     */
    public function cOr($query)
    {
        $this->addQuery($query, Query::COR);
    }

    private function addQuery($query, $operator)
    {
        if (is_array($query)) {
            foreach ($query as $condition) {
                if (!$condition instanceof Condition) {
                    throw new BadQueryException('All element of array must be Condition');
                }
            }
            $this->query[] = [
                'condition' => $query,
                'operator' => $operator
            ];
        } elseif ($query instanceof Query) {
            $this->query[] = [
                'query' => $query,
                'operator' => $operator
            ];
        } else {
            throw new BadQueryException('Bad query element');
        }

    }

    public function getQueryForRepository(Repository $repository)
    {
        $queryString = '(' . self::$operator[$this->queryOperator];
        foreach ($this->query as $queryPart) {
            $queryString .= '(' . self::$operator[$queryPart['operator']];
            if (array_key_exists('condition', $queryPart)) {
                $queryString .= $this->getConditionQuery($queryPart['condition'], $repository);
            } else {
                $queryString .= $queryPart['query']->getQueryForRepository($repository);
            }
            $queryString .= ')';
        }
        return $queryString . ')';
    }

    private function getConditionQuery(array $conditions, Repository $repository)
    {
        $queryString = '';
        foreach ($conditions as $condition) {
            $queryString .= $condition->getQueryForRepository($repository);
        }
        return $queryString;
    }
}