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
        $ramlDoc->rawRaml = self::apply($ramlDoc, $ramlDoc->rawRaml);

        return $this;
    }

    /**
     * Actually applies resource types and traits.
     * @param RamlDoc $ramlDoc
     * @param array &$item
     * @throws \ErrorException
     * @see http://raml.org/spec.html#resource-types-and-traits
     */
    private static function apply(
        RamlDoc $ramlDoc, array &$item, $itemKey = NULL
    )
    {
        $applyTraits = function(&$item, array $traits) use ($ramlDoc) {
            foreach ($traits as $traitName) {
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
        };

        if (RamlDoc::isValidMethod($itemKey) && !empty($item['is'])) {
            $applyTraits($item, $item['is']);
        } elseif (RamlDoc::isResource($itemKey)) {
            if (!empty($item['type'])) {
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
            } elseif (!empty($item['is'])) {
                foreach ($item as $key => &$value) {
                    if (RamlDoc::isValidMethod($key)) {
                        $applyTraits($value, $item['is']);
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
}
