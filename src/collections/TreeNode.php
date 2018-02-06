<?php
namespace onix\collections;

/**
 * Class TreeNode
 *
 * @property-read  TreeNode  $first        The first node of the tree
 * @property-read  TreeNode  $last         The last node of the tree
 * @property-read  TreeNode  $predecessor  The predecessor node
 * @property-read  TreeNode  $successor    The successor node
 * @property-read  mixed     $key          The node key
 * @property-read  integer   $count        The number of elements in the tree
 */
class TreeNode implements \Countable
{
    /**
     * @var integer Information associated to that node.
     *              Bits of order 0 and 1 are reserved for the existence of left and right tree.
     *              Other bits are for the balance
     */
    private $information = 0;

    /**
     * @var TreeNode Left|Predecessor node
     */
    private $left;

    /**
     * @var TreeNode  Right|Successor node
     */
    private $right;

    /**
     * @var mixed Node key
     */
    private $fKey;

    /**
     * @var     mixed  Node value
     */
    public $value;

    /**
     * Create a node
     *
     * @param mixed $key The node key
     * @param mixed $value The node value
     * @return TreeNode A new node
     */
    public static function create($key, $value)
    {
        return new static($key, $value);
    }

    /**
     * Constructor
     *
     * @param mixed $key The node key
     * @param mixed $value The node value
     * @param TreeNode $predecessor The left node
     * @param TreeNode $successor The right node
     */
    protected function __construct($key, $value, $predecessor = null, $successor = null)
    {
        $this->fKey = $key;
        $this->value = $value;
        $this->left = $predecessor;
        $this->right = $successor;
    }

    /**
     * Magic get method
     * @param string $property  The node property
     * @return mixed The value associated to the property
     * @throws  \RuntimeException  If the property is undefined
     */
    public function __get($property)
    {
        switch ($property) {
            case 'first':
                return $this->first();
                break;
            case 'last':
                return $this->last();
                break;
            case 'predecessor':
                return $this->predecessor();
                break;
            case 'successor':
                return $this->successor();
                break;
            case 'key':
                return $this->fKey;
                break;
            case 'count':
                return $this->count();
                break;
            default:
                throw new \RuntimeException('Undefined property');
                break;
        }
    }

    /**
     * Get the first node
     * @return TreeNode the first node
     */
    public function first()
    {
        $node = $this;

        while ($node->information & 2) {
            $node = $node->left;
        }

        return $node;
    }

    /**
     * Get the last node
     * @return TreeNode the last node
     */
    public function last()
    {
        $node = $this;

        while ($node->information & 1) {
            $node = $node->right;
        }

        return $node;
    }

    /**
     * Get the predecessor
     * @return TreeNode the predecessor node
     */
    public function predecessor()
    {
        if ($this->information & 2) {
            $node = $this->left;

            while ($node->information & 1) {
                $node = $node->right;
            }

            return $node;
        } else {
            return $this->left;
        }
    }

    /**
     * Get the successor
     * @return TreeNode the successor node
     */
    public function successor()
    {
        if ($this->information & 1) {
            $node = $this->right;

            while ($node->information & 2) {
                $node = $node->left;
            }

            return $node;
        } else {
            return $this->right;
        }
    }

    /**
     * Count the number of key/value pair
     * @return  integer
     */
    public function count()
    {
        $count = 1;
        if ($this->information & 2) {
            $count += $this->left->count;
        }

        if ($this->information & 1) {
            $count += $this->right->count;
        }

        return $count;
    }

    /**
     * Get the node for a key
     *
     * @param mixed $key The key
     * @param ComparatorInterface $comparator The comparator function
     * @param integer $type The operation type
     *                      -2 for the greatest key lesser than the given key
     *                      -1 for the greatest key lesser than or equal to the given key
     *                      0 for the given key
     *                      +1 for the lowest key greater than or equal to the given key
     *                      +2 for the lowest key greater than the given key
     *
     * @return TreeNode|null The node or null if not found
     */
    public function find($key, $comparator, $type = 0)
    {
        $cmp = -1;
        $node = $this;
        while (true) {
            $cmp = $comparator->compare($key, $node->fKey);
            if ($cmp < 0 && $node->information & 2) {
                $node = $node->left;
            } elseif ($cmp > 0 && $node->information & 1) {
                $node = $node->right;
            } else {
                break;
            }
        }

        if ($cmp < 0) {
            if ($type < 0) {
                return $node->left;
            } elseif ($type > 0) {
                return $node;
            } else {
                return null;
            }
        } elseif ($cmp > 0) {
            if ($type < 0) {
                return $node;
            } elseif ($type > 0) {
                return $node->right;
            } else {
                return null;
            }
        } else {
            if ($type < -1) {
                return $node->predecessor;
            } elseif ($type > 1) {
                return $node->successor;
            } else {
                return $node;
            }
        }
    }

    /**
     * Rotate the node to the left
     * @return  TreeNode  The rotated node
     */
    private function rotateLeft()
    {
        $right = $this->right;

        if ($right->information & 2) {
            $this->right = $right->left;
            $right->left = $this;
        } else {
            $right->information |= 2;
            $this->information &= ~ 1;
        }

        $this->information -= 4;

        if ($right->information >= 4) {
            $this->information -= $right->information & ~3;
        }

        $right->information -= 4;

        if ($this->information < 0) {
            $right->information += $this->information & ~3;
        }

        return $right;
    }

    /**
     * Rotate the node to the right
     *
     * @return  TreeNode  The rotated node
     */
    private function rotateRight()
    {
        $left = $this->left;

        if ($left->information & 1) {
            $this->left = $left->right;
            $left->right = $this;
        } else {
            $this->information &= ~ 2;
            $left->information |= 1;
        }

        $this->information += 4;

        if ($left->information < 0) {
            $this->information -= $left->information & ~3;
        }

        $left->information += 4;

        if ($this->information >= 4) {
            $left->information += $this->information & ~3;
        }

        return $left;
    }

    /**
     * Increment the balance of the node
     *
     * @return  TreeNode  $this or a rotated version of $this
     */
    private function incBalance()
    {
        $this->information += 4;

        if ($this->information >= 8) {
            if ($this->right->information < 0) {
                $this->right = $this->right->rotateRight();
            }

            return $this->rotateLeft();
        }

        return $this;
    }

    /**
     * Decrement the balance of the node
     *
     * @return  TreeNode  $this or a rotated version of $this
     */
    private function decBalance()
    {
        $this->information -= 4;

        if ($this->information < - 4) {
            if ($this->left->information >= 4) {
                $this->left = $this->left->rotateLeft();
            }

            return $this->rotateRight();
        }

        return $this;
    }

    /**
     * Insert a key/value pair
     *
     * @param mixed $key The key
     * @param mixed $value The value
     * @param ComparatorInterface $comparator  The comparator function
     *
     * @return TreeNode  The new root
     */
    public function insert($key, $value, $comparator)
    {
        $node = $this;
        $cmp = $comparator->compare($key, $this->fKey);

        if ($cmp < 0) {
            if ($this->information & 2) {
                $leftBalance = $this->left->information & ~3;
                $this->left = $this->left->insert($key, $value, $comparator);

                if (($this->left->information & ~3) && ($this->left->information & ~3) != $leftBalance) {
                    $node = $this->decBalance();
                }
            } else {
                $this->left = new static($key, $value, $this->left, $this);
                $this->information|= 2;
                $node = $this->decBalance();
            }
        } elseif ($cmp > 0) {
            if ($this->information & 1) {
                $rightBalance = $this->right->information & ~3;
                $this->right = $this->right->insert($key, $value, $comparator);

                if (($this->right->information & ~3) && ($this->right->information & ~3) != $rightBalance) {
                    $node = $this->incBalance();
                }
            } else {
                $this->right = new static($key, $value, $this, $this->right);
                $this->information|= 1;
                $node = $this->incBalance();
            }
        } else {
            $this->value = $value;
        }

        return $node;
    }

    /**
     * Pull up the left most node of a node
     *
     * @return  TreeNode  The new root
     */
    private function pullUpLeftMost()
    {
        if ($this->information & 2) {
            $leftBalance = $this->left->information & ~3;
            $this->left = $this->left->pullUpLeftMost();

            if (!($this->information & 2) || $leftBalance != 0 && ($this->left->information & ~3) == 0) {
                return $this->incBalance();
            } else {
                return $this;
            }
        } else {
            $this->left->fKey = $this->fKey;
            $this->left->value = $this->value;

            if ($this->information & 1) {
                $this->right->left = $this->left;

                return $this->right;
            } else {
                if ($this->left->right === $this) {
                    $this->left->information &= ~ 1;

                    return $this->right;
                } else {
                    $this->right->information &= ~ 2;

                    return $this->left;
                }
            }
        }
    }

    /**
     * Remove a key
     *
     * @param mixed $key The key
     * @param ComparatorInterface $comparator The comparator function
     *
     * @return  TreeNode  The new root
     */
    public function remove($key, $comparator)
    {
        $cmp = $comparator->compare($key, $this->fKey);

        if ($cmp < 0) {
            if ($this->information & 2) {
                $leftBalance = $this->left->information & ~3;
                $this->left = $this->left->remove($key, $comparator);

                if (!($this->information & 2) || $leftBalance != 0 && ($this->left->information & ~3) == 0) {
                    return $this->incBalance();
                }
            }
        } elseif ($cmp > 0) {
            if ($this->information & 1) {
                $rightBalance = $this->right->information & ~3;
                $this->right = $this->right->remove($key, $comparator);

                if (!($this->information & 1) || $rightBalance != 0 && ($this->right->information & ~3) == 0) {
                    return $this->decBalance();
                }
            }
        } else {
            if ($this->information & 1) {
                $rightBalance = $this->right->information & ~3;
                $this->right = $this->right->pullUpLeftMost();

                if (!($this->information & 1) || $rightBalance != 0 && ($this->right->information & ~3) == 0) {
                    return $this->decBalance();
                }
            } else {
                $left = $this->left;
                $right = $this->right;

                if ($this->information & 2) {
                    $left->right = $right;

                    return $left;
                } else {
                    if ($left && $left->right === $this) {
                        $left->information &= ~ 1;

                        return $right;
                    } elseif ($right && $right->left === $this) {
                        $right->information &= ~ 2;

                        return $left;
                    } else {
                        return null;
                    }
                }
            }
        }

        return $this;
    }
}
