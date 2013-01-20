<?php

namespace Ner0tic\PortfolioMgmt;

/**
 * Interface implemented by the factory to create items
 */
interface FactoryInterface
{
    /**
     * Creates a project item
     *
     * @param string $name
     * @param array  $options
     *
     * @return ItemInterface
     */
    public function createItem( $name, array $options = array() );

    /**
     * Create a project item from a NodeInterface
     *
     * @param NodeInterface $node
     *
     * @return ItemInterface
     */
    public function createFromNode( NodeInterface $node );

    /**
     * Creates a new project item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from ProjectItem->toArray().
     *
     * @param array $data The array of data to use as a source for the project tree
     *
     * @return ItemInterface
     */
    public function createFromArray( array $data );
}