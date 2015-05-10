<?php

namespace Aboustayyef\Previewer;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class TitleScraper
{
	protected $crawler;

	public function __construct($crawler){
		$this->crawler = $crawler;
	}

	public function getTitle()
    {
        return trim($this->crawler->filter('title')->text());
    }
}
