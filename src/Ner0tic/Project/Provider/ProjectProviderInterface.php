<?php

namespace Ner0tic\PortfolioMgmt\Provider;

interface ProjectProviderInterface
{
    /**
     * Retrieves a project by its name
     *
     * @param string $name
     * @param array  $options
     *
     * @return \Ner0tic\PortfolioMgmt\ItemInterface
     * @throws \InvalidArgumentException if the project does not exists
     */
    public function get($name, array $options = array());

    /**
     * Checks whether a project exists in this provider
     *
     * @param string $name
     * @param array  $options
     *
     * @return boolean
     */
    public function has($name, array $options = array());
}