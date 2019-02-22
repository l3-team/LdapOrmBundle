<?php
namespace L3\Bundle\LdapOrmBundle\Builder;

use L3\Bundle\LdapOrmBundle\Exception\BadConditionException;
use L3\Bundle\LdapOrmBundle\Repository\Repository;

class Condition
{
    private $key;
    private $value;
    private $not;
    private $conditionOperator;
    const CEQUALS = 0, CDIFFERENT = 1, CLOWER = 2, CLOWEREQUALS = 3, CGREATER = 4, CGREATEREQUALS = 5, CAPPROX = 6;    
    private static $operator = [Condition::CEQUALS => '=', Condition::CDIFFERENT => '!=', Condition::CLOWER => '<', Condition::CLOWEREQUALS => '<=', Condition::CGREATER => '>', Condition::CGREATEREQUALS => '>=', Condition::CAPPROX => '~='];

    public function __construct($key, $value, $not = false, $operator = 0)
    {
        $this->key = $key;
        $this->value = $value;
        $this->not = $not;
        if (!array_key_exists($operator, self::$operator)) {
            throw new BadConditionException('Bad operator');
        }
        $this->conditionOperator = $operator;
    }

    public function getQueryForRepository(Repository $repository)
    {
        //$columns = $repository->getAnalyzer()->listColumns();
        
        //if (!array_key_exists($this->key, $columns)) {
        if($repository->getClass()->getKey($this->key) == null) {
            throw new \InvalidArgumentException('No column name ' . $this->key . '.');
        }
        return ($this->not ? '(!' : '') . '(' . $this->key . self::$operator[$this->conditionOperator] . $this->value . ')' . ($this->not ? ')' : '');
    }
}
