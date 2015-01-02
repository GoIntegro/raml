<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

class Map implements \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param array $map
     */
    public function __construct(array $map)
    {
        foreach ($map as $key => $item) $this->add($key, $item);
    }

    /**
     * JSON schemas will be objects, resource types and traits arrays.
     *
     * The reason behind this is the JSON schema library, which expects a
     * schema given to the match() method to be an instance of stdClass.
     * @param string $key
     * @param array|\stdClass $item
     * @return self
     */
    public function add($key, $item)
    {
        $this->items[$key] = $item;
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
