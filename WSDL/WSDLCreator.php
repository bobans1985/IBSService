<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL;

use WSDL\Parser\ClassParser;
use WSDL\Service\Service;
use WSDL\XML\XMLGenerator;

class WSDLCreator
{
    private $_class;
    private $_location;
    /**
     * @var ClassParser
     */
    private $_classParser;
    private $_namespace = 'http://example.com/';

    public function __construct($class, $location)
    {
        $this->_class = $class;
        $this->_location = $location;
        $this->_parseClass();
    }

    private function _parseClass()
    {
        $this->_classParser = new ClassParser($this->_class);
        $this->_classParser->parse();
    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");
        $xml = new XMLGenerator($this->_class, $this->_namespace, $this->_location);
        $xml->setWSDLMethods($this->_classParser->getMethods())->generate();
        $xml->render();
    }

    public function setNamespace($namespace)
    {
        $namespace = $this->_addShlashAtEndIfNotExists($namespace);
        $this->_namespace = $namespace;
    }

    private function _addShlashAtEndIfNotExists($namespace)
    {
        return rtrim($namespace, '/') . '/';
    }

    public function renderWSDLService()
    {
        $headers = apache_request_headers();
        if (empty($headers['Content-Type']) || !preg_match('#xml#i', $headers['Content-Type'])) {
            $service = new Service($this->_class, $this->_classParser->getMethods(), $this->_namespace);
            $service->render();
        }
    }
}