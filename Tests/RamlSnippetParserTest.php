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
    public function testParsingRamlSnippet()
    {
        /* Given... (Fixture) */
        $rawSnippet = [
            'usage' => "Apply this to any method that needs to be secured",
            'description' => "Some requests require authentication",
            'queryParameters' => [
                '<<methodName>>' => [
                    'description' => "A <<methodName>> name-value pair must be provided for this request to succeed.",
                    'example' => "<<methodName>>=h8duh3uhhu38"
                ]
            ]
        ];
        $parser = new RamlSnippetParser;
        /* When... (Action) */
        $ramlSnippet = $parser->parse($rawSnippet);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\Root\\RamlSnippet', $ramlSnippet
        );
        $expected = [
            'key' => [
                '<<methodName>>' => [
                    ['queryParameters']
                ]
            ],
            'value' => [
                '<<methodName>>' => [
                    ['queryParameters', '<<methodName>>', 'description'],
                    ['queryParameters', '<<methodName>>', 'example']
                ]
            ]
        ];
        $this->assertEquals($expected, $ramlSnippet->params);
    }
}
