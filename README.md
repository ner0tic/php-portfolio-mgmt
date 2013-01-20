Portfolio Mgmt
==============
This is the core library used to manage my project portfolio via an api styled 
library system.

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

## Installation

No Autoloader, but does follow PSR-0

Add to `composer.json`:
```js
// ...
"ner0tic/php-portfolio-mgmt": "dev-master",
// ...
```

## Usage

[01. Basic Usage](http://github.com/ner0tic/php-portfolio-mgmt/blob/master/doc/01-Basic-Usage.md)
[02. Advanced Usage](http://github.com/ner0tic/php-portfolio-mgmt/blob/master/doc/02-Advanced-Usage.md)