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

class DocExpanderTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/Resources/raml/some-resources.raml';

    public function testExpandingRamlDoc()
    {
        /* Given... (Fixture) */
        $collection = Stub::makeEmpty(
            'GoIntegro\\Raml\\Root\\MapCollection',
            [
                'has' => TRUE,
                'get' => function ($name) {
                    return Stub::makeEmpty(
                        'GoIntegro\\Raml\\Root\\RamlSnippet',
                        ['apply' => [$name => TRUE]]
                    );
                }
            ]
        );
        $ramlDoc = Stub::makeEmpty(
            'GoIntegro\\Raml\\RamlDoc',
            [
                'rawRaml' => Yaml::parse(__DIR__ . self::RAML_PATH),
                'resourceTypes' => $collection,
                'traits' => $collection
            ]
        );
        $expander = new DocExpander;
        /* When... (Action) */
        $expander = $expander->expand($ramlDoc);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\DocExpander', $expander
        );
        $this->assertTrue(
            $ramlDoc->rawRaml['/some-resources']['get']['searchable']
        );
        $this->assertTrue(
            $ramlDoc->rawRaml['/some-other-resources']['get']['secured']
        );
        $this->assertTrue(
            $ramlDoc->rawRaml['/some-other-resources']['post']['secured']
        );
        $this->assertTrue(
            $ramlDoc->rawRaml['/some-other-resources']['/{some-resources-ids}']['collection']
        );
    }
}
