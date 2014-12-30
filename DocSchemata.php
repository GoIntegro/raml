<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

/**
 * @see http://raml.org/spec.html#schemas
 */
class DocSchemata
{
    /**
     * @var array
     */
    private $schemaMaps = [];

    /**
     * @param array $map
     * @return self
     */
    public function addSchemaMap(array $map)
    {
        $this->schemaMaps[] = $map;
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function hasNamedSchema($name)
    {
        foreach ($this->schemaMaps as $map) {
            foreach ($map as $key => $schema) {
                if ($key === $name) return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function getNamedSchema($name)
    {
        foreach ($this->schemaMaps as $map) {
            foreach ($map as $key => $schema) {
                if ($key === $name) return $schema;
            }
        }

        return NULL;
    }
}
