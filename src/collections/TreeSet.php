<?php
namespace onix\collections;

use onix\exceptions\NullPointerException;
use onix\exceptions\UnsupportedOperationException;
use UnexpectedValueException;

class TreeSet extends AbstractSortedSet
{
    /**
     * @param CollectionInterface|array $elements
     * @param ComparatorInterface $comparator
     */
    public function __construct($elements = [], ComparatorInterface $comparator = null)
    {
        $this->setMap(TreeMap::create($comparator));
        $this->addAll($elements);
    }

    /**
     * Create
     *
     * @param ComparatorInterface $comparator  Comparison function
     * @return  TreeSet  A new TreeSet
     */
    public static function create($comparator = null)
    {
        return new static([], $comparator);
    }

    /**
     * Clear the set
     * @return  TreeSet  $this for chaining
     */
    public function clear()
    {
        $this->getMap()->clear();

        return $this;
    }

    /**
     * Clone the set
     * @return  void
     */
    public function __clone()
    {
        $this->setMap(clone $this->getMap());
    }

    /**
     * Serialize the object
     *
     * @return  array  Array of values
     */
    public function jsonSerialize()
    {
        $array = [];

        foreach ($this as $value) {
            $array[] = $value;
        }

        return ['TreeSet' => $array];
    }

    /**
     * Set the value for an element
     *
     * @param   mixed  $element  The element
     * @param   mixed  $value    The value
     *
     * @return  void
     */
    public function offsetSet($element, $value)
    {
        $map = $this->getMap();
        if ($value) {
            $map[$element] = true;
        } else {
            unset($map[$element]);
        }
    }

    /**
     * Unset the existence of an element
     *
     * @param   mixed  $element  The element
     *
     * @return  void
     */
    public function offsetUnset($element)
    {
        $map = $this->getMap();
        unset($map[$element]);
    }

    /**
     * Adds the element to the set, if not already present.
     *
     * This is an optional operation.
     *
     * @param mixed $element Element to add to the set.
     * @return SetInterface A reference to the set.
     *
     * @throws NullPointerException If the element is null and the set does not permit null elements (optional).
     * @throws UnexpectedValueException If the element is incompatible with the set (optional).
     * @throws UnsupportedOperationException If the `add` operation is not supported by the set.
     *
     * @see allAll
     */
    public function add($element)
    {
        $this[$element] = true;
        return $this;
    }

    /**
     * Adds elements to the set, if not already present.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to add to the set.
     * @return SetInterface A reference to the set.
     *
     * @throws NullPointerException          If one or more of the elements is null and the set does not permit null
     *                                       elements (optional).
     * @throws UnexpectedValueException      If one or more of the elements is incompatible with the set (optional).
     * @throws UnsupportedOperationException If the `addAll` operation is not supported by the set.
     *
     * @see add
     */
    public function addAll($elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     * Returns `true` if the set contains the specified element.
     *
     * @param mixed $element Element to test.
     * @return bool `true` if the set contains the specified element, otherwise `false`.
     *
     * @see containsAll
     */
    public function contains($element)
    {
        return isset($this[$element]);
    }

    /**
     * Returns `true` if the set contains all of the specified elements.
     *
     * @param CollectionInterface|array $elements Elements to test.
     * @return bool `true` if the set contains all of the specified elements, otherwise `false`.
     *
     * @see contains
     */
    public function containsAll($elements)
    {
        foreach ($elements as $element) {
            if (false === $this->contains($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes the element from the set, if it is present.
     *
     * This is an optional operation.
     *
     * @param mixed $element Element to be removed from the set.
     *
     * @throws NullPointerException If the element is null and the set does not permit null elements (optional).
     * @throws UnexpectedValueException If the element is incompatible with the set (optional).
     * @throws UnsupportedOperationException If the `remove` operation is not supported by the set.
     *
     * @see removeAll
     */
    public function remove($element)
    {
        unset($this[$element]);
    }

    /**
     * Removes elements from the set, if they are present.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to be removed from the set, if present.
     *
     * @throws NullPointerException If one or more of the elements is null and the set does not permit null
     *                                       elements (optional).
     * @throws UnexpectedValueException If one or more of the elements is incompatible with the set (optional).
     * @throws UnsupportedOperationException If the `retainAll` operation is not supported by the set.
     *
     * @see remove
     */
    public function removeAll($elements)
    {
        foreach ($elements as $element) {
            $this->remove($element);
        }
    }

    /**
     * Retains only the elements in the set that are contained in the specified
     * collection.
     *
     * In other words, removes from the set all of its elements that are not
     * contained in the specified collection.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to be retained in the set.
     * @return SetInterface A reference to the set.
     *
     * @throws NullPointerException If one or more of the elements is null and the set does not permit null
     * elements (optional).
     * @throws UnexpectedValueException If one or more of the elements is incompatible with the set (optional).
     * @throws UnsupportedOperationException If the `retainAll` operation is not supported by the set.
     *
     * @see remove, removeAll
     */
    public function retainAll($elements)
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        }

        $this->clear()->addAll($elements);
        return $this;
    }

    /**
     * Returns `true` if the set contains no elements.
     *
     * @return bool `true` if the set contains no elements, otherwise `false`.
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }
}
