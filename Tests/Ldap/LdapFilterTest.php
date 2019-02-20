<?php

use L3\Bundle\LdapOrmBundle\Tests\Functional\AppKernel;
use L3\Bundle\LdapOrmBundle\Ldap\Filter\LdapFilter;
use L3\Bundle\LdapOrmBundle\Exception\Filter\InvalidLdapFilterException;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class LdapFilterTest extends PHPUnit_Framework_TestCase {
    private $app;

    private $container;

    public function setUp()
    {
        $this->app = new AppKernel('test', true);
        $this->app->boot();
        $this->container = $this->app->getContainer();
    }

    /**
     * Test creation of an empty filter
     */
    public function testEmptyFilter()
    {
        $filter = new LdapFilter(array());
        $this->assertInstanceOf('L3\\Bundle\\LdapOrmBundle\\Ldap\\Filter\\LdapFilter', $filter);
        $this->assertEquals($filter->format(), 'objectclass=*');
    }

    /**
     * Test creation of ilter with one attribute 
     */
    public function testSingleAttributeFilter()
    {
        $filter = new LdapFilter(array(
            'foo' => 'bar',
        ));
        $this->assertInstanceOf('L3\\Bundle\\LdapOrmBundle\\Ldap\\Filter\\LdapFilter', $filter);
        $this->assertEquals($filter->format(), '(foo=bar)');
    }

    /**
     * Test creation of filter with many attribute
     */
    public function testMultiAttributeFilter()
    {
        $filter = new LdapFilter(array(
            'foo1' => 'bar',
            'foo2' => 'bar2',
             )
        );
        $this->assertInstanceOf('L3\\Bundle\\LdapOrmBundle\\Ldap\\Filter\\LdapFilter', $filter);
        $this->assertEquals($filter->format(), '(&(foo1=bar)(foo2=bar2))');
    }

    /**
     * Test creation of filter with many attribute
     */
    public function testMultiAttributeOrFilter()
    {
        $filter = new LdapFilter(array(
            'foo1' => 'bar',
            'foo2' => 'bar2',
             ),
             'OR'
        );
        $this->assertInstanceOf('L3\\Bundle\\LdapOrmBundle\\Ldap\\Filter\\LdapFilter', $filter);
        $this->assertEquals($filter->format(), '(|(foo1=bar)(foo2=bar2))');
    }

    /**
     * Test creation of filter with many attribute
     */
    public function testMultiAttributeAndFilter()
    {
        $filter = new LdapFilter(array(
            'foo1' => 'bar',
            'foo2' => 'bar2',
             ),
             'AND'
        );
        $this->assertInstanceOf('L3\\Bundle\\LdapOrmBundle\\Ldap\\Filter\\LdapFilter', $filter);
        $this->assertEquals($filter->format(), '(&(foo1=bar)(foo2=bar2))');
    }

    /**
     * Test creation of invalid filter
     * @expectedException     L3\Bundle\LdapOrmBundle\Exception\Filter\InvalidLdapFilterException
     * @expectedExceptionMessage The second argument of LdapFilter must be "OR" or "AND" ("test" given)
     */
    public function testInvalidFilter()
    {
        $filter = new LdapFilter(array(), "test");
    }

}
