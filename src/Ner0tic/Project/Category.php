<?php
namespace Ner0tic\PortfolioMgmt;

class Category implements \ArrayAccess, \Countable, \IteratorAggregate
{
    protected   $name           =   null,
                $description    =   null,
                $display        =   true,
                $attributes     =   array(),
                $extras         =   array(),
                $children       =   array(),
                $parent         =   null;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName( $name ) {
        $this->name = $name;
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
            throw new InvalidArg( '[error] Cannot add category as child, it already belongs to another category (e.g. has a parent).' );
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

    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
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

        // A category acts like first only if it is displayed
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
            // loop until we find a visible category. If its this category, we're first
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

        // A category acts like last only if it is displayed
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
            // loop until we find a visible category. If its this category, we're first
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
            'attributes'            =>  $this->attributes,
            'childrenAttributes'    =>  $this->childrenAttributes,
            'parent'                =>  $this->parent,
            'extras'                =>  $this->extras,
            'display'               =>  $this->display,
            'displayChildren'       =>  $this->displayChildren,
            'slug'                  =>  $this->slug,
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
