<?php
namespace L3\Bundle\LdapOrmBundle\Ldap\Filter;

use L3\Bundle\LdapOrmBundle\Exception\Filter\InvalidLdapFilterException;

class LdapFilter
{
	private $filterData;
        private $operator;

	function __construct($filterArray = array(), $operator = "AND")
	{
		$this->filterData = $filterArray;
                if($operator == "AND") {
                    $this->operator = "&";
                } elseif($operator == "OR") {
                    $this->operator = "|";
                } else {
                    throw new InvalidLdapFilterException(sprintf('The second argument of LdapFilter must be "OR" or "AND" ("%s" given)', $operator));
                }
	}

	public function format($type = null)
	{
		$returnString = "";
                $sufix = "";
                if(count($this->filterData) > 1) 
                {
                    $returnString .= '(' . $this->operator;
                    $sufix .= ')';
                }
		foreach($this->filterData as $key => $value)
		{
			$returnString .= '('  . $key . '=' . $value . ')';
		}
                $returnString .= $sufix;
                if($returnString == null) {
                    $returnString="objectclass=*";
                }
		return $returnString;
	}
}
