<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

class MapCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $maps = [];

    /**
     * @param array $map
     * @return self
     */
    public function add(array $map)
    {
        $this->maps[] = new Map($map);
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function has($name)
    {
        foreach ($this->maps as $map) {
            if ($map->has($name)) return TRUE;
        }

        return FALSE;
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function get($name)
    {
        foreach ($this->maps as $map) {
            if ($map->has($name)) return $map->get($name);
        }

        return NULL;
    }

    /**
     * @see \Countable::count
     */
    public function count()
    {
        return count($this->maps);
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->maps);
    }
}
