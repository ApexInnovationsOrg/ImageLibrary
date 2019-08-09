<?php
//Class for handling image library requests

$imageParse = new ImageParser($_GET);
$thumbnail = $imageParse->parse();

class ImageParser
{
	protected $src;
	protected $thumbnail;
	
	public function __construct($params)
    {
		$this->src = $params['src'];	
		$this->thumbnail = $params['thumb'];
    }
	
	public function parse()
	{
		//Check if image thumbnail exists. If not, create it, save it, then pull it.
		$src = explode("SortedAssets/",$this->src);
		$newSrc = "Thumbnails/" . $src[1];
		if(!file_exists($newSrc)){
			$newThumb = new Imagick($this->src);		
			$imgWidth = $newThumb->getImageWidth();
			$imgHeight = $newThumb->getImageHeight();				
			$newThumb->thumbnailImage(100,100,true);	
			
			$directoryStructure = dirname($newSrc);			
			$fileName = basename($newSrc);
			if(!file_exists($directoryStructure)){
				mkdir($directoryStructure,0755,true);	
			}
			$newThumb->writeImage($newSrc);
		}
				
		header('Content-type: ' . mime_content_type($newSrc));	
		readfile($newSrc);
		
	}	
	
	
}

?>
