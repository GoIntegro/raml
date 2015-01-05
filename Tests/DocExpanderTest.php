<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

// Mocks.
use Codeception\Util\Stub;

class DocExpanderTest extends \PHPUnit_Framework_TestCase
{
    const RAML_PATH = '/Resources/raml/some-resources.raml';

    public function testParsingRamlDoc()
    {
        /* Given... (Fixture) */
        $ramlDoc = Stub::makeEmpty(
            'GoIntegro\\Raml\\RamlDoc',
            ['rawRaml' => []]
        );
        $expander = new DocExpander;
        /* When... (Action) */
        $expander = $expander->expand($ramlDoc);
        /* Then... (Assertions) */
        $this->assertInstanceOf(
            'GoIntegro\\Raml\\DocExpander', $expander
        );
    }
}
