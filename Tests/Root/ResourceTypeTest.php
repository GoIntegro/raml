<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

// Mocks.
use Codeception\Util\Stub;
// YAML.
use Symfony\Component\Yaml\Yaml;

class ResourceTypeTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/../Resources/raml/snippets.raml';

    public function testApplyingSnippet()
    {
        /* Given... (Fixture) */
        $raml = Yaml::parse(__DIR__ . self::RAML_PATH);
        $node = $raml['/books'];
        $snippet = $raml['resourceTypes'][0]['searchableCollection'];
        $snippet = new ResourceType('searchableCollection', $snippet);
        /* When... (Action) */
        $actual = $snippet->apply('/books', $node);
        $expected = [
            'get' => [
                'queryParameters' => [
                    'title' => [
                        'description' => "Return books that have their title matching the given value"
                    ],
                    'digest_all_fields' => [
                        'description' => "If no values match the value given for title, use digest_all_fields instead"
                    ]
                ],
                'is' => [[
                    'secured' => ['tokenName' => 'access_token']
                ], [
                    'paginated' => ['maxPages' => 10]
                ]]
            ],
            'type' => [
                'searchableCollection' => [
                    'queryParamName' => 'title',
                    'fallbackParamName' => 'digest_all_fields'
                ]
            ]
        ];
        /* Then... (Assertions) */
        $this->assertEquals($expected, $actual);
    }
}
