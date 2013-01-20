<?php

namespace Ner0tic\Project;

/**
 * Factory to create a project from a tree
 */
class ProjectFactory implements FactoryInterface
{
    public function createItem( $name, array $options = array() )
    {
        $item = new MenuItem( $name, $this );

        $options = $this->buildOptions( $options );
        $this->configureItem( $item, $options );

        return $item;
    }

    /**
     * Builds the full option array used to configure the item.
     *
     * @param array $options
     *
     * @return array
     */
    protected function buildOptions( array $options )
    {
        return array_merge( array( 
                'urls'                  =>  null,
                'description'           =>  null,
                'status'                =>  null,
                'primaryCategory'       =>  null,
                'attributes'            =>  array(),
                'childrenAttributes'    =>  array(),
                'extras'                =>  array(),
                'display'               =>  true,
                'displayChildren'       =>  true,
             ), $options
         );
    }

    /**
     * Configures the newly created item with the passed options
     *
     * @param ItemInterface $item
     * @param array         $options
     */
    protected function configureItem( ItemInterface $item, array $options )
    {
        $item
            ->setUrls( $options[ 'urls' ] )
            ->setDescription( $options[ 'description' ] )
            ->setStatus( $options[ ' status' ] )
            ->setPrimaryCategory( $options[ 'primaryCategory' ] )
            ->setAttributes( $options[ 'attributes' ] )
            ->setChildrenAttributes( $options[ 'childrenAttributes' ] )
            ->setExtras( $options[ 'extras' ] )
            ->setDisplay( $options[ 'display' ] )
            ->setDisplayChildren( $options[ 'displayChildren' ] )
        ;
    }

    public function createFromNode( NodeInterface $node )
    {
        $item = $this->createItem( $node->getName(), $node->getOptions() );

        foreach( $node->getChildren() as $childNode ) 
        {
            $item->addChild( $this->createFromNode( $childNode ) );
        }

        return $item;
    }

    public function createFromArray( array $data, $name = null )
    {
        $name = isset( $data[ 'name' ] ) ? $data[ 'name' ] : $name;
        if( isset( $data[ 'children' ] ) ) {
            $children = $data[ 'children' ];
            unset( $data[ 'children' ] );
        } 
        else 
        {
            $children = array();
        }

        $item = $this->createItem( $name, $data );
        foreach( $children as $name => $child ) 
        {
            $item->addChild( $this->createFromArray( $child, $name ) );
        }

        return $item;
    }
}