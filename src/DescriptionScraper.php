<?php

namespace Aboustayyef\Previewer;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class DescriptionScraper
{
	protected $crawler;

	public function __construct($crawler){
		$this->crawler = $crawler;
	}

	public function getDescription()
    {
        
        $metaTagsDescription = $this->getDescriptionFromMetaTags();
        if ($metaTagsDescription) {
        	return $metaTagsDescription;
        }

        $openGraphDescription = $this->getDescriptionFromOpenGraphTags();
        if ($openGraphDescription) {
        	return $openGraphDescription;
        }

        $PTagsDescription = $this->getDescriptionFromPTags();
        if($PTagsDescription){
        	return $PTagsDescription;
        }

        return false;
    }

    private function getDescriptionFromMetaTags(){
    	$descriptions = $this->crawler->filter('meta[name="description"]');
    	if ($descriptions->count() > 0) {
    		return $descriptions->first()->attr('content');
    	}
    	return false;
    }


    private function getDescriptionFromOpenGraphTags()
    {
        $descriptions = $this->crawler->filter('meta[property="og:description"]');
    	if ($descriptions->count() > 0) {
    		return $descriptions->first()->attr('content');
    	}
    	return false;
    }

    private function getDescriptionFromPTags()
    {
        $descriptions = $this->crawler->filter('p');
    	if ($descriptions->count() > 0) {
    		foreach ($descriptions as $key => $description) {
    			if (strlen($description->textContent) > 60) {
    				return $description->textContent;
    			}
    		}
    	}
    	return false;
    }

}
