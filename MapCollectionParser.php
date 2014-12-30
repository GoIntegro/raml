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

class MapCollectionParser
{
    use DereferencesIncludes;

    const ERROR_ROOT_SCHEMA_VALUE = "The root section \"schemas\" have an unsupported item.",
        ERROR_UNEXPECTED_VALUE = "An unexpected value was found when parsing the RAML.";

    /**
     * @var JsonCoder
     * @see DereferencesIncludes::dereferenceInclude
     */
    private $jsonCoder;
    /**
     * @var string
     */
    private $fileDir;

    /**
     * @param JsonCoder $jsonCoder
     */
    public function __construct(JsonCoder $jsonCoder)
    {
        $this->jsonCoder = $jsonCoder;
    }

    /**
     * @param array $rawRaml
     * @param RamlDoc $ramlDoc
     * @return self
     * @throws \ErrorException
     */
    public function parse(array $raw, RamlDoc $ramlDoc)
    {
        $collection = new Root\MapCollection;

        foreach ($raw as $map) {
            if (is_array($map)) {
                $this->dereferenceIncludes($map, $ramlDoc->fileDir);
            } elseif (is_string($map) && RamlDoc::isInclude($value)) {
                $this->dereferenceInclude($map, $ramlDoc->fileDir);
            } else {
                throw new \ErrorException(self::ERROR_ROOT_SCHEMA_VALUE);
            }

            $collection->add($map);
        }

        return $collection;
    }
}
