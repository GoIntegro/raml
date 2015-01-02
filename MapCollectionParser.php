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
     * @param mixed $rawRaml
     * @param RamlDoc $ramlDoc
     * @return self
     * @throws \ErrorException
     */
    public function parse($raw, RamlDoc $ramlDoc)
    {
        $collection = new Root\MapCollection;

        if (is_string($raw)) {
            if (RamlDoc::isInclude($raw)) {
                $raw = $this->dereferenceInclude($raw, $ramlDoc->fileDir);
            } else {
                throw new \ErrorException(self::ERROR_ROOT_SCHEMA_VALUE);
            }
        }

        foreach ($raw as $map) {
            if (is_array($map)) {
                $map = $this->dereferenceIncludes($map, $ramlDoc->fileDir);
            } elseif (is_string($map) && RamlDoc::isInclude($map)) {
                $map = $this->dereferenceInclude($map, $ramlDoc->fileDir);
            } else {
                throw new \ErrorException(self::ERROR_ROOT_SCHEMA_VALUE);
            }

            $collection->add($map);
        }

        return $collection;
    }

    /**
     * @param array &$map
     * @param string $fileDir
     * @return array
     */
    protected function dereferenceIncludes(array &$map, $fileDir = __DIR__)
    {
        foreach ($map as &$value) {
            if (is_string($value)) {
                if (RamlDoc::isInclude($value)) {
                    $value = $this->dereferenceInclude($value, $fileDir);
                } else {
                    throw new \ErrorException(
                        self::ERROR_UNEXPECTED_VALUE
                    );
                }
            }
        }

        return $map;
    }
}
