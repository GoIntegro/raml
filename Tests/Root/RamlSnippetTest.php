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

class RamlSnippetTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyingSnippet()
    {
        /* Given... (Fixture) */
        $snippet = new RamlSnippet([], "", []);
        /* When... (Action) */
        $node = $snippet->apply([]);
        /* Then... (Assertions) */
        $this->assertEquals([], $node);
    }
}
