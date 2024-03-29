<?php

namespace Ner0tic\PortfolioMgmt\Renderer;

use Ner0tic\PortfolioMgmt\ItemInterface;
use Ner0tic\PortfolioMgmt\Matcher\MatcherInterface;

class TwigRenderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $matcher;
    private $defaultOptions;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct( \Twig_Environment $environment, $template, MatcherInterface $matcher, array $defaultOptions = array() )
    {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge( array( 
            'depth' => null,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
         ), $defaultOptions );
    }

    public function render( ItemInterface $item, array $options = array() )
    {
        $options = array_merge( $this->defaultOptions, $options );

        $template = $options['template'];
        if ( !$template instanceof \Twig_Template ) {
            $template = $this->environment->loadTemplate( $template );
        }

        $block = $options['compressed'] ? 'compressed_root' : 'root';

        $html = $template->renderBlock( $block, array( 'item' => $item, 'options' => $options, 'matcher' => $this->matcher ) );

        if ( $options['clear_matcher'] ) {
            $this->matcher->clear();
        }

        return $html;
    }
}