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
        $path = __DIR__ . self::RAML_PATH;
        $ramlDoc = Stub::makeEmpty(
            'GoIntegro\\Raml\\RamlDoc',
            ['fileDir' => dirname($path), 'rawRaml' => Yaml::parse($path)]
        );
        $raw = [
            '!include some-traits.yml',
            ['secured' => '!include some-trait.yml'],
            [
                'paged' => [
                    'queryParameters' => [
                        'start' => ['type' => 'number']
                    ]
                ]
            ],
            [
                'searchable' => [
                    'queryParameters' => [
                        'query' => ['type' => 'string']
                    ]
                ]
            ]
        ];
        $parser = new MapCollectionParser($jsonCoder);
        /* When... (Action) */
        $mapCollection = $parser->parse($raw, $ramlDoc);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\Root\\MapCollection', $mapCollection
        );
    }
}
