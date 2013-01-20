Basic Usage
===========
The basic rundown...

Create a portfolio
------------------
The portfolio framework's core is the interface `Ner0tic\PortfolioMgmt\ItemInterface`.   Items are created by a factory implementing Ner0tic\PortfolioMgmtFactoryInterface`.   
The easiest way to think about it is that each `ItemInterface` object as a project - in an unordered list each one would be a `<li>` tag with nested children.
An Example:
```php
<?php

use Ner0tic\PortfolioMgmt\PortfolioFactory,
    Ner0tic\PortfolioMgmt\Renderer\ListRenderer;

// Create a Master ProjectItem to house the projects (your portfolio)
$factory = new PortfolioFactory();
$portfolio = $factory->createItem('My Portfolio');

// Add a project the traditional way
$portfolio->addChild( 'Project A', array( 'description' => 'blah blah blah...' ) );

// Add a project the minimal way.
$portfolio->addChild( 'Project B' );
$portfolio[ 'Project B' ]->setUrl( 'dev', 'http://dev.localhost/app_dev.php/');

// Add a project and assign it a child project (sub-project / dependency / module / etc... )
$portfolio->addChild( 'Project C' );
$portfolio[ 'Project C' ]->SetParent( $portfolio['Project A'] );

// Render the portfolio
$renderer = new ListRenderer();

echo $renderer->render( $portfolio );
```

This would render:

```html
<ul>
    <li class="first">Project A</li>
    <li>Project B</li>
    <li class="last">Project C
        <ul>
            <li>Children:
                <ul>
                    <li>Project A</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>        
```
>**NOTE** This framework automagically adds `first` and `last` classes to each `<li>` tag for each level.

When the project is rendered, it's actually spaced correctly so that it appears as showin in the souce html.  This eases debugging and can be disabled by ammending `true` as the second parameter to the renderer.
```php
<?php 
//...
$renderer = new ListRenderer( new Matcher() );
echo $renderer->render( $portfolio, array( 'compressed' => true ) );
```

Working with the tree
---------------------
The tree works and acts like a multi-dimensional array.  Specifically, it implements ArrayAccess, Countable and Iterator.
```php
<?php

use Ner0tic\PortfolioMgmt\PortfolioFactory;

$factory = new PortfolioGactory();
$portfolio = new $factory->createItem( 'My Portfolio' );

$portfolio->addChild( 'Project A' );
$portfolio->addChild( 'Portfolio B' );

// ArrayAccess
$portfolio[ 'Project A' ]->setUrl( 'dev', 'http://dev.example.com/');
$portfoliop[ 'Project A ']->addChild( 'Project A-1', array('urls' => array('dev' => 'http://dev.localhost/project-a-1')));

// Countable
echo count( $portoflio ); // returns 2

// Iterator
foreach( $portfolio as $project )
{
    echo $project->getName();
}
```
>**NOTE** Notice that the name you give the project upon creation is how you access it via the `ArrayAccess`.

Project Attributes
------------------
You can add any attribute to the `<li>` tag of a portfolio item.  This can be done when creating a portfolio item or via the `setAttribute()` and `setAttributes()` methods.
```php
<?php
$portfolio->addChild( 'Project A', array( 'attributes'  => array( 'id'  =>  'project-a' ) ) );
$portfolio[' Project A' ]->setAttribute( 'id', 'project-a' );
```
>**NOTE** `setAttributes()` will overwrite all exiting attributes.

### Remove Attributes
Removing an attribute is done simply by setting it to `null`.
```php
$portfolio[ 'Project B '] = null;
```

Rendering only part of a portfolio
----------------------------------
If you need to render only part of your portfolio, the framework gives you the access to do so.
```php
<?php
// render only 2 levels deep (root, parents, children)
$renderer->render( $portfolio, array( 'depth' => 2 ) );

// rendering everything except for the children of Project A
$portfolio[ 'Project A' ]->setDisplayChildren( false );
$renderer->render( $portfolio );

// render ecerything except for Project A and its children
$portfolio[ 'Project A' ]->setDisplay( false );
$renderer->render( $portfolio );
```

This allows for a quick-sort if you will.

Other Rendering options
-----------------------
Most renderers also support several other options, which can be passed as the second parameter to the `render()` method.

* `depth`
* `currentClass` (default: `current_ancestor`)
* `firstClass` (default: `first`)
* `lastClass` (default: `last`)
* `compressed` (default: `false`)
* `clear_matcher` (default: `true`) whether to clear the internal cache of the matcher after rendering

Creating a portfolio from a Tree structure
------------------------------------------
You can create a portfolio from a Tree structure (a nested set for example) quite easily by making it implement `Ner0tic\PortfolioMgmt\NodeInterface`.  You will then be able to create the portoflio easily (assuming `$node` is the root node of your structure).
```php
<?php
$factory = new Ner0tic\PortfolioMgmt\PortfolioFactory();
$portfolio = new $factory->createFromNode( $node );
```
