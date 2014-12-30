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
    use DereferencesIncludes;

    const ERROR_ROOT_SCHEMA_VALUE = "The root section \"schemas\" have an unsupported item.",
        ERROR_UNEXPECTED_VALUE = "An unexpected value was found when parsing the RAML.",
        ERROR_INCLUDED_FILE_TYPE = "The included file path is neither JSON nor YAML.";

    /**
     * @var JsonCoder
     */
    private $jsonCoder;
    /**
     * @var MapCollectionParser
     */
    private $mapCollectionParser;

    /**
     * @param JsonCoder $jsonCoder
     * @param MapCollectionParser $mapCollectionParser
     */
    public function __construct(
        JsonCoder $jsonCoder,
        MapCollectionParser $mapCollectionParser
    )
    {
        $this->jsonCoder = $jsonCoder;
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
