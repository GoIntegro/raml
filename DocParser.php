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

        // @todo Resource types. Encapsulate.
        foreach ($ramlDoc->getResources() as $resource) {
            $key = '/' . $resource;

            if (!empty($rawRaml[$key]['type'])) {
                $typeName = $rawRaml[$key]['type'];

                if ($ramlDoc->resourceTypes->has($typeName)) {
                    $typeRaml = $ramlDoc->resourceTypes->get($typeName);
                    $rawRaml[$key] = array_merge_recursive(
                        $rawRaml[$key], $typeRaml
                    );
                } else {
                    // @todo Exception.
                }
            }
        }

        // @todo Traits for resources. Encapsulate.
        foreach ($ramlDoc->getResources() as $resource) {
            $key = '/' . $resource;

            if (!empty($rawRaml[$key]['is'])) {
                foreach ($rawRaml[$key]['is'] as $traitName) {
                    if ($ramlDoc->traits->has($traitName)) {
                        $traitRaml = $ramlDoc->traits->get($traitName);
                        $rawRaml[$key] = array_merge_recursive(
                            $rawRaml[$key], $traitRaml
                        );
                    } else {
                        // @todo Exception.
                    }
                }
            }

            // @todo Traits for methods. Encapsulate.
            foreach (array_keys($rawRaml[$key]) as $property) {
                if (
                    RamlDoc::isValidMethod($property)
                    && !empty($rawRaml[$key][$property]['is'])
                ) {
                    foreach ($rawRaml[$key][$property]['is'] as $traitName) {
                        if ($ramlDoc->traits->has($traitName)) {
                            $traitRaml = $ramlDoc->traits->get($traitName);

                            $rawRaml[$key][$property] = array_merge_recursive(
                                $rawRaml[$key][$property], $traitRaml
                            );
                        } else {
                            // @todo Exception.
                        }
                    }
                }
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
