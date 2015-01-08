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

class RamlTraitTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/../Resources/raml/snippets.raml';

    public function testApplyingSnippet()
    {
        /* Given... (Fixture) */
        $raml = Yaml::parse(__DIR__ . self::RAML_PATH);
        $node = $raml['/books']['get'];
        $params = [
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
        $secured = $raml['traits'][0]['secured'];
        $secured = new RamlTrait('secured', $secured);
        $paginated = $raml['traits'][0]['secured'];
        $paginated = new RamlTrait('paginated', $paginated);
        /* When... (Action) */
        $actual = $secured->apply('get', $node);
        $actual = $paginated->apply('get', $actual);
        $expected = [
            'get' => [
                'queryParameters' => [
                    '<<queryParamName>>' => [
                        'description' => "Return <<resourcePathName>> that have their <<queryParamName>> matching the given value"
                    ],
                    '<<fallbackParamName>>' => [
                        'description' => "If no values match the value given for <<queryParamName>>, use <<fallbackParamName>> instead"
                    ]
                ],
                'is' => [[
                    'secured' => ['tokenName' => 'access_token']
                ], [
                    'paged' => ['maxPages' => 10]
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
