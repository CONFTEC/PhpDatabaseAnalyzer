<?php
namespace PhpDatabaseAnalyzer;

/**
 *
 * @author fgluecks
 *
 */
class Config implements ConfigInterface
{

    private $xmlConfigObject;

    /**
     *
     * @param string $configFileName
     */
    public function __construct($configFileName)
    {
        $this->loadConfig($configFileName);
    }

    private function loadConfig($configFileName)
    {
        if (! is_file($configFileName)) {
            throw new \InvalidArgumentException("Config file does not exists", E_ERROR);
        } else {
            $this->xmlConfigObject = simplexml_load_file($configFileName);

            if (! is_object($this->xmlConfigObject) or $this->xmlConfigObject->getName() != 'phpDatabaseAnalyzer') {
                throw new \InvalidArgumentException("Invalid config file", E_ERROR);
            } else {
                return true;
            }
        }
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhpDatabaseAnalyzer\ConfigInterface::getOutputType()
     */
    public function getOutputType()
    {
        if (! isset($this->xmlConfigObject->outputType)) {
            throw new \RuntimeException("outputType does not exist in XML config", E_ERROR);
        } else {
            return $this->xmlConfigObject->outputType->__toString();
        }
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhpDatabaseAnalyzer\ConfigInterface::getDatabaseTestSuiteAsList()
     */
    public function getDatabaseTestSuiteAsList()
    {
        if (! isset($this->xmlConfigObject->databaseTestSuite)) {
            throw new \RuntimeException("databaseTestSuite does not exist in XML config", E_ERROR);
        } else {
            $databaseTestSuiteList = array();
            $positionKey = 0;

            foreach ($this->xmlConfigObject->databaseTestSuite as $databaseTestSuite) {
                $databaseTestSuiteList[] = $positionKey;
                $positionKey ++;
            }
            return $databaseTestSuiteList;
        }
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhpDatabaseAnalyzer\ConfigInterface::getConnectionParametersOfDatabaseTestSuite()
     */
    public function getConnectionParametersOfDatabaseTestSuite($positionInList)
    {
        if (! isset($this->xmlConfigObject->databaseTestSuite[$positionInList])) {
            throw new \RuntimeException("databaseTestSuite does not exist in XML config", E_ERROR);
        } else {
            $connectionParameter = array(
                'engine' => $this->xmlConfigObject->databaseTestSuite[$positionInList]['databaseEngine']->__toString(),
                'host' => $this->xmlConfigObject->databaseTestSuite[$positionInList]->connection->host->__toString(),
                'port' => $this->xmlConfigObject->databaseTestSuite[$positionInList]->connection->port->__toString(),
                'username' => $this->xmlConfigObject->databaseTestSuite[$positionInList]->connection->username->__toString(),
                'password' => $this->xmlConfigObject->databaseTestSuite[$positionInList]->connection->password->__toString(),
            );
            return $connectionParameter;
        }
    }
}
