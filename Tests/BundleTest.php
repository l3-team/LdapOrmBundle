<?php
class BundleTest extends PHPUnit_Framework_TestCase {
	// Ensure that the L3LdapOrmBundle.php file contains the L3LdapOrmBundle class
	public function testBundleFileExists () {
		$this->assertFileExists (__DIR__ . '/../L3LdapOrmBundle.php');
	}

	public function testBundleFileContainsBundleClass () {
		$className = 'L3\\Bundle\\LdapOrmBundle\\L3LdapOrmBundle';

		// Attempt to load the class without loading the file
		$this->assertFalse (class_exists ($className, FALSE));

		// Attempt to load the class after loading the file
		require_once __DIR__ . '/../L3LdapOrmBundle.php';
		$this->assertTrue (class_exists ($className, FALSE));
	}

	public function testBundleClassIsABundle () {
		$bundle = new L3\Bundle\LdapOrmBundle\L3LdapOrmBundle();
		$this->assertTrue (is_a ($bundle, 'Symfony\Component\HttpKernel\Bundle\Bundle'));
	}
}
