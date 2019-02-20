<?php
namespace L3\Bundle\LdapOrmBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {
	public function registerBundles() {
		return array (
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\MonologBundle\MonologBundle(),
                        new \Symfony\Bundle\TwigBundle\TwigBundle(),
			new \L3\Bundle\LdapOrmBundle\L3LdapOrmBundle(),
		);
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load(__DIR__.'/config/minimal.yml');
	}
}
