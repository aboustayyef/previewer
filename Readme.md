#Previewer for URLs

This class takes a url as input and outputs a Title, a Description and an Image for that url

##Usage

```
<?php 

use Aboustayyef\Previewer;

$previewer = new Previewer('http://url.goes/here');
$title = $previewer->getTitle();
$description = $previewer->getDescription();
$image = $previewer->getImage();

?>

```