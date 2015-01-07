<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

// YAML.
use Symfony\Component\Yaml\Yaml;

class DocParser
{
    const ERROR_ROOT_SCHEMA_VALUE = "The root section \"schemas\" have an unsupported item.";

    /**
     * @var MapCollectionParser
     */
    private $mapCollectionParser;
    /**
     * @var DocExpander
     */
    private $docExpander;

    /**
     * @param MapCollectionParser $mapCollectionParser
     * @param DocExpander $docExpander
     */
    public function __construct(
        MapCollectionParser $mapCollectionParser,
        DocExpander $docExpander
    )
    {
        $this->mapCollectionParser = $mapCollectionParser;
        $this->docExpander = $docExpander;
    }

    /**
     * @param string $filePath
     * @return RamlDoc
     */
    public function parse($filePath)
    {
        $rawRaml = Yaml::parse($filePath);
        $ramlDoc = new RamlDoc($rawRaml, $filePath);

        foreach (RamlSpec::$rootLevelDeclarations as $key) {
            if (isset($rawRaml[$key])) {
                $ramlDoc->$key = $this->mapCollectionParser->parse(
                    $rawRaml[$key], $ramlDoc
                );
            }
        }

        $this->docExpander->expand($ramlDoc);

        return $ramlDoc;
    }
}
