<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

class RamlSnippet
{
    /**
     * @var string
     * @see http://raml.org/spec.html#usage
     */
    public $usage;
    /**
     * @var array (Without the usage.)
     */
    public $source;
    /**
     * @var array
     */
    public $params;

    /**
     * @param array $source
     * @param string $usage
     * @param array $params
     */
    public function __construct(array $source, $usage = NULL, $params = [])
    {
        $this->source = $source;
        $this->usage = $usage;
        $this->params = $params;
    }

    /**
     * @param array $node
     * @return array
     */
    public function apply(array $node)
    {
        return array_merge_recursive($this->source, $node);
    }
}
