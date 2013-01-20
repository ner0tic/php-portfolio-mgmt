<?php

namespace Ner0tic\PortfolioMgmt\Matcher;

use Ner0tic\PortfolioMgmt\ItemInterface;
use Ner0tic\PortfolioMgmt\Matcher\Voter\VoterInterface;

/**
 * A MatcherInterface implementation using a voter system
 */
class Matcher implements MatcherInterface
{
    private $cache;

    /**
     * @var VoterInterface[]
     */
    private $voters = array();

    public function __construct()
    {
        $this->cache = new \SplObjectStorage();
    }

    /**
     * Adds a voter in the matcher.
     *
     * @param VoterInterface $voter
     */
    public function addVoter( VoterInterface $voter )
    {
        $this->voters[] = $voter;
    }

    public function isAncestor( ItemInterface $item, $depth = null )
    {
        if( 0 === $depth ) 
        {
            return false;
        }

        $childDepth = null === $depth ? null : $depth - 1;
        foreach( $item->getChildren() as $child ) 
        {
            if( $this->isAncestor( $child, $childDepth ) ) 
            {
                return true;
            }
        }

        return false;
    }

    public function clear()
    {
        $this->cache = new \SplObjectStorage();
    }
}