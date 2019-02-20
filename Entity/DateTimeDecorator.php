<?php
namespace L3\Bundle\LdapOrmBundle\Entity;

/**
 * Class used to Decorate DateTime objects (and permits them to be printed)
 *
 * @author Eric Bourderau <eric.bourderau@soce.fr>
 * @category API
 * @package  GramApiServerBundle
 * @license  GNU General Public License
 */
class DateTimeDecorator
{
    protected $_instance;
    
    private $format = 'YmdHisO'; // default for ldap

    public function __toString() {
        return $this->_instance->format($this->format);
    }
    
    public function setFormat($format) {
        $this->format = $format;
    }

    /**
     * Decorator of a DateTime object (adding __toString() and setFormat method)
     * @param  string|DateTime  $datetime
     */
    public function __construct($datetime) {
        if ($datetime instanceof \DateTime) {
            $this->_instance = $datetime;
        }
        else {
            $this->_instance = new \DateTime($datetime);
        }
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->_instance, $method), $args);
    }

    public function __get($key) {
        return $this->_instance->$key;
    }

    public function __set($key, $val) {
        return $this->_instance->$key = $val;
    }
}
