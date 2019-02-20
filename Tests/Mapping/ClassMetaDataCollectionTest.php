<?php
namespace L3\Bundle\LdapOrmBundle\Tests\Mapping;

use L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection;

/**
 * Testing of entity metadata storage in ClassMetaDataCollection
 */
class ClassMetaDataCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetaDataCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ClassMetaDataCollection;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addArrayField
     */
    public function testAddArrayField()
    {
        $this->object->AddArrayField('test');
        $this->assertTrue($this->object->arrayField['test']);
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::isArrayField
     */
    public function testIsArrayField()
    {
        $this->object->AddArrayField('test');
        $this->assertTrue($this->object->isArrayField('test'));
        $this->assertFalse($this->object->isArrayField('unknown'));
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::setObjectClass
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getObjectClass
     */
    public function testSetObjectClass()
    {
        $this->object->setObjectClass('test');
        $this->assertEquals($this->object->getObjectClass(), 'test');
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addMeta
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getKey
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getMeta
     */
    public function testAddMeta()
    {
        $this->object->addMeta('attributePHP', 'attributeLdap');
        $this->assertEquals($this->object->getKey('attributeLdap'), 'attributePHP');
        $this->assertEquals($this->object->getMeta('attributePHP'), 'attributeLdap');
        $this->assertContains('attributeLdap', $this->object->getMetadatas());
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addArrayOfLink
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::isArrayOfLink
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getArrayOfLinkClass
     */
    public function testAddArrayOfLink()
    {
        $this->object->addArrayOfLink('attributePHP', 'PHPEntityClass');
        $this->assertTrue($this->object->isArrayOfLink('attributePHP'));
        $this->assertEquals('PHPEntityClass', $this->object->getArrayOfLinkClass('attributePHP'));
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addSequence
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::isSequence
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getSequence
     */
    public function testAddSequence()
    {
        $this->object->addSequence('attributePHP', 'SequenceDn');
        $this->assertTrue($this->object->isSequence('attributePHP'));
        $this->assertEquals($this->object->getSequence('attributePHP'), 'SequenceDn');
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addParentLink
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getParentLink
     */
    public function testAddParentLink()
    {
        $this->object->addParentLink('attributePHP', 'Dn');
        $this->assertContains('Dn', $this->object->getParentLink());
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::addRegex
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getDnRegex
     */
    public function testAddRegex()
    {
        $this->object->addRegex('attributePHP', '/regEx/');
        $this->assertContains('/regEx/', $this->object->getDnRegex());
    }

    /**
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::getRepository
     * @covers L3\Bundle\LdapOrmBundle\Mapping\ClassMetaDataCollection::setRepository
     */
    public function testGetRepository()
    {
        $this->object->setRepository('testRepo');
        $this->assertEquals($this->object->getRepository(), 'testRepo');
    }
}
