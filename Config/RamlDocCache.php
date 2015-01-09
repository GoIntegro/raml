<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Hateoas\Config;

// RAML.
use GoIntegro\Raml\RamlDoc;

interface RamlDocCache
{
    /**
     * @return boolean
     */
    public function isFresh();

    /**
     * @return RamlDoc $doc
     * @return self
     */
    public function keep(RamlDoc $doc);

    /**
     * @return RamlDoc
     */
    public function read();
}
