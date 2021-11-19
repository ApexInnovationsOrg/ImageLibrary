<?php
//Class for handling image library requests

$imgCtrl = new ImageController($_POST);
$fileStructure = $imgCtrl->parse();

echo json_encode($fileStructure);


class ImageController
{
	private $folder;
	private $dirLocation;
	private $badCharacters = ['(',')'];
	
	public function __construct($params)
    {
		$this->folder = $params['folder'];
    }
	
	public function parse()
	{
		$this->getDirectoryLocation();
		$directoryArray = $this->directoryArray($this->dirLocation);		
		
		return $directoryArray;
	}
	
	private function getDirectoryLocation()
	{
		switch($this->folder){
			case 'All':
				$this->dirLocation = dirname(realpath(__FILE__)).'/SortedAssets/';
				break;
			default:
				$this->dirLocation = dirname(realpath(__FILE__)).'/SortedAssets/' . $this->folder;
				break;
		}

	}
	
	private function directoryArray($directory) 
	{ 
   
		$retval = array(); 

		$dir = scandir($directory); 
		foreach ($dir as $key => $value){

			if (!in_array($value,array(".","..","Thumbs.db",".DS_Store"))){   

				if (is_dir($directory . "/" . $value)){ 
					$retval[$value] = $this->directoryArray($directory . "/" . $value); 
				}else{ 
					$value = $this->cleanFileName($value,$directory);

					$retval[] = $value; 
				} 

			} 
		} 

		return $retval; 
	} 

	private function cleanFileName($fileName, $directory)
	{
		$retval = $fileName;

		$cleanedName = $fileName;
		$cleanIt = false;
		
		foreach($this->badCharacters as $badCharacter)
		{
			if (strpos($cleanedName, $badCharacter) !== false) {
				$cleanIt = true;
			}
		}

		if($cleanIt)
		{
			$retval = str_replace($this->badCharacters,'',$cleanedName);

			$cleanedName = $directory . "/" . str_replace($this->badCharacters,'',$cleanedName);			
			$fileName = $directory . "/" . $fileName;	

			rename($fileName, $cleanedName);
		}

		return $retval;
	}
	
}

?>
