<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

class RamlDoc
{
    /**
     * @var array Read-only.
     */
    public $rawRaml;
    /**
     * @var string
     */
    public $fileName;
    /**
     * @var string
     */
    public $fileDir;
    /**
     * @var Root\MapCollection
     */
    public $schemas;
    /**
     * @var Root\MapCollection
     */
    public $resourceTypes;
    /**
     * @var Root\MapCollection
     */
    public $traits;

    /**
     * @param array $rawRaml
     * @param string $fileName
     */
    public function __construct(array $rawRaml, $fileName)
    {
        $this->rawRaml = $rawRaml;
        $this->fileName = $fileName;
        $this->fileDir = dirname($fileName);
        $this->schemata = new Root\MapCollection;
        $this->resourceTypes = new Root\MapCollection;
        $this->traits = new Root\MapCollection;
    }

    /**
     * @param string $method
     * @return boolean
     */
    public static function isValidMethod($method)
    {
        return in_array($method, RamlSpec::$methods);
    }

    /**
     * @param string $mediaType
     * @return boolean
     */
    public static function isValidMediaType($mediaType)
    {
        return in_array($mediaType, RamlSpec::$mediaTypes);
    }

    /**
     * @param string $value
     * @return boolean
     */
    public static function isInclude($value)
    {
        return 0 === strpos($value, '!include ');
    }

    /**
     * @param string $value
     * @return boolean
     */
    public static function isResource($value)
    {
        return 0 === strpos($value, '/');
    }

    /**
     * @param string $value
     * @return boolean
     */
    public static function isParameter($value)
    {
        return self::isResource($value)
            && '{' === substr($value, 1, 1) && '}' === substr($value, -1);
    }

    /**
     * @param string $method
     * @param string $path
     * @return boolean
     */
    public function isDefined($method, $path)
    {
        $raml = $this->getPathDefinition($path);

        return array_key_exists($method, $raml);
    }

    /**
     * @param string $path
     * @param integer $case
     * @return array
     */
    public function getAllowedMethods($path, $case = CASE_LOWER)
    {
        $raml = $this->getPathDefinition($path);
        $methods = array_intersect(array_keys($raml), RamlSpec::$methods);

        if (CASE_UPPER === $case) {
            $callback = function($method) { return strtoupper($method); };
            $methods = array_map($callback, $methods);
        }

        return array_values($methods);
    }

    /**
     * @param string $path
     * @return array|NULL
     */
    public function getPathDefinition($path)
    {
        $raml = $this->rawRaml;

        foreach (explode('/', substr($path, 1)) as $part) {
            $resource = '/' . $part;
            $fullResource = '/' . strstr($path, $part);

            if (isset($raml[$resource])) {
                $raml = $raml[$resource];
            } else if (isset($raml[$fullResource])) {
                $raml = $raml[$fullResource];
            } else {
                foreach (array_keys($raml) as $key) {
                    if (static::isParameter($key)) {
                        $raml = $raml[$key];
                        break;
                    }
                }
            }
        }

        return $raml;
    }

    /**
     * @return array
     * @see http://raml.org/spec.html#resources-and-nested-resources
     */
    public function getResources()
    {
        $types = [];

        foreach ($this->rawRaml as $key => $value) {
            if ($this->isResource($key)) {
                $types[] = substr($key, 1);
            }
        }

        return $types;
    }
}
