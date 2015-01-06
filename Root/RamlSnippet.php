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
    private $usage;
    /**
     * @var array (Without the usage.)
     */
    private $source;
    /**
     * @var array
     */
    private $parameters = [];
}
