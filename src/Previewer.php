<?php

namespace Aboustayyef;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class Previewer
{

	protected $content;
	public $crawler;
    public $url;

	public function __construct($url){
        $this->url = $url;
		try {
        	$this->content = @file_get_contents($url);
        	if (strlen($this->content) > 10) {
                $this->crawler = new Crawler;
                $this->crawler->addHTMLContent($this->content, 'UTF-8');
        	}
        } catch (Exception $e) {
        	echo "couldn't extract url";
        }
	}

	private function hasContent(){
		if (strlen($this->content) > 10) {
			return true;
		}
		return false;
	}

	private function hasCrawler(){
		if (is_object($this->crawler)) {
			return true;
		}
		return false;
	}

    public function getTitle(){
        return (new TitleScraper($this->crawler))->getTitle();
    }

    public function getDescription(){
        return (new DescriptionScraper($this->crawler))->getDescription();
    }

    public function getImage(){
        return (new ImageScraper($this->crawler, $this->url))->getImage();
    }

}
