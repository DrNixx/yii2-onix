<?php
namespace onix\collections;

/**
 * This interface imposes a total ordering on the objects of each class that
 * implements it.
 *
 * This ordering is referred to as the class's natural ordering, and the class's
 * `compareTo` method is referred to as its natural comparison method.
 */
interface ComparableInterface
{
    /**
     * Compares this object with the specified object for order. Returns a
     * negative integer, zero, or a positive integer as this object is less
     * than, equal to, or greater than the specified object.
     *
     * @param mixed $item The item to be compared.
     *
     * @return int A negative integer, zero, or a positive integer as this object is less than, equal to, or greater
     *             than the specified item.
     *
     * @throws \UnexpectedValueException If the item's type prevents it from being compared to this object (optional).
     */
    public function compareTo($item);
}
