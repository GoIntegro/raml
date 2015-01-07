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

class RamlSnippetParserTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/Resources/raml/snippets.raml';

    public function testParsingRamlSnippet()
    {
        /* Given... (Fixture) */
        $raml = Yaml::parse(__DIR__ . self::RAML_PATH);
        $rawSnippet = $raml['resourceTypes'][0]['searchableCollection'];
        $parser = new RamlSnippetParser;
        /* When... (Action) */
        $ramlSnippet = $parser->parse($rawSnippet);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\Root\\RamlSnippet', $ramlSnippet
        );
        $expected = [
            'key' => [
                '<<queryParamName>>' => [
                    ['get', 'queryParameters']
                ],
                '<<fallbackParamName>>' => [
                    ['get', 'queryParameters']
                ]
            ],
            'value' => [
                '<<queryParamName>>' => [
                    [
                        'get', 'queryParameters',
                        '<<queryParamName>>', 'description'
                    ],
                    [
                        'get', 'queryParameters',
                        '<<fallbackParamName>>', 'description'
                    ]
                ],
                '<<fallbackParamName>>' => [
                    [
                        'get', 'queryParameters',
                        '<<fallbackParamName>>', 'description'
                    ]
                ],
                '<<resourcePathName>>' => [
                    [
                        'get', 'queryParameters',
                        '<<queryParamName>>', 'description'
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $ramlSnippet->params);
    }
}
