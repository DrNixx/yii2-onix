<?php
namespace onix\collections;

/**
 * A comparison function, which imposes a total ordering on some collection of
 * objects.
 */
interface ComparatorInterface
{
    /**
     * Compares two items for order.
     *
     * Returns a negative integer, zero, or a positive integer as the first
     * item is less than, equal to, or greater than the second.
     *
     * @param mixed $item1 First item to be compared.
     * @param mixed $item2 Second item to be compared.
     *
     * @return int A negative integer, zero, or a positive integer as the first item is less than, equal to, or
     *             greater than the second.
     *
     * @throws \UnexpectedValueException If the items' types prevent them from being compared by this comparator.
     */
    public function compare($item1, $item2);
}
