<?php

namespace Aboustayyef\Previewer;
use Symfony\Component\DomCrawler\Crawler as Crawler;
use Aboustayyef\Previewer\Utilities\FastImage as FastImage;

class ImageScraper
{
	protected $crawler, $url;

	public function __construct($crawler, $url){
		$this->crawler = $crawler;
        $this->url = $url;
	}

	public function getImage()
    {
        
        $openGraphImage = $this->getImageFromOpenGraphTags();
        if ($openGraphImage) {
        	return $openGraphImage;
        }

        $ImgTagsImage = $this->getImageFromImgTags();
        if($ImgTagsImage){
            return $ImgTagsImage;
        }


        return false;
    }

    private function getImageFromOpenGraphTags()
    {
        $image = $this->crawler->filter('meta[property="og:image"]');
    	if ($image->count() > 0) {
    		return $image->first()->attr('content');
    	}
    	return false;
    }

    private function getImageFromImgTags()
    {
        $images = $this->crawler->filter('img');
    	if ($images->count() > 0) {

            $foundImages = array();

    		foreach ($images as $key => $image) {
                $foundImages[$key] = array();
                $foundImages[$key]['src'] = $image->getAttribute('src');
                $foundImages[$key]['width'] = (int) $image->getAttribute('width');
                $foundImages[$key]['height'] = (int) $image->getAttribute('height');
    		}

            // fill the gaps and harmonize list (image sizes and remove relative url)
            $list = $this->FillGapsInListOfImages($foundImages);

            // go through images to select the best
            $finalImage = $this->ProcessListOfImages($list);

            return $finalImage['src'];
    	}
    	return false;
    }

    private function FillGapsInListOfImages($list){


        // find root url to deal with relative images
        $pieces = parse_url($this->url);
        $rootUrl = $pieces['scheme'].'://'.$pieces['host'];

        foreach ($list as $key => $image) {

            // fix relative image
            if ($image['src'][0] == '/') {
                $list[$key]['src'] = $rootUrl . $image['src'];
            }

            if ( (!$image['width']) || ($image['width'] == "auto") || (!$image['height']) || ($image['height'] == "auto") ) {
                $img = new FastImage($list[$key]['src']);
                list($width, $height) = $img->getSize();
                $list[$key]['width'] = $width;
                $list[$key]['height'] = $height;
            }

        }
        return $list;
    }

    private function ProcessListOfImages($list){
        
        $candidate = array();
        $minHeight = 100;
        $minWidth = 100;
        $bannerRatio = 3;

        foreach ($list as $key => $image) {

            // ignore small images
            if ( ($image['width'] < $minWidth) || ($image['height'] < $minHeight)) {
                echo "Ignored " . $image['src'] . " because it's too small \n";
                continue;
            }

            // ignore images that are too thin like headers;
            if ( (($image['width']/$image['height']) > $bannerRatio) || (($image['height']/$image['width']) > $bannerRatio) ){
                echo "Ignored " . $image['src'] . " because it's too thin \n";
                continue;
            }

            // if candidate is empty, make this image the candidate
            if (!$candidate) {
                $candidate = $image;
                continue;
            }

            // otherwise, compare surface to see which is bigger
            if ( ($image['width'] * $image['height']) > ($candidate['width'] * $candidate['height']) ) {
                $candidate = $image;
            }

        }

        return $candidate;
    }
}
