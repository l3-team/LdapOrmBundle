<?php
namespace L3\Bundle\LdapOrmBundle\Builder;


use L3\Bundle\LdapOrmBundle\Builder\Method\AdderBuilder;
use L3\Bundle\LdapOrmBundle\Builder\Method\GetterBuilder;
use L3\Bundle\LdapOrmBundle\Builder\Method\MethodBuilder;
use L3\Bundle\LdapOrmBundle\Builder\Method\RemoverBuilder;
use L3\Bundle\LdapOrmBundle\Builder\Method\SetterBuilder;
use L3\Bundle\LdapOrmBundle\Manager\EntityAnalyzer;

class EntityBuilder
{
    private $className;
    /**
     * @var EntityAnalyzer
     */
    private $analyzer;

    public function __construct($className)
    {
        $this->className = $className;
        $this->analyzer = EntityAnalyzer::get($this->className);
    }

    public function completeEntity()
    {
        $missingMethod = $this->analyzer->listMissingMethod();

        $lines = file($this->analyzer->getReflection()->getFileName());

        $lineToSet = $lines[$this->analyzer->getReflection()->getEndLine() - 1];

        $endClassPos = strrpos($lineToSet, '}');

        $before = substr($lineToSet, 0, $endClassPos - 2);
        $after = substr($lineToSet, $endClassPos - 2);

        foreach ($missingMethod as $method) {
            $before .= $this->getBuilder($method)->getMethodSrc();
        }

        $lines[$this->analyzer->getReflection()->getEndLine() - 1] = $before . $after;

        file_put_contents($this->analyzer->getReflection()->getFileName(), implode('', $lines));
    }

    public function regenerateGetterSetter()
    {
        $methodList = $this->analyzer->listRequiredMethod();
        $fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());

        foreach ($methodList as $method) {
            $methodBuilder = $this->getBuilder($method);
            $fileContent = str_replace($methodBuilder->getMethodName(), $methodBuilder->getMethodSrc(), $fileContent);
        }

        file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
    }

    public function cleanGetterSetter()
    {
        $methodList = $this->analyzer->listRequiredMethod();
        $fileContent = file_get_contents($this->analyzer->getReflection()->getFileName());
        foreach ($methodList as $method) {
            $methodBuilder = $this->getBuilder($method);
            $fileContent = str_replace($methodBuilder->getMethodName(), '', $fileContent);
        }
        file_put_contents($this->analyzer->getReflection()->getFileName(), $fileContent);
    }

    protected function getMethodSrc($methodName, $file = null)
    {
        if (is_null($file)) {
            $file = file($this->analyzer->getReflection()->getFileName());
        }

        try {
            $startLine = $this->analyzer->getReflection()->getMethod($methodName)->getStartLine() - 1;
            $endLine = $this->analyzer->getReflection()->getMethod($methodName)->getEndLine() + 1;
        } catch (\ReflectionException $e) {
            return '';
        }
        $length = $endLine - $startLine;

        return implode('', array_slice($file, $startLine, $length));
    }

    /**
     * @param $method
     * @return MethodBuilder
     */
    protected function getBuilder($method)
    {
        switch ($method['type']) {
            case EntityAnalyzer::GETTER:
                return new GetterBuilder($method['column']);
            case EntityAnalyzer::SETTER:
                return new SetterBuilder($method['column']);
            case EntityAnalyzer::ADDER:
                return new AdderBuilder($method['column']);
            case EntityAnalyzer::REMOVER:
                return new RemoverBuilder($method['column']);
        }
    }
}