L3LdapOrmBundle
===================

L3LdapOrmBundle is a fork from GorgLdapOrmBundle for use without dependency "r1pp3rj4ck/TwigstringBundle" and use with Symfony version 2, 3 and 4, and add a system of complex Query and Conditions LDAP.
It is an interface for retrieving, modifying and persisting LDAP entities, using PHP's native LDAP functions.

Installation
------------

Add this bundle to your project in `composer.json`:

1.1. Plain `L3LdapOrmBundle`

Simple add this line in your require in your composer.json :

```
"l3/ldap-orm-bundle": "~1.0"
```

1.2. Declare the use of `L3LdapOrmBundle`

```
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new L3\Bundle\LdapOrmBundle\L3LdapOrmBundle(),
        );

        // ...
    }

    // ...
}
```

1.3. Configure the LDAP parameters

For Symfony2 or Symfony3, add the l3_ldap_orm parameters in your config file (parameters.yml and parameters.yml.dist) :

```yaml
l3_ldap_orm:
    connection:
        uri: ldap://ldap.exemple.com
        use_tls: false
        bind_dn: cn=admin,dc=exemple,dc=com
        password: exemplePassword
    ldap:
        base_dn: dc=exemple,dc=com
        password_type: sha1
```

Sha1 is the default hashing method. In some cases (such as OpenLdap) the password is hashed server-side,
if this is the case then change `password_type` to `plaintext`.

Basic Usage
-----------

To use the L3LdapOrmBundle you have to add annotation to an entity like this example:

```php
namespace AppBundle\Entity;

use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Attribute;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Dn;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Sequence;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\ArrayField;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\DnPregMatch;

/**
 * Class for represent Account
 *
 * @ObjectClass("udlAccount")
 * @Dn("uid={{ entity.uid }},{% for entite in entity.entities %}ou={{ entite }},{% endfor %}{{ baseDN }}")
 */
class Account
{
    /**
     * @Attribute("uid")
     */
    private $uid;

    /**
     * @Attribute("givenName")
     */
    private $firstname;

    /**
     * @Attribute("sn")
     */
    private $lastname;

    /**
     * @Attribute("udlAliasLogin")
     * @ArrayField()
     */
    private $alias;

    /**
     * @DnPregMatch("/ou=([a-zA-Z0-9\.]+)/")
     */
    private $entities = array("accounts");

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname=$firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid=$uid;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * For password use sha1 php function in base16 encoding
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function getEntities()
    {
        return $this->entities;
    }
}
```

* Attribute : Use this annotation to map a class variable on ldap object field
* ObjectClass : Use this annotation to attribute to a php entity class an ldapObjectClass
* Dn : Use this annotation to build the dn with twig syntaxe
* Sequence : Use this annotation to define a link with an ldap Sequence Object
* ArrayField : This annotation defines an attribute is multi-valued as an array
* DnPregMatch : This annotation calculates the value of attribute with a regular expression on DN

After you can use entity like this example:

```php
$a = new Account();
$a->setUid('john.doo');
$a->setFirstname('John');
$a->setLastname('Doo');
$a->setAlias(array('jdoo','j.doo'));
$em = $this->get('l3_ldap_orm.entity_manager');
$em->persist($a);
$em->flush();

$repo = $em->getRepository('AppBundle\Entity\Account');
$a = $repo->findOneByUid('john.doo');
```

you also can set complex request ldap :
```php
$conditions = Array();
$not = true;
$conditions[] = new Condition('sn', 'Hetru');
$conditions[] = new Condition('sn', 'Bomy', $not);
        
$query = new Query(Query::CAND); 
$query->cAnd($conditions);
        
$em = $this->get('l3_ldap_orm.entity_manager');
$repo = $em->getRepository('AppBundle\Entity\Account');
$a = $repo->findByQuery($query);
```


Advanced Usage
--------------

On can use the @DnLinkArray annotation to map a field and an other ldap object

```php
namespace AppBundle\Entity;

use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Attribute;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Dn;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\Sequence;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\ArrayField;
use L3\Bundle\LdapOrmBundle\Annotation\Ldap\DnPregMatch;

/**
 * Class for represent Groups
 *
 * @ObjectClass("groupOfNames")
 * @Dn("cn={{ entity.name }},{% for entite in entity.entities %}ou={{ entite }},{% endfor %}{{ baseDN }}")
 */
class Group
{
    /**
     * @Attribute("cn")
     */
    private $name;

    /**
     * @Attribute("gidnumber")
     * @Sequence("cn=gidSequence,ou=sequences,ou=gram,{{ baseDN }}")
     */
    private $id;

    /**
     * @Attribute("member")
     * @DnLinkArray("AppBundle\Entity\Account")
     */
    private $members;

    /**
     * @DnPregMatch("/ou=([a-zA-Z0-9\.]+)/")
     */
    private $entities = array('groups');

    /**
     * Set the name of the group
     *
     * @param string $name the name of the group
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set members of the groups
     *
     * @param string $members the name of the group
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * Set the id of the group
     *
     * @param integer $id the id of the group
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add a member to the group
     *
     * @param Account $member the mmeber to add
     */
    public function addMember($member)
    {
        $this->members[] = $member;
    }

    /**
     * Remove a member to the group
     *
     * @param Account $member the mmeber to remove
     */
    public function removeMember($member)
    {
        foreach($this->members as $key => $memberAccount)
        {
            if($memberAccount->compare($member) == 0) {
                $this->members[$key] = null;
            }
        }
        $members = array_filter($this->members);
        $this->members = $members;
    }

    /**
     * Return the Entities of group
     *
     * @param array $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * Return the name of the group
     *
     * @return string name of the group
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the id of the group
     *
     * @return integer id of the group
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the members of the group
     *
     * @return array of object Accounts representing the of the group
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Return the name of the group
     *
     * @return string name of the group
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
```

After you can use the entity like this example:

```php
$a = new Account();
$a->setUid('john.doo');
$a->setFirstname('John');
$a->setLastname('Doo');
$a->setAlias(array('jdoo','j.doo'));
$em = $this->get('l3_ldap_orm.entity_manager');
$em->persist($a);
$em->flush();

$g = new Group();
$g->setName('Administrators');
$g->addMember($a);
$em->persist($g);
$em->flush();

$repo = $em->getRepository('AppBundle\Entity\Account');
$a = $repo->findOneByUid('john.doo');

/* Retreve all group of $a */
$groupRepository = $em->getRepository('AppBundle\Entity\Group');
$groups = $groupRepository->findByMember($a);
```

Included Features
-----------------

* manage ldap entity
  * persist entity
  * delete entity
  * retrieve entity
* Find in ldap by Attribute
* Find in ldap by References
