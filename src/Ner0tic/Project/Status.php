<?php
namespace Ner0tic\PortfolioMgmt;

class Status
{
    protected   $name           =   null,
                $description    =   null,
                $display        =   true,
                $attributes     =   array(),
                $extras         =   array();
    
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
    
    public function isDisplayed()
    {
        return $this->display;
    }

    public function setDisplay( $bool )
    {
        $this->display  =   ( bool ) $bool;

        return $this;
    }
}