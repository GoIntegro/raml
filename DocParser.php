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
use GoIntegro\Hateoas\Util\JsonCoder;

class DocParser
{
    use DereferencesIncludes;

    const ERROR_ROOT_SCHEMA_VALUE = "The root section \"schemas\" have an unsupported item.",
        ERROR_UNEXPECTED_VALUE = "An unexpected value was found when parsing the RAML.";

    /**
     * @var JsonCoder
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
     * @param string $filePath
     * @return RamlDoc
     */
    public function parse($filePath)
    {
        $rawRaml = Yaml::parse($filePath);
        $ramlDoc = new RamlDoc($rawRaml, $filePath);

        if (isset($rawRaml['schemas'])) {
            foreach ($rawRaml['schemas'] as $map) {
                if (is_array($map)) {
                    $this->dereferenceIncludes($map, $ramlDoc->fileDir);
                    $ramlDoc->addSchemaMap($map);
                } elseif (is_string($map)) {
                    // @todo Finish.
                } else {
                    throw new \ErrorException(self::ERROR_ROOT_SCHEMA_VALUE);
                }
            }
        }

        return $ramlDoc;
    }
}
