<?php
//Class for handling image library requests

$imageParse = new ImageParser($_GET);
$thumbnail = $imageParse->parse();

class ImageParser
{
	protected $src;
	protected $passedSource;
	
	public function __construct($params)
    {
		$this->src = $params['src'];	
		$this->thumbnail = $params['thumb'];
    }
	
	public function parse()
	{
		//Check if image thumbnail exists. If not, create it, save it, then pull it.
		$src = explode("SortedAssets/",$this->src);
		$this->passedSource = "Thumbnails/" . $src[1];
		if(!file_exists($this->passedSource)){
			$this->createThumbnailFile();
		}
				
		header('Content-type: ' . mime_content_type($this->passedSource));	
		readfile($this->passedSource);		
	}	
	
	private function createThumbnailFile()
	{
		//Ceate thumbnail
		$newThumb = new Imagick($this->src);		
		$imgWidth = $newThumb->getImageWidth();
		$imgHeight = $newThumb->getImageHeight();				
		$newThumb->thumbnailImage(100,100,true);	
		
		$this->createDirectoryStructure();
		
		//Save image to filesystem
		$newThumb->writeImage($this->passedSource);
	}
	
	private function createDirectoryStructure()
	{
		//Create folder structure
		$directoryStructure = dirname($this->passedSource);			
		$fileName = basename($this->passedSource);
		if(!file_exists($directoryStructure) && !is_dir($directoryStructure)){
			mkdir($directoryStructure,0755,true);	
		}
	}
	
}

?>
