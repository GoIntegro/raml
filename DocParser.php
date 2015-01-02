<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

// YAML.
use Symfony\Component\Yaml\Yaml;
// JSON.
use GoIntegro\Json\JsonCoder;

class DocParser
{
    const ERROR_ROOT_SCHEMA_VALUE = "The root section \"schemas\" have an unsupported item.";

    /**
     * @var MapCollectionParser
     */
    private $mapCollectionParser;

    /**
     * @param MapCollectionParser $mapCollectionParser
     */
    public function __construct(MapCollectionParser $mapCollectionParser)
    {
        $this->mapCollectionParser = $mapCollectionParser;
    }

    /**
     * @param string $filePath
     * @return RamlDoc
     */
    public function parse($filePath)
    {
        $rawRaml = Yaml::parse($filePath);
        $ramlDoc = new RamlDoc($rawRaml, $filePath);

        foreach (['schemas', 'resourceTypes', 'traits'] as $key) {
            if (isset($rawRaml[$key])) {
                $ramlDoc->$key = $this->mapCollectionParser->parse(
                    $rawRaml[$key], $ramlDoc
                );
            }
        }

        return $ramlDoc;
    }

    /**
     * @param array $rawRaml
     * @return array
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    protected function applyResourceTypes(array $rawRaml)
    {
        // @todo Recursive.
    }

    /**
     * @param array $rawRaml
     * @return array
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    protected function applyTraits(array $rawRaml)
    {
        // @todo Recursive.
    }
}
