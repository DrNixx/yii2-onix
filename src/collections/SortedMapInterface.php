<?php
namespace onix\collections;

/**
 * A map that further provides a total ordering on its keys.
 *
 * The map is ordered according to the natural ordering of its keys, or by a
 * comparator typically provided at sorted map creation time.
 */
interface SortedMapInterface extends MapInterface, SortedCollectionInterface
{
    /**
     * Constructor.
     *
     * @param MapInterface|array $map Optional initial map.
     * @param ComparatorInterface|null $comparator Optional comparator.
     */
    public function __construct($map = array(), ComparatorInterface $comparator = null);

    /**
     * Get the first key or throw an exception if there is no element
     * @return  mixed  The first key
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function firstKey();

    /**
     * Get the last key or throw an exception if there is no element
     * @return  mixed  The last key
     * @throws  \OutOfBoundsException  If there is no element
     */
    public function lastKey();

    /**
     * Returns the greatest key lesser than the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no lower element
     */
    public function lowerKey($key);

    /**
     * Returns the greatest key lesser than or equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no floor element
     */
    public function floorKey($key);

    /**
     * Returns the key equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no such element
     */
    public function findKey($key);

    /**
     * Returns the lowest key greater than or equal to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no ceiling element
     */
    public function ceilingKey($key);

    /**
     * Returns the lowest key greater than to the given key or throw an exception if there is no such key
     * @param   mixed  $key  The searched key
     * @return  mixed  The found key
     * @throws  \OutOfBoundsException  If there is no higher element
     */
    public function higherKey($key);

    /**
     * Get the predecessor node
     * @param TreeNode $node  A tree node member of the underlying TreeMap
     * @return mixed The predecessor node
     */
    public function predecessor($node);

    /**
     * Get the successor node
     * @param TreeNode $node A tree node member of the underlying TreeMap
     * @return mixed The successor node
     */
    public function successor($node);

    /**
     * Keys generator
     * @return  mixed  The keys generator
     */
    public function keys();

    /**
     * Values generator
     * @return  mixed  The values generator
     */
    public function values();
}
