# README #

EAC stands for External Assets Compiler.

EAC does the following:

1. Takes a number of HTML files as input ("sources")
2. From each file extracts regions marked with <!-- eac:compile -->...<!-- /eac:compile --> ("chunks")
3. For each region, minifies js inside region ("compiles")
4. And finally, replaces contents of the region with a single script tag.

Effectively it is the same that Assetic twig extension does with assets enclosed in {% javascripts %} tag in .twig templates when Symfony 2's application container is being compiled ([Read more about {% javascripts %}](http://symfony.com/doc/current/cookbook/assetic/asset_management.html#including-javascript-files)).

### Installation ###

To run EAC requires PHP 5.3+, mbstring extension, some JRE to run YUI Compressor and YUI Compressor itself.

#### Pre-built phar ####

`build/eac.phar` is the latest phar build from sources.
Also available from downloads section.

#### From sources ####

`$ git clone git@bitbucket.org:artsafin/eac.git`

`$ cd eac && composer install`

#### Run tests ####

`$ git clone git@bitbucket.org:artsafin/eac.git`

`$ cd eac && composer install`

`$ phpunit -c phpunit.xml.dist`

### Basic usage ###

#### List available commands ####

`$ php eac.phar`

#### Inspect sources ####

Just show found assets, which will be compiled with eac:compile.

`$ php eac.phar eac:sources [-d|--depth="..."] [-m|--mode="..."] webroot source1 ... [sourceN]`

Example:

`$ php eac.phar eac:sources --mode="js" /var/www/example.com/htdocs /var/www/example.com/htdocs/templates/*`

#### Compile ####

`$ php eac.phar eac:compile [-d|--depth="..."] [-m|--mode="..."] [--out="..."] [--prefix="..."] [--yuicompressor="..."] [--replace] webroot source1 ... [sourceN]`

Example:

`$ php eac.phar eac:compile --mode="js" --out="/var/www/example.com/htdocs/compiled" --prefix="compiled" --yuicompressor="/opt/yuicompressor-2.4.7.jar" --replace /var/www/example.com/htdocs /var/www/example.com/htdocs/compiled_templates`