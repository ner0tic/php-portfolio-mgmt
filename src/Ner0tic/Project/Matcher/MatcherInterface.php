<?php

namespace Ner0tic\PortfolioMgmt\Matcher;

use Ner0tic\PortfolioMgmt\ItemInterface;

/**
 * Interface implemented by the item matcher
 */
interface MatcherInterface
{

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param ItemInterface $item
     * @param integer       $depth The max depth to look for the item
     *
     * @return boolean
     */
    public function isAncestor(ItemInterface $item, $depth = null);

    /**
     * Clears the state of the matcher.
     */
    public function clear();
}