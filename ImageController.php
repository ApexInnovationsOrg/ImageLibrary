<?php
//Class for handling image library requests

$imgCtrl = new ImageController($_POST);
$fileStructure = $imgCtrl->parse();

echo json_encode($fileStructure);


class ImageController
{
	private $folder;
	private $dirLocation;
	
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

			if (!in_array($value,array(".","..")) && $value != "Thumbs.db" && $value != ".DS_Store"){   

				if (is_dir($directory . "/" . $value)){ 
					$retval[$value] = $this->directoryArray($directory . "/" . $value); 
				}else{ 
					$retval[] = $value; 
				} 

			} 
		} 

		return $retval; 
	} 
	
}

?>
