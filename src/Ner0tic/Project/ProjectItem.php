<?php
namespace Ner0tic\PortfolioMgmt;

use \InvalidArgumentException as InvalidArg;

class ProjectItem implements ItemInterface
{
    protected   $name               =   null,
                $description        =   null,
                $status             =   null,
                
                $urls               =   array(
                    'dev'           =>  null,
                    'prod'          =>  null),                
            
                $categories         =   array(),
                $primary_category   =   null,
            
                $parent             =   null,
                $children           =   array(),
            
                $attributes         =   array(),
                $childrenAttributes =   array(),
                $extras             =   array(),
                
                $display            =   true,
                $displayChildren    =   true;
    
    protected   $factory;
    
    private     $accepted_environments = array();
                    
    
    public function __construct( $name, FactoryInterface $factory )
    {
        $this->name     =   ( string ) $name;
        $this->factory  =   $factory;
        
        $this->setAcceptedEnvironments( array(
            'dev'           =>  null,
            'test'          =>  null,
            'prod'          =>  null
        ));
    }
    
    public function setFactory( FactoryInterface $factory )
    {
        $this->factory  =   $factory;
        
        return $this;       
    }
    
    public function setAcceptedEnvironments( array $env )
    {
        $this->accepted_environments = $env;        
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName( $name )
    {
        if( $this->name == $name )
        {
            return $this;
        }
        
        $parent = $this->getParent();
        if( null !== $parent && isset( $parent[ $name ] ) )
        {
            throw new InvalidArg( '[error] Cannot rename item, name is already used by sibling.' );
        }
        
        $oName      = $this->name;
        $this->name = $name;
        
        if( null !== $parent ) 
        {
            $names  = array_keys( $parent->getChildren() );
            $items  = array_values( $parent->getChildren() );
            
            $offset = array_search( $oName, $names );
            $names[ $offset ] = $name;
            
            $parent->setChildren( array_combine( $names, $items ) );
        }
        
        return $this;
    }
    
    public function getDescription() 
    {
        return $this->description;
    }
    
    public function setDescription( $description )
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus( $status )
    {
        $this->status = $status;
        
        return $this;
    }
    
    public function getUrls()
    {
        return $this->urls;
    }
    
    public function setUrls( array $urls )
    {
        $this->urls = $urls;
        
        return $this;
    }
    
    public function getUrl( $name )
    {
        if( null == $name )
        {
            throw new InvalidArgumentException( '[error] You must supply a url type.' );
        }
        elseif( !in_array( $name, $this->getAcceptedEnvironments() ) )
        {
            throw new InvalidArgumentException( '[error] Invalid url type. ' );
        }
        
        return $this->urls[ $name ];        
    }
    
    public function setUrl( $name, $value )
    {
        if( $name != 'dev' || $name != 'prod' ) 
        {
            throw new \InvalidArgumentException( '[error] Invalid url type' );
        }
            
        $this->urls[ $name ] = $value;
        
        return $this;
    }    
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function setAttributes( array $attributes ) 
    {
        $this->attributes   =   $attributes;
        
        return $this;
    }
    
    public function getAttribute($name, $default = null )
    {
        if( isset( $this->attributes[ $name ] ) )
        {
            return $this->attributes[ $name ];
        }
        
        return $default;
    }
    
    public function setAttribute( $name )
    {
        $this->attributes[ $name ]  =   $value;
        
        return $this;
    }
    
    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes( array $childrenAttributes )
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    public function getChildrenAttribute( $name, $default = null )
    {
        if( isset( $this->childrenAttributes[ $name ] ) ) {
            return $this->childrenAttributes[ $name ];
        }

        return $default;
    }

    public function setChildrenAttribute( $name, $value )
    {
        $this->childrenAttributes[ $name ]  =   $value;

        return $this;
    }
    
    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras( array $extras )
    {
        $this->extras   =   $extras;

        return $this;
    }

    public function getExtra( $name, $default = null )
    {
        if( isset( $this->extras[ $name ] ) ) {
            return $this->extras[ $name ];
        }

        return $default;
    }

    public function setExtra( $name, $value )
    {
        $this->extras[ $name ] = $value;

        return $this;
    }

    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    public function setDisplayChildren( $bool )
    {
        $this->displayChildren  =   ( bool ) $bool;

        return $this;
    }

    public function isDisplayed()
    {
        return $this->display;
    }

    public function setDisplay( $bool )
    {
        $this->display  =   ( bool ) $bool;

        return $this;
    }

    public function addChild( $child, array $options = array() )
    {
        if( !$child instanceof ItemInterface ) 
        {
            $child = $this->factory
                          ->createItem( $child, $options );
        } 
        elseif( null !== $child->getParent() ) 
        {
            throw new InvalidArg( '[error] Cannot add project item as child, it already belongs to another project (e.g. has a parent).' );
        }

        $child->setParent( $this );

        $this->children[ $child->getName() ]    =   $child;

        return $child;
    }

    public function getChild( $name )
    {
        return isset( $this->children[ $name ] ) ? $this->children[ $name ] : null;
    }

    public function moveToPosition( $position )
    {
        $this->getParent()
             ->moveChildToPosition( $this, $position );

        return $this;
    }

    public function moveChildToPosition( ItemInterface $child, $position )
    {
        $name           =   $child->getName();
        $order          =   array_keys( $this->children );

        $oldPosition    =   array_search( $name, $order );
        unset( $order[ $oldPosition ] );

        $order = array_values( $order );

        array_splice( $order, $position, 0, $name );
        $this->reorderChildren( $order );

        return $this;
    }

    public function moveToFirstPosition()
    {
        return $this->moveToPosition( 0 );
    }

    public function moveToLastPosition()
    {
        return $this->moveToPosition( $this->getParent()
                                           ->count() );
    }

    public function reorderChildren( $order )
    {
        if( count( $order ) != $this->count() ) 
        {
            throw new InvalidArg( '[error] Cannot reorder children, order does not contain all children.' );
        }

        $newChildren = array();

        foreach( $order as $name )
        {
            if( !isset( $this->children[ $name ] ) ) {
                throw new InvalidArg( '[error] Cannot find children named ' . $name );
            }

            $child                  =   $this->children[ $name ];
            $newChildren[ $name ]   =   $child;
        }

        $this->children             = $newChildren;

        return $this;
    }

    public function copy()
    {
        $newProject = clone $this;
        $newProject->children = array();
        $newProject->setParent( null );
        foreach( $this->getChildren() as $child ) 
        {
            $newProject->addChild( $child->copy() );
        }

        return $newProject;
    }

    public function slice( $offset, $length = null )
    {
        $names = array_keys( $this->getChildren() );
        if( $offset instanceof ItemInterface )
        {
            $offset = $offset->getName();
        }
        if( !is_numeric( $offset ) ) 
        {
            $offset = array_search( $offset, $names );
        }

        if( null !== $length )
        {
            if( $length instanceof ItemInterface ) 
            {
                $length = $length->getName();
            }
            if( !is_numeric( $length ) ) 
            {
                $index  =   array_search( $length, $names );
                $length =   ( $index < $offset ) ? 0 : $index - $offset;
            }
        }
        $item           = $this->copy();
        $children       =  array_slice( $item->getChildren(), $offset, $length );
        $item->setChildren( $children );

        return $item;
    }

    public function split( $length )
    {
        $ret                =   array();
        $ret[ 'primary' ]   =   $this->slice( 0, $length );
        $ret[ 'secondary' ] =   $this->slice( $length );

        return $ret;
    }

    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    public function getRoot()
    {
        $obj = $this;
        do 
        {
            $found = $obj;
        } 
        while( $obj = $obj->getParent() );

        return $found;
    }

    public function isRoot()
    {
        return null === $this->parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent( ItemInterface $parent = null )
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren( array $children )
    {
        $this->children = $children;

        return $this;
    }

    public function removeChild( $name )
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;

        if( isset( $this->children[ $name ] ) ) {
            // unset the child and reset it so it looks independent
            $this->children[ $name ]->setParent( null );
            unset( $this->children[ $name ] );
        }

        return $this;
    }

    public function getFirstChild()
    {
        return reset( $this->children );
    }

    public function getLastChild()
    {
        return end( $this->children );
    }

    public function hasChildren()
    {
        foreach( $this->children as $child )
        {
            if( $child->isDisplayed() )
            {
                return true;
            }
        }

        return false;
    }    

    public function isLast()
    {
        // if this is root, then return false
        if( $this->isRoot() )
        {
            return false;
        }

        return $this->getParent()
                    ->getLastChild() === $this;
    }

    public function isFirst()
    {
        // if this is root, then return false
        if( $this->isRoot() )
        {
            return false;
        }

        return $this->getParent()
                    ->getFirstChild() === $this;
    }

    public function actsLikeFirst()
    {
        // root items are never "marked" as first
        if ( $this->isRoot() )
        {
            return false;
        }

        // A project acts like first only if it is displayed
        if( !$this->isDisplayed() )
        {
            return false;
        }

        // if we're first and visible, we're first, period.
        if( $this->isFirst() )
        {
            return true;
        }

        $children = $this->getParent()
                         ->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible project. If its this project, we're first
            if( $child->isDisplayed() )
            {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    public function actsLikeLast()
    {
        // root items are never "marked" as last
        if( $this->isRoot() )
        {
            return false;
        }

        // A project acts like last only if it is displayed
        if( !$this->isDisplayed() )
        {
            return false;
        }

        // if we're last and visible, we're last, period.
        if( $this->isLast() )
        {
            return true;
        }

        $children = array_reverse( $this->getParent()
                                        ->getChildren() );
        foreach( $children as $child )
        {
            // loop until we find a visible project. If its this project, we're first
            if( $child->isDisplayed() )
            {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    public function callRecursively( $method, $arguments = array() )
    {
        call_user_func_array( array( $this, $method ), $arguments );

        foreach( $this->children as $child )
        {
            $child->callRecursively( $method, $arguments );
        }

        return $this;
    }

    public function toArray( $depth = null )
    {
        $array = array(
            'name'                  =>  $this->name,
            'description'           =>  $this->description,
            'urls'                  =>  $this->urls,
            'categories'            =>  $this->categories,
            'primary_category'      =>  $this->primary_category,
            'status'                =>  $this->status,
            'attributes'            =>  $this->attributes,
            'childrenAttributes'    =>  $this->childrenAttributes,
            'extras'                =>  $this->extras,
            'display'               =>  $this->display,
            'displayChildren'       =>  $this->displayChildren,
            'slug'                  =>  $this->slug,
            'parent'                =>  $this->parent,
        );

        // export the children as well, unless explicitly disabled
        if( 0 !== $depth )
        {
            $childDepth = ( null === $depth ) ? null : $depth - 1;
            $array[ 'children' ] = array();
            foreach( $this->children as $key => $child )
            {
                $array[ 'children' ][ $key ] = $child->toArray( $childDepth );
            }
        }

        return $array;
    }

    /**
     * Implements Countable
     */
    public function count()
    {
        return count( $this->children );
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->children );
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists( $name )
    {
        return isset( $this->children[ $name ] );
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet( $name )
    {
        return $this->getChild( $name );
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet( $name, $value )
    {
        return $this->addChild( $name );
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset( $name )
    {
        $this->removeChild( $name );
    }
}