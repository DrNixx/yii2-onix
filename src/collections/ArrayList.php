<?php
namespace onix\collections;

/**
 * Resizable-array implementation of {@link ListInterface}.
 *
 * Implements all optional list operations, and permits all elements, including `null`.
 */
class ArrayList extends AbstractList
{
    /**
     * @var SubArrayList[]
     */
    protected $subLists = [];

    /**
     * Adds the element to the end of the list.
     *
     * @param mixed $element Element to add to the list.
     *
     * @return ArrayList A reference to the list.
     *
     * @see addAll
     */
    public function add($element)
    {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * Adds elements to the end of the list.
     *
     * @param CollectionInterface|array $elements Elements to add to the list.
     *
     * @return ArrayList A reference to the list.
     *
     * @see add
     */
    public function addAll($elements)
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        } else {
            $elements = array_values($elements);
        }
        $this->elements = array_merge($this->elements, $elements);
        return $this;
    }

    /**
     * Inserts the element at the specified position in this list.
     *
     * Shifts the element currently at that position (if any) and any subsequent
     * elements to the right (adds one to their indices).
     *
     * @param int   $index   Index to insert at.
     * @param mixed $element Element to insert.
     *
     * @return ArrayList A reference to the list.
     *
     * @throws \OutOfBoundsException If the index is out of range.
     *
     * @see insertAll, add
     */
    public function insert($index, $element)
    {
        if (0 < $index && $this->count() < $index) {
            throw new \OutOfBoundsException();
        }

        array_splice($this->elements, $index, 0, $element);
        foreach ($this->subLists as $subList) {
            $subList->insert($index, $element, true);
        }

        return $this;
    }

    /**
     * Inserts all of the elements into this list at the specified position.
     *
     * Shifts the element currently at that position (if any) and any
     * subsequent elements to the right (increases their indices). The new
     * elements will appear in this list in the order that they are returned by
     * the specified collection's iterator.
     *
     * @param int $index Index at insert at.
     * @param CollectionInterface|array $elements Elements to insert.
     * @return ArrayList A reference to the list.
     * @throws \OutOfBoundsException If the index is out of range.
     *
     * @see insert, addAll
     */
    public function insertAll($index, $elements)
    {
        if (0 < $index && $this->count() < $index) {
            throw new \OutOfBoundsException();
        }
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        } else {
            $elements = array_values($elements);
        }
        array_splice($this->elements, $index, 0, $elements);
        foreach ($this->subLists as $subList) {
            $subList->insertAll($index, $elements, true);
        }
        return $this;
    }

    /**
     * Replaces the element at the specified position in this list with the
     * specified element.
     *
     * @param int   $index   Index of the element to replace.
     * @param mixed $element Element to replace.
     *
     * @return ArrayList A reference to the list.
     *
     * @throws \OutOfBoundsException If the index is out of range.
     */
    public function set($index, $element)
    {
        if (false === array_key_exists($index, $this->elements)) {
            throw new \OutOfBoundsException();
        }
        $this->elements[$index] = $element;
        foreach ($this->subLists as $subList) {
            $subList->set($index, $element, true);
        }
        return $this;
    }

    /**
     * Removes the first instance of the element from the list, if it is present.
     *
     * @param mixed $element Element to be removed from the list.
     *
     * @return ArrayList A reference to the list.
     *
     * @see removeAll
     */
    public function remove($element)
    {
        $key = array_search($element, $this->elements, true);
        if (false !== $key) {
            unset($this->elements[$key]);
            $this->elements = array_values($this->elements);
        }
        foreach ($this->subLists as $subList) {
            $subList->drop($key, true);
        }
        return $this;
    }

    /**
     * Removes all instances of the elements from the list, if they are present.
     *
     * @param CollectionInterface|array $elements Elements to be removed from the list, if present.
     *
     * @return ArrayList A reference to the list.
     *
     * @see remove
     */
    public function removeAll($elements)
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        }
        $this->elements = array_values(
            array_udiff(
                $this->elements,
                $elements,
                function ($a, $b) {
                    if ($a === $b) {
                        return 0;
                    } elseif (is_int($a) && is_object($b)) {
                        return -1;
                    } elseif (is_object($a) && is_int($b)) {
                        return 1;
                    } elseif ($a < $b) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            )
        );
        foreach ($this->subLists as $subList) {
            $subList->removeAll($elements, true);
        }
        return $this;
    }

    /**
     * Removes the element at the specified position in this list.
     *
     * Shifts any subsequent elements to the left (subtracts one from their
     * indices).
     *
     * @param int $index Index of the element to be removed.
     *
     * @return ArrayList A reference to the list.
     *
     * @throws \OutOfBoundsException If the index is out of range.
     */
    public function drop($index)
    {
        if (false === array_key_exists($index, $this->elements)) {
            throw new \OutOfBoundsException();
        }
        unset($this->elements[$index]);
        $this->elements = array_values($this->elements);
        foreach ($this->subLists as $subList) {
            $subList->drop($index, true);
        }
        return $this;
    }

    /**
     * Removes all elements from the list.
     *
     * @return ArrayList A reference to the list.
     */
    public function clear()
    {
        $this->elements = array();
        foreach ($this->subLists as $subList) {
            $subList->clear(true);
        }
        return $this;
    }

    /**
     * Retains only the elements in the list that are contained in the specified
     * collection.
     *
     * In other words, removes from the list all of its elements that are not
     * contained in the specified collection.
     *
     * @param CollectionInterface|array $elements Elements to be retained in the list.
     *
     * @return ArrayList A reference to the list.
     */
    public function retainAll($elements)
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        }
        $this->elements = array_values(
            array_uintersect(
                $this->elements,
                $elements,
                function ($a, $b) {
                    if ($a === $b) {
                        return 0;
                    } elseif (is_int($a) && is_object($b)) {
                        return -1;
                    } elseif (is_object($a) && is_int($b)) {
                        return 1;
                    } elseif ($a < $b) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            )
        );
        return $this;
    }

    /**
     * Returns a view of the portion of this list between the specified
     * `fromIndex`, inclusive, and `toIndex`, exclusive. (If `fromIndex` and
     * `toIndex` are equal, the returned list is empty.)
     *
     * The returned list is backed by this list, so changes in the returned
     * list are reflected in this list, and vice-versa. The returned list
     * supports all of the optional list operations supported by this list.
     *
     * @param int $fromIndex Low endpoint (inclusive) of the sub-list.
     * @param int $toIndex   High endpoint (exclusive) of the sub-list.
     *
     * @return ArrayList A view of the specified range within this list.
     *
     * @throws \OutOfBoundsException If one of the indices is out of range.
     */
    public function subList($fromIndex, $toIndex)
    {
        if ($fromIndex > $toIndex || $fromIndex < 0 || $toIndex > $this->count()) {
            throw new \OutOfBoundsException();
        }
        $subList = SubArrayList::factory($this, $fromIndex, $toIndex);
        $this->subLists[] = $subList;
        return $subList;
    }
}
