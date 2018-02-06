<?php
namespace onix\collections;

/**
 * This provides a skeletal implementation of {@link SetInterface}, to
 * minimize the effort required to implement this interface.
 *
 * This is an immutable collection: you must overwrite methods such as `add()`
 * to allow modification.
 */
abstract class AbstractSet extends AbstractCollection implements SetInterface
{
    /**
     * {@inheritdoc}
     */
    protected $elements;

    /**
     * @param CollectionInterface|array $elements
     */
    public function __construct($elements = [])
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        }

        $this->elements = array_values(array_unique($elements, SORT_REGULAR));
    }
}
