<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

// Mocks.
use Codeception\Util\Stub;
// YAML.
use Symfony\Component\Yaml\Yaml;

class MapCollectionParserTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/Resources/raml/some-resources.raml';

    public function testParsingMapCollection()
    {
        /* Given... (Fixture) */
        $jsonCoder = Stub::makeEmpty('GoIntegro\\Json\\JsonCoder');
        $ramlDoc = Stub::makeEmpty(
            'GoIntegro\\Raml\\RamlDoc',
            [
                'rawRaml' => Yaml::parse(__DIR__ . self::RAML_PATH)
            ]
        );
        $parser = new MapCollectionParser($jsonCoder);
        /* When... (Action) */
        $mapCollection = $parser->parse([], $ramlDoc);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\Root\\MapCollection', $mapCollection
        );
    }
}
