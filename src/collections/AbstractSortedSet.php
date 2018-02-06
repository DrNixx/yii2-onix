<?php
namespace onix\collections;

/**
 * Class AbstractSortedSet
 *
 * @property-read  callable   $comparator  The element comparison function
 * @property-read  mixed      $first       The first element of the set
 * @property-read  mixed      $last        The last element of the set
 * @property-read  integer    $count       The number of elements in the set
 */
abstract class AbstractSortedSet implements SortedSetInterface
{
    /**
     * @var SortedMapInterface Underlying map
     */
    private $map;

    /**
     * Get the map
     * @return SortedMapInterface The underlying map
     */
    protected function getMap()
    {
        return $this->map;
    }

    /**
     * Set the map
     * @param   SortedMapInterface  $map  The underlying map
     * @return  AbstractSet  $this for chaining
     */
    protected function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Magic get method
     * @param   string  $property  The property
     * @throws  \RuntimeException  If the property does not exist
     * @return  mixed  The value associated to the property
     */
    public function __get($property)
    {
        switch ($property) {
            case 'comparator':
                return $this->comparator();
                break;
            case 'first':
                return $this->first();
                break;
            case 'last':
                return $this->last();
                break;
            case 'count':
                return $this->count();
                break;
            default:
                throw new \RuntimeException('Undefined property');
                break;
        }
    }

    /**
     * Get the comparator
     *
     * @return ComparatorInterface The comparator
     */
    public function comparator()
    {
        return $this->map->comparator();
    }

    /**
     * Get the first element or throw an exception if there is no such element
     *
     * @return  mixed  The first element
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function first()
    {
        return $this->map->firstKey();
    }

    /**
     * Get the last element or throw an exception if there is no such element
     * @return  mixed  The last element
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function last()
    {
        return $this->map->lastKey();
    }

    /**
     * Returns the greatest element lesser than the given element or throw an exception if there is no such element
     * @param   mixed  $element  The searched element
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no lower element
     */
    public function lower($element)
    {
        return $this->map->lowerKey($element);
    }

    /**
     * Returns the greatest element lesser than or equal to the given element or throw an
     * exception if there is no such element
     * @param   mixed  $element  The searched element
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no floor element
     */
    public function floor($element)
    {
        return $this->map->floorKey($element);
    }

    /**
     * Returns the element equal to the given element or throw an exception if there is no such element
     * @param   mixed  $element  The searched element
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no such element
     */
    public function find($element)
    {
        return $this->map->findKey($element);
    }

    /**
     * Returns the lowest element greater than or equal to the given element or throw an exception
     * if there is no such element
     * @param   mixed  $element  The searched element
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no ceiling element
     */
    public function ceiling($element)
    {
        return $this->map->ceilingKey($element);
    }

    /**
     * Returns the lowest element greater than to the given element or throw an exception if there is no such element
     * @param   mixed  $element  The searched element
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no higher element
     */
    public function higher($element)
    {
        return $this->map->higherKey($element);
    }

    /**
     * Convert the object to a string
     * @return  string  String representation of the object
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert the object to an array
     * @return  array  Array representation of the object
     */
    public function toArray()
    {
        $array = [];

        foreach ($this as $value) {
            $array[] = $value;
        }

        return $array;
    }

    /**
     * Create an iterator
     * @return  Iterator  A new iterator
     */
    public function getIterator()
    {
        return Iterator::keys($this->map);
    }

    /**
     * Get the value for an element
     * @param   mixed  $element  The element
     * @return  mixed  The found value
     */
    public function offsetGet($element)
    {
        try {
            return (bool) $this->map->find($element);
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * Test the existence of an element
     * @param   mixed  $element  The element
     * @return  boolean  TRUE if the element exists, false otherwise
     */
    public function offsetExists($element)
    {
        return $this->offsetGet($element);
    }

    /**
     * Set the value for an element
     *
     * @param   mixed  $element  The element
     * @param   mixed  $value    The value
     *
     * @return  void
     *
     * @throws  \RuntimeException  The operation is not supported by this class
     *
     * @since   1.0.0
     */
    public function offsetSet($element, $value)
    {
        throw new \RuntimeException('Unsupported operation');
    }

    /**
     * Unset the existence of an element
     *
     * @param   mixed  $element  The element
     *
     * @return  void
     *
     * @throws  \RuntimeException  The operation is not supported by this class
     *
     * @since   1.0.0
     */
    public function offsetUnset($element)
    {
        throw new \RuntimeException('Unsupported operation');
    }

    /**
     * Count the number of elements
     *
     * @return  integer
     *
     * @since   1.0.0
     */
    public function count()
    {
        return count($this->map);
    }
}
