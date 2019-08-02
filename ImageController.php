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
		
		return $this->directoryArray($this->dirLocation);		
	}
	
	private function getDirectoryLocation()
	{
		switch($this->folder){
			case 'All':
				$this->dirLocation = dirname(realpath(__FILE__)).'/Library/';
				break;
			default:
				$this->dirLocation = dirname(realpath(__FILE__)).'/Library/' . $this->folder;
				break;
		}

	}
	
	private function directoryArray($directory) 
	{ 
   
		$retval = array(); 


		$dir = scandir($directory); 
		foreach ($dir as $key => $value){

			if (!in_array($value,array(".","..")) && $value != "Thumbs.db"){   

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
