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
		
		$newThumb = new Imagick($this->src);		
		$imgWidth = $newThumb->getImageWidth();
		$imgHeight = $newThumb->getImageHeight();				
		$newThumb->thumbnailImage(100,100,true);
		
		$mimeType = $newThumb->getImageMimeType();
		
		header('Content-type: ' . $mimeType);	
		echo $newThumb;
		
	}	
	
	
}

?>
