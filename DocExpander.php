<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

class DocExpander
{
    const ERROR_UNKNOWN_TRAIT = "The trait \"%s\" is unknown.",
        ERROR_UNKNOWN_RESOURCE_TYPE = "The resource type \"%s\" is unknown.";

    /**
     * Applies resource types and traits.
     * @param RamlDoc $ramlDoc
     * @throws \ErrorException
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    public function expand(RamlDoc $ramlDoc)
    {
        $ramlDoc->rawRaml = self::applyAllToTree($ramlDoc, $ramlDoc->rawRaml);

        return $this;
    }

    /**
     * Actually applies resource types and traits.
     * @param RamlDoc $ramlDoc
     * @param array &$item
     * @throws \ErrorException
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    private static function applyAllToTree(
        RamlDoc $ramlDoc, array &$item, $itemKey = NULL
    )
    {
        if (
            RamlDoc::isValidMethod($itemKey)
            && !empty($item[RamlSpec::PROPERTY_APPLY_TRAITS])
        ) {
            $item = self::applyTraitsToNode(
                $ramlDoc, $item, $item[RamlSpec::PROPERTY_APPLY_TRAITS]
            );
        } elseif (RamlDoc::isResource($itemKey)) {
            if (!empty($item[RamlSpec::PROPERTY_APPLY_RESOURCE_TYPE])) {
                $item = self::applyResourceTypeToNode($ramlDoc, $item);
            }

            if (!empty($item[RamlSpec::PROPERTY_APPLY_TRAITS])) {
                foreach ($item as $key => &$value) {
                    if (RamlDoc::isValidMethod($key)) {
                        $value = self::applyTraitsToNode(
                            $ramlDoc,
                            $value,
                            $item[RamlSpec::PROPERTY_APPLY_TRAITS]
                        );
                    }
                }
            }
        }

        foreach ($item as $key => &$value) {
            if (
                (RamlDoc::isValidMethod($key) || RamlDoc::isResource($key))
                && !empty($value)
            ) {
                $value = call_user_func(__METHOD__, $ramlDoc, $value, $key);
            }
        }

        return $item;
    }

    /**
     * @param RamlDoc $ramlDoc
     * @param array &$item
     * @return array
     * @throws \ErrorException
     * @see http://raml.org/spec.html#usage
     */
    public static function applyResourceTypeToNode(
        RamlDoc $ramlDoc, array &$item
    )
    {
        $typeName = $item[RamlSpec::PROPERTY_APPLY_RESOURCE_TYPE];

        if ($ramlDoc->resourceTypes->has($typeName)) {
            $typeRaml = $ramlDoc->resourceTypes->get($typeName);
            $item = $typeRaml->apply($item);
        } else {
            $message = sprintf(
                self::ERROR_UNKNOWN_RESOURCE_TYPE, $typeName
            );
            throw new \ErrorException($message);
        }

        return $item;
    }

    /**
     * @param RamlDoc $ramlDoc
     * @param array &$item
     * @param array $traits
     * @return array
     * @throws \ErrorException
     * @see http://raml.org/spec.html#usage
     */
    private static function applyTraitsToNode(
        RamlDoc $ramlDoc, array &$item, array $traits
    )
    {
        foreach ($traits as $traitName) {
            if ($ramlDoc->traits->has($traitName)) {
                $traitRaml = $ramlDoc->traits->get($traitName);
                $item = $traitRaml->apply($item);
            } else {
                $message = sprintf(self::ERROR_UNKNOWN_TRAIT, $traitName);
                throw new \ErrorException($message);
            }
        }

        return $item;
    }
}
