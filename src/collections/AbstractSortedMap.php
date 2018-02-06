<?php
namespace onix\collections;

/**
 * Class AbstractSortedMap
 *
 * @property-read ComparatorInterface $comparator  The key comparison function
 * @property-read TreeNode $first The first element of the map
 * @property-read mixed $firstKey The first key of the map
 * @property-read mixed $firstValue The first value of the map
 * @property-read TreeNode $last The last element of the map
 * @property-read mixed $lastKey The last key of the map
 * @property-read mixed $lastValue The last value of the map
 * @property-read Iterator $keys The keys iterator
 * @property-read Iterator $values The values iterator
 * @property-read integer $count The number of elements in the map
 */
abstract class AbstractSortedMap implements SortedMapInterface
{
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
            case 'firstKey':
                return $this->firstKey();
                break;
            case 'lastKey':
                return $this->lastKey();
                break;
            case 'firstValue':
                return $this->firstValue();
                break;
            case 'lastValue':
                return $this->lastValue();
                break;
            case 'first':
                return $this->first();
                break;
            case 'last':
                return $this->last();
                break;
            case 'keys':
                return $this->keys();
                break;
            case 'values':
                return $this->values();
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
     * Get the first key or throw an exception if there is no element
     * @return  mixed  The first key
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function firstKey()
    {
        return $this->first()->key;
    }

    /**
     * Get the first value or throw an exception if there is no element
     * @return  mixed  The first value
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function firstValue()
    {
        return $this->first()->value;
    }

    /**
     * Get the last key or throw an exception if there is no element
     * @return  mixed  The last key
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function lastKey()
    {
        return $this->last()->key;
    }

    /**
     * Get the last value or throw an exception if there is no element
     * @return  mixed  The last value
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function lastValue()
    {
        return $this->last()->value;
    }

    /**
     * Returns the greatest key lesser than the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no lower element
     */
    public function lowerKey($key)
    {
        return $this->lower($key)->key;
    }

    /**
     * Returns the value whose key is the greatest key lesser than the given key or throw an exception
     * if there is no such key
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found value
     * @throws  \OutOfBoundsException  If there is no lower element
     */
    public function lowerValue($key)
    {
        return $this->lower($key)->value;
    }

    /**
     * Returns the greatest key lesser than or equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no floor element
     */
    public function floorKey($key)
    {
        return $this->floor($key)->key;
    }

    /**
     * Returns the value whose key is the greatest key lesser than or equal to the given key or
     * throw an exception if there is no such key
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found value
     * @throws  \OutOfBoundsException  If there is no floor element
     */
    public function floorValue($key)
    {
        return $this->floor($key)->value;
    }

    /**
     * Returns the key equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no such element
     */
    public function findKey($key)
    {
        return $this->find($key)->key;
    }

    /**
     * Returns the value whose key equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found value
     * @throws  \OutOfBoundsException  If there is no such element
     */
    public function findValue($key)
    {
        return $this->find($key)->value;
    }

    /**
     * Returns the lowest key greater than or equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no ceiling element
     */
    public function ceilingKey($key)
    {
        return $this->ceiling($key)->key;
    }

    /**
     * Returns the value whose key is the lowest key greater than or equal to the given key or
     * throw an exception if there is no such key
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found value
     * @throws  \OutOfBoundsException  If there is no ceiling element
     */
    public function ceilingValue($key)
    {
        return $this->ceiling($key)->value;
    }

    /**
     * Returns the lowest key greater than to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no higher element
     */
    public function higherKey($key)
    {
        return $this->higher($key)->key;
    }

    /**
     * Returns the value whose key is the lowest key greater than to the given key or
     * throw an exception if there is no such key
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found value
     * @throws  \OutOfBoundsException  If there is no higher element
     */
    public function higherValue($key)
    {
        return $this->higher($key)->value;
    }

    /**
     * Keys iterator
     * @return Iterator The keys iterator
     */
    public function keys()
    {
        return Iterator::keys($this);
    }

    /**
     * Values iterator
     * @return  Iterator  The values iterator
     */
    public function values()
    {
        return Iterator::values($this);
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
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Create an iterator
     * @return  Iterator  A new iterator
     */
    public function getIterator()
    {
        return Iterator::create($this);
    }

    /**
     * Get the value for a key
     * @param   mixed  $key  The key
     * @return  mixed  The found value
     * @throws  \OutOfRangeException  If there is no such element
     */
    public function offsetGet($key)
    {
        try {
            return $this->find($key)->value;
        } catch (\OutOfBoundsException $e) {
            throw new \OutOfRangeException('Undefined offset');
        }
    }

    /**
     * Test the existence of a key
     * @param   mixed  $key  The key
     * @return  boolean  TRUE if the key exists, false otherwise
     */
    public function offsetExists($key)
    {
        try {
            return (bool) $this->find($key);
        } catch (\OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * Set the value for a key
     * @param   mixed  $key    The key
     * @param   mixed  $value  The value
     * @return  void
     * @throws  \RuntimeException  The operation is not supported by this class
     */
    public function offsetSet($key, $value)
    {
        throw new \RuntimeException('Unsupported operation');
    }

    /**
     * Unset the existence of a key
     * @param   mixed  $key  The key
     * @return  void
     * @throws  \RuntimeException  The operation is not supported by this class
     */
    public function offsetUnset($key)
    {
        throw new \RuntimeException('Unsupported operation');
    }

    /**
     * Returns the key for its hashed form.
     * @param string $hash Hashed key.
     * @return mixed Key.
     * @throws \RuntimeException If the hashed key is not in the map.
     */
    public function key($hash)
    {
        throw new \RuntimeException('Unsupported operation');
    }
}
