<?php
namespace onix\collections;

/**
 * A set that further provides a total ordering on its elements.
 *
 * The elements are ordered using their natural ordering, or by a comparator
 * typically provided at sorted set creation time.
 */
interface SortedSetInterface extends SetInterface, SortedCollectionInterface
{
    /**
     * Constructor.
     *
     * @param CollectionInterface|array $elements   Optional initial elements.
     * @param ComparatorInterface|null  $comparator Optional comparator.
     */
    public function __construct($elements = [], ComparatorInterface $comparator = null);

    /**
     * Returns the comparator used to order the elements in this set, or null
     * if this set uses the natural ordering of its elements.
     *
     * @return ComparatorInterface|null The comparator used to order the elements in this set, or null if this set uses
     *                                  the natural ordering of its elements
     */
    public function comparator();

    /**
     * Returns the first (lowest) element currently in this set.
     *
     * @return mixed The first (lowest) element currently in this set.
     *
     * @throws \UnderflowException If this set is empty.
     */
    public function first();

    /**
     * Returns the last (highest) element currently in this set.
     *
     * @return mixed The last (highest) element currently in this set.
     *
     * @throws \UnderflowException If this set is empty.
     */
    public function last();
}
