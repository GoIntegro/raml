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

        $ramlDoc->rawRaml = self::expand($ramlDoc, $ramlDoc->rawRaml);

        return $ramlDoc;
    }

    const ERROR_UNKNOWN_TRAIT = "The trait \"%s\" is unknown.",
        ERROR_UNKNOWN_RESOURCE_TYPE = "The resource type \"%s\" is unknown.";

    /**
     * Applies resource types and traits.
     * @param RamlDoc $ramlDoc
     * @param array &$item
     * @throws \ErrorException
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    private static function expand(
        RamlDoc $ramlDoc, array &$item, $itemKey = NULL
    )
    {
        if (RamlDoc::isValidMethod($itemKey) && !empty($item['is'])) {
            foreach ($item['is'] as $traitName) {
                if ($ramlDoc->traits->has($traitName)) {
                    $traitRaml = $ramlDoc->traits->get($traitName);
                    $item = array_merge_recursive(
                        $item, $traitRaml
                    );
                } else {
                    $message = sprintf(self::ERROR_UNKNOWN_TRAIT, $traitName);
                    throw new \ErrorException($message);
                }
            }
        } elseif (RamlDoc::isResource($itemKey) && !empty($item['type'])) {
            $typeName = $item['type'];

            if ($ramlDoc->resourceTypes->has($typeName)) {
                $typeRaml = $ramlDoc->resourceTypes->get($typeName);
                $item = array_merge_recursive(
                    $item, $typeRaml
                );
            } else {
                $message = sprintf(
                    self::ERROR_UNKNOWN_RESOURCE_TYPE, $typeName
                );
                throw new \ErrorException($message);
            }
        }

        foreach ($item as $key => &$value) {
            if (
                (RamlDoc::isValidMethod($key) || RamlDoc::isResource($key))
                && !empty($value)
            ) {
                $value = call_user_func(__METHOD__, $ramlDoc, $value);
            }
        }

        return $item;
    }
}
