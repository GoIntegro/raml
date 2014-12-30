<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

abstract class Map implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param array $item
     * @return self
     */
    public function add(array $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function has($name)
    {
        foreach ($this->items as $key => $item) {
            if ($key === $name) return TRUE;
        }

        return FALSE;
    }

    /**
     * @param string $name
     * @return \stdClass|NULL
     */
    public function get($name)
    {
        foreach ($this->items as $key => $item) {
            if ($key === $name) return $item;
        }

        return NULL;
    }

    /**
     * @see \Countable::count
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
