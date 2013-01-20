<?php

namespace Ner0tic\Project\Matcher\Voter;

use Ner0tic\Project\ItemInterface;

/**
 * Voter based on the uri
 */
class UriVoter implements VoterInterface
{
    private $uri;

    public function __construct( $uri = null )
    {
        $this->uri = $uri;
    }

    public function setUri( $uri )
    {
        $this->uri = $uri;
    }

    public function matchItem( ItemInterface $item )
    {
        if( null === $this->uri || null === $item->getUri() ) 
        {
            return null;
        }

        if( $item->getUri() === $this->uri ) 
        {
            return true;
        }

        return null;
    }
}