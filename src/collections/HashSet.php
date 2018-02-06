<?php
namespace onix\collections;

/**
 * Hash-map-based implementation of {@link SetInterface}.
 *
 * It makes no guarantees as to the iteration order of the set; in particular,
 * it does not guarantee that the order will remain constant over time.
 *
 * This class permits the null element.
 */
class HashSet extends AbstractSet
{
    /**
     * @var HashMap
     */
    protected $elements;

    /**
     * @param CollectionInterface|array $elements
     */
    public function __construct($elements = [])
    {
        $this->elements = new HashMap();
        $this->addAll($elements);
    }

    /**
     * {@inheritdoc}
     *
     * @return HashSet A reference to the set.
     */
    public function add($element)
    {
        $this->elements->put($element, $element);
        return $this;
    }

    /**
     * @param CollectionInterface|array $elements Elements to add to the collection.
     * @return HashSet A reference to the set.
     */
    public function addAll($elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        return $this->elements->containsValue($element);
    }

    /**
     * {@inheritdoc}
     */
    public function containsAll($elements)
    {
        foreach ($elements as $element) {
            if (false === $this->elements->containsValue($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return HashSet A reference to the set.
     */
    public function remove($element)
    {
        $this->elements->remove($element);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return HashSet A reference to the set.
     */
    public function removeAll($elements)
    {
        $this->elements->removeAll($elements);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return HashSet A reference to the set.
     */
    public function clear()
    {
        $this->elements->clear();
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return HashSet A reference to the set.
     */
    public function retainAll($elements)
    {
        if ($elements instanceof CollectionInterface) {
            $elements = $elements->toArray();
        }
        $elements = $this->elements->values()->retainAll($elements);
        $this->clear()->addAll($elements);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->elements->values()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements->values()->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $elements = $this->elements->values();
        if (is_null($offset)) {
            $elements[] = $value;
        } else {
            $elements[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $elements = $this->elements->values();
        return isset($elements[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $elements = $this->elements->values();
        unset($elements[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $elements = $this->elements->values();
        return isset($elements[$offset]) ? $elements[$offset] : null;
    }
}
