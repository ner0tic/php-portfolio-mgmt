<?php

namespace Ner0tic\PortfolioMgmt\Matcher\Voter;

use Ner0tic\PortfolioMgmt\ItemInterface;

/**
 * Interface implemented by the matching voters
 */
interface VoterInterface
{
    /**
     * Checks whether an item is current.
     *
     * If the voter is not able to determine a result,
     * it should return null to let other voters do the job.
     *
     * @param ItemInterface $item
     *
     * @return boolean|null
     */
    public function matchItem( ItemInterface $item );
}