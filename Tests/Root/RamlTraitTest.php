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
        $paginated = $raml['traits'][0]['paginated'];
        $paginated = new RamlTrait('paginated', $paginated);
        /* When... (Action) */
        $actual = $secured->apply('get', $node);
        $actual = $paginated->apply('get', $actual);
        $expected = [
            'queryParameters' => [
                'access_token' => [
                    'description' => 'A valid access_token is required'
                ],
                'numPages' => [
                    'description' => 'The number of pages to return, not to exceed 10'
                ]
            ],
            'is' => [[
                'secured' => ['tokenName' => 'access_token']
            ], [
                'paginated' => ['maxPages' => 10]
            ]]
        ];
        /* Then... (Assertions) */
        $this->assertEquals($expected, $actual);
    }
}
