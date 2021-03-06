<?php
namespace onix\collections;

use onix\exceptions\NullPointerException;
use onix\exceptions\UnsupportedOperationException;

interface ListInterface extends CollectionInterface
{
    /**
     * Adds the element to the end of the list.
     *
     * This is an optional operation.
     *
     * @param mixed $element Element to add to the list.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException If the element is null and the list does not permit null elements (optional).
     * @throws \UnexpectedValueException If the element is incompatible with the list (optional).
     * @throws UnsupportedOperationException If the `add` operation is not supported by this list.
     *
     * @see allAll
     */
    public function add($element);

    /**
     * Adds elements to the end of the list.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to add to the list.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If one or more of the elements is null and the list does not permit null
     *                                       elements (optional).
     * @throws \UnexpectedValueException      If one or more of the elements is incompatible with the list (optional).
     * @throws UnsupportedOperationException If the `addAll` operation is not supported by the list.
     *
     * @see add
     */
    public function addAll($elements);

    /**
     * Inserts the element at the specified position in this list.
     *
     * Shifts the element currently at that position (if any) and any
     * subsequent elements to the right (adds one to their indices).
     *
     * This is an optional operation.
     *
     * @param int   $index   Index to insert at.
     * @param mixed $element Element to insert.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If the element is null and this list does not permit null elements
     *                                       (optional).
     * @throws \OutOfBoundsException          If the index is out of range.
     * @throws \UnexpectedValueException      If the element is incompatible with this collection (optional).
     * @throws UnsupportedOperationException If the `insert` operation is not supported by this list.
     *
     * @see insertAll, add
     */
    public function insert($index, $element);

    /**
     * Inserts all of the elements into this list at the specified position.
     *
     * Shifts the element currently at that position (if any) and any
     * subsequent elements to the right (increases their indices). The new
     * elements will appear in this list in the order that they are returned by
     * the specified collection's iterator.
     *
     * This is an optional operation.
     *
     * @param int                       $index    Index at insert at.
     * @param CollectionInterface|array $elements Elements to insert.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If the element is null and this list does not permit null elements
     *                                       (optional).
     * @throws \OutOfBoundsException          If the index is out of range.
     * @throws \UnexpectedValueException      If an element is incompatible with this collection (optional).
     * @throws UnsupportedOperationException If the `insertAll` operation is not supported by this list.
     *
     * @see insert, addAll
     */
    public function insertAll($index, $elements);

    /**
     * Replaces the element at the specified position in this list with the
     * specified element.
     *
     * This is an optional operation.
     *
     * @param int   $index   Index of the element to replace.
     * @param mixed $element Element to replace.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If the element is null and this list does not permit null elements
     *                                       (optional).
     * @throws \OutOfBoundsException          If the index is out of range.
     * @throws \UnexpectedValueException      If the element is incompatible with this collection (optional).
     * @throws UnsupportedOperationException If the `set` operation is not supported by this list.
     */
    public function set($index, $element);

    /**
     * Returns the element at the specified position in this list.
     *
     * @param int $index Index of the element to return.
     *
     * @return mixed The element at the specified index in this list.
     *
     * @throws \OutOfBoundsException If the index is out of range.
     */
    public function get($index);

    /**
     * Returns `true` if the list contains the specified element.
     *
     * @param mixed $element Element to test.
     *
     * @return bool `true` if the list contains the specified element, otherwise `false`.
     *
     * @see containsAll
     */
    public function contains($element);

    /**
     * Returns `true` if the list contains all of the specified elements.
     *
     * @param CollectionInterface|array $elements Elements to test.
     *
     * @return bool `true` if the list contains all of the specified elements, otherwise `false`.
     *
     * @see contains
     */
    public function containsAll($elements);

    /**
     * Removes the element at the specified position in this list.
     *
     * Shifts any subsequent elements to the left (subtracts one from their
     * indices).
     *
     * This is an optional operation.
     *
     * @param int $index Index of the element to be removed.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws \OutOfBoundsException          If the index is out of range.
     * @throws UnsupportedOperationException If the `drop` operation is not supported by this list.
     */
    public function drop($index);

    /**
     * Removes the first instance of the element from the list, if it is present.
     *
     * This is an optional operation.
     *
     * @param mixed $element Element to be removed from the list.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException If the element is null and the list does not permit null elements (optional).
     * @throws \UnexpectedValueException If the element is incompatible with the list (optional).
     * @throws UnsupportedOperationException If the `remove` operation is not supported by the list.
     *
     * @see removeAll
     */
    public function remove($element);

    /**
     * Removes all instances of the elements from the list, if they are present.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to be removed from the list, if present.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If one or more of the elements is null and the list does not permit null
     *                                       elements (optional).
     * @throws \UnexpectedValueException      If one or more of the elements is incompatible with the list (optional).
     * @throws UnsupportedOperationException If the `retainAll` operation is not supported by the list.
     *
     * @see remove
     */
    public function removeAll($elements);

    /**
     * Retains only the elements in the list that are contained in the specified
     * collection.
     *
     * In other words, removes from the list all of its elements that are not
     * contained in the specified collection.
     *
     * This is an optional operation.
     *
     * @param CollectionInterface|array $elements Elements to be retained in the list.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws NullPointerException          If one or more of the elements is null and the list does not permit null
     *                                       elements (optional).
     * @throws \UnexpectedValueException      If one or more of the elements is incompatible with the list (optional).
     * @throws UnsupportedOperationException If the `retainAll` operation is not supported by the list.
     *
     * @see remove, removeAll
     */
    public function retainAll($elements);

    /**
     * Removes all elements from the list.
     *
     * This is an optional operation.
     *
     * @return ListInterface A reference to the list.
     *
     * @throws UnsupportedOperationException If the `clear` operation is not supported by the list.
     */
    public function clear();

    /**
     * Returns the number of elements in the list.
     *
     * @return int Number of elements in the list.
     */
    public function count();

    /**
     * Returns `true` if the list contains no elements.
     *
     * @return bool `true` if the list contains no elements, otherwise `false`.
     */
    public function isEmpty();

    /**
     * Returns an array containing all of the elements in the list.
     *
     * @return array An array containing all of the elements in the list.
     */
    public function toArray();

    /**
     * Returns the index of the first occurrence of the specified element in
     * this list, or null if this list does not contain the element.
     *
     * @param mixed $element Element to search for.
     *
     * @return int|null The index of the first occurrence of the specified element in this list, or null if this list
     *                  does not contain the element.
     *
     * @throws NullPointerException If the element is null and this list does not permit null elements (optional).
     */
    public function indexOf($element);

    /**
     * Returns the index of the last occurrence of the specified element in
     * this list, or null if this list does not contain the element.
     *
     * @param mixed $element Element to search for.
     *
     * @return int|null The index of the last occurrence of the specified element in this list, or null if this list
     *                  does not contain the element.
     *
     * @throws NullPointerException If the element is null and this list does not permit null elements (optional).
     */
    public function lastIndexOf($element);

    /**
     * Returns a view of the portion of this list between the specified
     * `fromIndex`, inclusive, and `toIndex`, exclusive. (If `fromIndex` and
     * `toIndex` are equal, the returned list is empty.)
     *
     * The returned list is backed by this list, so changes in the returned
     * list are reflected in this list, and vice-versa. The returned list
     * supports all of the optional list operations supported by this list.
     *
     * Due to the potential complications in its implementation, this is an
     * optional operation.
     *
     * @param int $fromIndex Low endpoint (inclusive) of the sub-list.
     * @param int $toIndex   High endpoint (exclusive) of the sub-list.
     *
     * @return ListInterface A view of the specified range within this list.
     *
     * @throws \OutOfBoundsException          If one of the indices is out of range.
     * @throws UnsupportedOperationException If the `subList` operation is not supported by the list.
     */
    public function subList($fromIndex, $toIndex);
}
