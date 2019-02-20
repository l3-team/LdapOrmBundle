<?php
namespace L3\Bundle\LdapOrmBundle\Ldap;
use L3\Bundle\LdapOrmBundle\Ldap\Filter\LdapFilter;

class LdapIterator implements \Iterator
{
    private $result;
    private $pos;
    private $total;
    private $currentElement;
    private $entityName;
    private $filter;
    private $forceBadEntityName;

    /**
     * Build the iterator entity
     * @param LdapFilter        $filter the filter to fetch element
     * @param string            $entityName the type (full class name with namespace) of entity to fetch
     * @param LdapEntityManager $entityManager the ldap entity manager
     */
    public function __construct (LdapFilter $filter, $entityName, LdapEntityManager $entityManager, $forceBadEntityName = false) {
        $this->entityManager = $entityManager;
        $this->filter        = $filter;
        $this->entityName    = $entityName;
        $this->pos    = 0;
        $this->forceBadEntityName = $forceBadEntityName;
    }

    /**
     * Init the iterator with doing query
     * 
     * @return bool true if fetching is ok false else
     */
    private function init()
    {
        $instanceMetadataCollection = $this->entityManager->getClassMetadata($this->entityName);
        $this->result = $this->entityManager->doRawLdapSearch(
            $this->filter->format('ldap'),
            array_values($instanceMetadataCollection->getMetadatas()),
            0
        );
        if($this->result) {
            $this->pos    = 0;
            $this->total  = $this->entityManager->doRawLdapCountEntries($this->result);
            $this->currentElement = $this->entityManager->doRawLdapFirstEntry($this->result);

            return true;
        } else {

            return false;
        }
    }

    /**
     * Put the position to the first element of the Iteration and returns it.
     *
     * @return mixed
     */
    public function first() {
        if ($this->result === false) return false;
        $this->init();
        return $this->current();
    }

    /**
     * Return the number of element of the iterator.
     *
     * @return int
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Returns the key of the current element.
     *
     * @return int
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * Moves the current position to the next element.
     *
     * @return mixed
     */
    public function next()
    {
        if (!isset($this->currentElement)) return $this->first();
        if ($this->result === false) return false;
        $this->pos++;
        if (!$this->valid()) return false;

        $this->currentElement = $this->entityManager->doRawLdapNextEntry($this->currentElement);
        if($this->forceBadEntityName) {
            return $this->current();
        }

        if($this->current() instanceof $this->entityName) {
            return $this->current();
        } else {
            return $this->next();
        }
    }

    /**
     * Rewinds back to the first element of the Iterator.
     */
    public function rewind()
    {
        $this->init();
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     *
     * @return bool true if iterator is valid
     */
    public function valid()
    {
        if ($this->result === false) return false;
        if($this->pos >= $this->total) {
            return false;
        }

        return true;
    }

    /**
     * Returns the current element.
     *
     * @return mixed
     */
    public function current()
    {
        if(!isset($this->currentElement)) {
            $this->init();
        }

        $data = $this->entityManager->doRawLdapGetAttributes($this->currentElement);
        $data['dn'] = $this->entityManager->doRawLdapGetDn($this->currentElement);

        return $this->entityManager->arrayToObject($this->entityName, 
            $data
        );
    }
}

