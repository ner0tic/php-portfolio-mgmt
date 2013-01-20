<?php

namespace Ner0tic\Project\Renderer;

use Ner0tic\Project\ItemInterface;

interface RendererInterface
{
    /**
     * Renders project tree.
     *
     * Common options:
     *      - depth: The depth at which the item is rendered
     *          null: no limit
     *          0: no children
     *          1: only direct children
     *      - currentClass: class added to the current item
     *      - ancestorClass: class added to the ancestors of the current item
     *      - firstClass: class added to the first child
     *      - lastClass: class added to the last child
     *
     * @param ItemInterface $item    Project item
     * @param array         $options some rendering options
     *
     * @return string
     */
    public function render( ItemInterface $item, array $options = array() );
}