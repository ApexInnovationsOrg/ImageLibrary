<?php
//Class for handling image library requests

$imageParse = new ImageParser($_GET);
$thumbnail = $imageParse->parse();

class ImageParser
{
	protected $src;
	protected $passedSource;
	protected $fileFormat; //.png, .gif, .jpg, .psd, .ai
	private $supportedConversionFormats = array(".psd",".ai");
	
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
		$this->fileFormat = $this->getFileFormat($src[1]);
		
		if(in_array($this->fileFormat,$this->supportedConversionFormats)){
			$pngSrc = str_replace($this->fileFormat, ".png", $src[1]);
			$this->passedSource = "Thumbnails/" . $pngSrc;
			
			if(!file_exists($this->passedSource)){
				$this->previewSupportedFormat($src[1]);
				$this->createThumbnailFile();
			}			
		}else{		
			if(!file_exists($this->passedSource)){
				$this->createThumbnailFile();
			}
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
		
		$this->createDirectoryStructure($this->passedSource);
		
		//Save image to filesystem
		$newThumb->writeImage($this->passedSource);
	}
	
	private function createDirectoryStructure($passedSrc)
	{
		//Create folder structure
		$directoryStructure = dirname($passedSrc);			
		$fileName = basename($passedSrc);
		if(!file_exists($directoryStructure) && !is_dir($directoryStructure)){
			mkdir($directoryStructure,0755,true);	
		}
	}
	
	private function previewSupportedFormat($src)
	{
		$newPreview = new Imagick($this->src);
		$newPreview->setImageFormat("png");		
		
		$this->createDirectoryStructure("ConvertedAssets/" . $src);
		
		$newSrc = str_replace($this->fileFormat, ".png", $src);
		$newPreview->writeImage("ConvertedAssets/" . $newSrc);

	}
	
	private function getFileFormat($file)
	{
		//Split file on extension
		$fileExplode = explode("/",$file);
		
		$filename = $fileExplode[count($fileExplode)-1];
		
		$retval = explode(".",$filename);
		
		return "." . $retval[1];
	}
	
}

?>
