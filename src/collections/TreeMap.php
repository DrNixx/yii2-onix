<?php
namespace onix\collections;

use onix\exceptions\NullPointerException;
use onix\exceptions\UnsupportedOperationException;
use UnexpectedValueException;

/**
 * Class TreeMap
 *
 */
class TreeMap extends AbstractSortedMap
{
    /**
     * @var TreeNode Root of the tree
     */
    private $root;

    /**
     * Comparator.
     *
     * @var ComparatorInterface|null
     */
    private $comparator;

    /**
     * @param array|TreeMap $map
     * @param ComparatorInterface $comparator
     */
    public function __construct($map = [], ComparatorInterface $comparator = null)
    {
        if ($comparator === null) {
            $this->comparator = function ($key1, $key2) {
                return $key1 - $key2;
            };
        } else {
            $this->comparator = $comparator;
        }

        $this->putAll($map);
        $this->comparator = $comparator;
    }

    /**
     * Create
     * @param ComparatorInterface $comparator
     * @return  TreeMap  A new TreeMap
     */
    public static function create($comparator = null)
    {
        return new static([], $comparator);
    }

    /**
     * Get the comparator
     *
     * @return ComparatorInterface The comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * Get the first element or throw an exception if there is no such element
     * @return  mixed  The first element
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function first()
    {
        if ($this->root) {
            return $this->root->first;
        } else {
            throw new \OutOfBoundsException('First element unexisting');
        }
    }

    /**
     * Get the last element or throw an exception if there is no such element
     * @return  mixed  The last element
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function last()
    {
        if ($this->root) {
            return $this->root->last;
        } else {
            throw new \OutOfBoundsException('Last element unexisting');
        }
    }

    /**
     * Get the predecessor element or throw an exception if there is no such element
     * @param   TreeNode  $element  A tree node member of the underlying TreeMap
     * @return  mixed  The predecessor element
     * @throws  \OutOfBoundsException  If there is no predecessor
     */
    public function predecessor($element)
    {
        $predecessor = $element->predecessor;
        if ($predecessor) {
            return $predecessor;
        } else {
            throw new \OutOfBoundsException('Predecessor element unexisting');
        }
    }

    /**
     * Get the successor element or throw an exception if there is no such element
     * @param   TreeNode  $element  A tree node member of the underlying TreeMap
     * @return  mixed  The successor element
     * @throws  \OutOfBoundsException  If there is no successor
     */
    public function successor($element)
    {
        $successor = $element->successor;
        if ($successor) {
            return $successor;
        } else {
            throw new \OutOfBoundsException('Successor element unexisting');
        }
    }

    /**
     * Returns the element whose key is the greatest key lesser than the given key or
     * throw an exception if there is no such element
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no lower element
     */
    public function lower($key)
    {
        if ($this->root) {
            $lower = $this->root->find($key, $this->comparator, -2);
        } else {
            $lower = null;
        }

        if ($lower) {
            return $lower;
        } else {
            throw new \OutOfBoundsException('Lower element unexisting');
        }
    }

    /**
     * Returns the element whose key is the greatest key lesser than or equal to the given key or throw
     * an exception if there is no such element
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no floor element
     */
    public function floor($key)
    {
        if ($this->root) {
            $floor = $this->root->find($key, $this->comparator, -1);
        } else {
            $floor = null;
        }

        if ($floor) {
            return $floor;
        } else {
            throw new \OutOfBoundsException('Floor element unexisting');
        }
    }

    /**
     * Returns the element whose key is equal to the given key or throw an exception if there is no such element
     * @param   mixed  $key  The searched key
     * @return  TreeNode|null  The found element
     * @throws  \OutOfBoundsException  If there is no such element
     */
    public function find($key)
    {
        if ($this->root) {
            $find = $this->root->find($key, $this->comparator, 0);
        } else {
            $find = null;
        }

        if ($find) {
            return $find;
        } else {
            throw new \OutOfBoundsException('Element unexisting');
        }
    }

    /**
     * Returns the element whose key is the lowest key greater than or equal to the given key or
     * throw an exception if there is no such element
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no ceiling element
     */
    public function ceiling($key)
    {
        if ($this->root) {
            $ceiling = $this->root->find($key, $this->comparator, 1);
        } else {
            $ceiling = null;
        }

        if ($ceiling) {
            return $ceiling;
        } else {
            throw new \OutOfBoundsException('Ceiling element unexisting');
        }
    }

    /**
     * Returns the element whose key is the lowest key greater than to the given key or
     * throw an exception if there is no such element
     *
     * @param   mixed  $key  The searched key
     * @return  mixed  The found element
     * @throws  \OutOfBoundsException  If there is no higher element
     */
    public function higher($key)
    {
        if ($this->root) {
            $higher = $this->root->find($key, $this->comparator, 2);
        } else {
            $higher = null;
        }

        if ($higher) {
            return $higher;
        } else {
            throw new \OutOfBoundsException('Higher element unexisting');
        }
    }

    /**
     * Put values in the map
     *
     * @param mixed $key
     * @param mixed $value
     * @return TreeMap $this for chaining
     */
    public function put($key, $value)
    {
        $this[$key] = $value;
        return $this;
    }

    /**
     * Initialise the map
     * @param MapInterface|array $map Values to initialise the map
     * @return TreeMap $this for chaining
     */
    public function putAll($map)
    {
        foreach ($map as $key => $val) {
            $this->put($key, $val);
        }

        return $this;
    }

    /**
     * Clear the map
     * @return  TreeMap  $this for chaining
     */
    public function clear()
    {
        $this->root = null;
        return $this;
    }

    /**
     * Clone the map
     * @return  void
     */
    public function __clone()
    {
        if ($this->root !== null) {
            $root = $this->root;
            $this->root = null;
            $node = $root->first;

            while ($node !== null) {
                $this[$node->key] = $node->value;
                $node = $node->successor;
            }
        }
    }

    /**
     * Serialize the object
     * @return  array  Array of values
     */
    public function jsonSerialize()
    {
        $array = [];

        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }

        return ['TreeMap' => $array];
    }

    /**
     * Set the value for a key
     * @param   mixed  $key    The key
     * @param   mixed  $value  The value
     * @return  void
     */
    public function offsetSet($key, $value)
    {
        if ($this->root) {
            $this->root = $this->root->insert($key, $value, $this->comparator);
        } else {
            $this->root = TreeNode::create($key, $value);
        }
    }

    /**
     * Unset the existence of a key
     * @param   mixed  $key  The key
     * @return  void
     */
    public function offsetUnset($key)
    {
        if ($this->root) {
            $this->root = $this->root->remove($key, $this->comparator);
        }
    }

    /**
     * Count the number of key/value pairs
     * @return  integer
     */
    public function count()
    {
        if ($this->root) {
            return count($this->root);
        } else {
            return 0;
        }
    }

    /**
     * Returns `true` if this map contains no key-value mappings.
     * @return bool `true` if this map contains no key-value mappings, otherwise `false`.
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

    /**
     * Returns the value to which the key is mapped, or null if this map
     * contains no mapping for the key.
     *
     * If this map permits null values, then a return value of null does not
     * necessarily indicate that the map contains no mapping for the key; it's
     * also possible that the map explicitly maps the key to null. The
     * {@see containsKey} operation may be used to distinguish these two cases.
     *
     * @param mixed $key Key whose associated value is to be returned.
     * @return mixed|null Value to which the specified key is mapped, or null if there is no mapping for the key.
     *
     * @throws NullPointerException     If the key is null and this collection does not permit null keys (optional).
     * @throws UnexpectedValueException If the key is incompatible with this map (optional).
     */
    public function get($key)
    {
        try {
            return $this->findValue($key);
        } catch (\OutOfBoundsException $ex) {
            return null;
        }
    }

    /**
     * Removes the mapping for a key from this map if it is present.
     *
     * This is an optional operation.
     *
     * @param mixed $key Key whose mapping is to be removed from the map.
     *
     * @throws NullPointerException          If the key is null and this map does not permit null keys (optional).
     * @throws UnexpectedValueException      If the key is incompatible with this map (optional).
     * @throws UnsupportedOperationException If the `remove` operation is not supported by this map.
     */
    public function remove($key)
    {
        unset($this[$key]);
    }

    /**
     * Removes mappings for a collection of keys from this map if they are
     * present.
     *
     * This is an optional operation.
     *
     * @param SetInterface|array $keys Keys whose mappings are to be removed from the map.
     *
     * @throws NullPointerException          If a key is null and this map does not permit null keys (optional).
     * @throws UnexpectedValueException      If a key is incompatible with this map (optional).
     * @throws UnsupportedOperationException If the `removeAll` operation is not supported by this map.
     */
    public function removeAll($keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }
    }

    /**
     * Returns `true` if this map contains a mapping for the specified key.
     *
     * @param mixed $key Key whose presence in this map is to be tested.
     *
     * @return bool `true` if this map contains a mapping for the specified key, otherwise `false`.
     *
     * @throws NullPointerException     If the key is null and this map does not permit null keys (optional).
     * @throws UnexpectedValueException If the key is incompatible with this map (optional).
     */
    public function containsKey($key)
    {
        return isset($this[$key]);
    }

    /**
     * Returns `true` if this map contains a mapping for all of the specified keys.
     *
     * @param CollectionInterface|array $keys Keys whose presence in this map is to be tested.
     *
     * @return bool `true` if this map contains a mapping for all of the specified keys, otherwise `false`.
     *
     * @throws NullPointerException     If a key is null and this map does not permit null keys (optional).
     * @throws UnexpectedValueException If a key is incompatible with this map (optional).
     */
    public function containsKeys($keys)
    {
        foreach ($keys as $key) {
            if (false === $this->containsKey($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns `true` if this map maps one or more keys to the specified value.
     *
     * @param mixed $value Value whose presence in this map is to be tested.
     *
     * @return bool `true` if this map maps one or more keys to the value, otherwise `false`.
     *
     * @throws NullPointerException     If the value is null and this map does not permit null values (optional).
     * @throws UnexpectedValueException If the value is incompatible with this map (optional).
     */
    public function containsValue($value)
    {
        foreach ($this->values() as $v) {
            if ($v === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns `true` if this map maps one or more keys to all of the specified
     * values.
     *
     * @param CollectionInterface|array $values Values whose presence in this map is to be tested.
     *
     * @return bool `true` if this map maps one or more keys to all of the values, otherwise `false`.
     *
     * @throws NullPointerException     If a value is null and this map does not permit null values (optional).
     * @throws UnexpectedValueException If a value is incompatible with this map (optional).
     */
    public function containsValues($values)
    {
        foreach ($values as $value) {
            if (false === $this->containsValue($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a set view of the keys contained in this map.
     *
     * @return array Set view of the keys contained in this map.
     */
    public function keySet()
    {
        $result = [];
        $keys = $this->keys();
        foreach ($keys as $key) {
            $result[] = $key;
        }

        return $result;
    }
}
