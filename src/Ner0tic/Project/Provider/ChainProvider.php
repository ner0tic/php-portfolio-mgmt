<?php

namespace Ner0tic\Project\Provider;

class ChainProvider implements ProjectProviderInterface
{
    /**
     * @var ProjectProviderInterface[]
     */
    private $providers;

    public function __construct( array $providers )
    {
        $this->providers = $providers;
    }

    public function get( $name, array $options = array(  ) )
    {
        foreach ( $this->providers as $provider ) {
            if ( $provider->has( $name, $options ) ) {
                return $provider->get( $name, $options );
            }
        }

        throw new \InvalidArgumentException( sprintf( 'The project "%s" is not defined.', $name ) );
    }

    public function has( $name, array $options = array(  ) )
    {
        foreach ( $this->providers as $provider ) {
            if ( $provider->has( $name, $options ) ) {
                return true;
            }
        }

        return false;
    }
}