<html>
<head>
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

	<style>
		body {
		
		}
		#display {		
			width:1260px;
			margin: 0 auto; 	
		}
		#selectionPanel {
			height: 150px;
			margin-bottom: 25px;
		}		
		#contentPanel {
			
		}
		.panel {
			background: rgba(220,230,244,.25);
			border: 2px solid rgba(220,230,244,.5);
			border-radius: 10px;
			padding: 10px;
		}
		.imgPanel {
			background: rgba(233,237,242,1);
			border: 2px solid rgba(233,237,242,.75);
			border-radius: 5px;
			height: 125px;
			width: 125px;
			margin: 5px;
			float: left;
			display: inline-block;
		}
		.imgPanel > img {
			max-width:95%;
			max-height:95%;
			height:auto;
			display: block;
			margin: auto;
		}
		.imgPanel:hover {
			cursor: pointer;
			transform: scaleY(1.35) scaleX(1.35);
			transform-origin: top left;
			border: 2px solid grey;
			-webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.5);
			-moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.5);
			box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.5);
		}
		.hide {
			display: none !important;
		}
		.loader {
			border: 12px solid #f3f3f3;
			border-top: 12px solid #787878;
			border-radius: 50%;
			width: 50px;
			height: 50px;
			animation: spin 2s linear infinite;
			margin-top: 3em;
		}
		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
	</style>
 
</head>
<body>

	<script>
	$(function() {
	  	  
		//On page load, get all images in the image library
	  	$.ajax({
			url: "ImageController.php",
			dataType: "json",
			type: "POST",
			data: {
				folder: "All"
			},
			success: function (data) {
				
				//process all files
				$.each(data, function(index,files) {
					
					processFiles(index,files);			
					
				});
				
				//Clicked an image panel
				$('.imgPanel').click(function(){
					
				});				
				
				//Once all files processed, lets make our selectable dropdown
				createDropdown();
				createImageTypes();				
			
				//Folder changes
				$('#Album').on("change",function(){

					var album =  $(this).val();
					
					$('.imgPanel').each(function(){

						var folder = $(this).attr("data-folder");
						
						if(folder == album || album == "All"){
							$(this).removeClass("hide");
						}else{
								
							//check if file is in subfolder of root folder
							if(folder.indexOf(album) != -1){
								$(this).removeClass("hide");
							}else{
								$(this).addClass("hide");
							}					
							
						}		
						
					})
					
				});
				
				
				//Changing image type selection
				$('.imageTypeSelector').change(function(){
					var imageType = $(this).attr('id').split('-')[1];
					
					$('.imgPanel').each(function(){
						
						var imgType = $(this).attr("data-imgType");
						var thisPanel = $(this);
												
						$('.imageTypeSelector').each(function(){
														
							var iType = $(this).attr('id').split('-')[1];
							var selection = $(this).prop("checked");
							
							if(iType == imgType){
								if(selection){
									thisPanel.removeClass("hide");
								}else{
									thisPanel.addClass("hide");
								}
							}
						});			
						
					})
					
				});
				
			}
		});		
		
		//Search keyword feature handling
		$('#searchKeywordField').keyup(function(){
			var searchedWords = ($(this).val()).toLowerCase();
			searchedWords = searchedWords.split(" ");
			
			var searchedPanels = [];
			
			searchedWords.forEach(function(value,index,array){
				var searchedWord = value;
				
				if(searchedWord == ''){				
					$('#Album').val('All');
					$('#Album').trigger('change');				
				}else{
					$('.imgPanel').each(function(){
						var imgPanel = $(this);
						var folder = ($(this).attr("data-folder")).toLowerCase();
						var title = ($(this).attr("title")).toLowerCase();					
						var imgID = ($(this).find('img').attr('id')).toLowerCase();
						var imgSrc = ($(this).find('img').attr('src')).toLowerCase();
						
						var searchData = [folder,title,imgID,imgSrc];
						
						searchData.forEach(function(value,index,array){
							
							if(!value.includes(searchedWord)){
								imgPanel.addClass("hide");
							}else{
								searchedPanels.push(imgPanel);
							}
							
						})					
						
					});				
				}
				
			});
			
			searchedPanels.forEach(function(value,index,array){
				
				value.removeClass("hide");
				
			});
				
		});
	});

	//Create image type selector section
	function createImageTypes(){
		var imageTypes = [];
		
		$('.imgPanel').each(function(){
			var imageType = $(this).attr("data-imgType");
			
			if(imageTypes.indexOf(imageType) == -1){
				imageTypes.push(imageType);
				
				if(imageType != "ai"){
					$('#imageTypePanel').append('<input class="imageTypeSelector" id="imageType-' + imageType +  '" type="checkbox" checked="checked" />' + imageType + '&nbsp;&nbsp;');
				}else{
					$('#imageTypePanel').append('<input class="imageTypeSelector" id="imageType-' + imageType +  '" type="checkbox" />' + imageType + '&nbsp;&nbsp;');
				}
			}
		});
	}

	//Create dropdown menu of available folders
	function createDropdown(){
		var folderNames = [];
		
		$('.imgPanel').each(function(){
			
			var folder = $(this).attr("data-folder");
			
			if(folderNames.indexOf(folder) == -1){				
				
				folderNames.push(folder);
				
				//check if folder is sub folder
				var subFolder = folder.split("/");
				if(subFolder.length > 1){
					
					//check if top parent folder is in array, if not add it first
					if(folderNames.indexOf(subFolder[0]) == -1){
						folderNames.push(subFolder[0]);
						$('#Album').append('<option value="' + subFolder[0] + '">' + subFolder[0] + '</option>');
					}
					
					var folderIndent = folderSelectIndent(subFolder.length);
					$('#Album').append('<option value="' + folder + '">' + folderIndent + subFolder[subFolder.length - 1] + '</option>');
				}else{
					$('#Album').append('<option value="' + folder + '">' + folder + '</option>');			
				}
			}
		});
		
	}
	
	//Adding indent to the dropdown menu
	function folderSelectIndent(num){
		var retval = '';
		
		while(num > 1){
			retval += '&nbsp;>&nbsp;';
			num--;
		}
		
		return retval;
	}
	
	//Making image ID uniqe for loading search
	function imageIDUnique(split){
		var retval = '';
		
		//How many subs deep
		var num = split.length -1;
		//Parent folder for uniqueness
		var preKey = split[num];
		
		retval += preKey;
		while(num > 0){
			retval += '_';
			num--;
		}
		retval = retval.replace(/\s+/g, '');
		
		return retval;
	}
	
	//Process each file
	function processFiles(index,files){
		if(Array.isArray(files)){					
			files = toObject(files);
		}	
		
		$.each(files, function(key, val){
			if(isNaN(key)){
				processFiles(index + "/" + key,val);		
			}else{
				createImagePanel(index,val);	
			}			
		});
	}
	
	//Create each image panel and handle loading
	function createImagePanel(location,filename)
	{
		//IDs cant have . in them, pull filename without extension
		var newID = filename.split(".");
		var newIDSpaceRemoved = newID[0].replace(/\s+/g, '');
		
		//Need to make IDs unique, add + for number of folders in
		var uniqueHandle = imageIDUnique(location.split("/"));
		
		//Get source location
		var source = 'SortedAssets/' + location + '/' + filename;
		var imgType = newID[newID.length - 1].toLowerCase();
		
		if(newID[newID.length - 1].toLowerCase() == "ai"){
			source = 'aiPlaceholder.png';			
		}
		$('#contentPanel').append('<div class="imgPanel" data-folder="' + location + '" data-imgType="' + imgType + '" title="' + filename + '"><div class="loader"></div><img class="hide" id="' + uniqueHandle + newIDSpaceRemoved + imgType + '" src="' + source +'"  /></div>');
		
		//when image loads, remove loader guy and show the image by removing hide class
		$('#' + uniqueHandle + newIDSpaceRemoved + imgType).on("load",function(){
			$('#' + uniqueHandle + newIDSpaceRemoved + imgType).siblings('.loader').remove();
			$('#' + uniqueHandle + newIDSpaceRemoved + imgType).removeClass("hide");
		})
	}
	
	//Array to object conversion for ease of use
	function toObject(arr) {
		var retval = {};
		for (var i = 0; i < arr.length; ++i){
			retval[i] = arr[i];
		}
		
		return retval;
	}
	</script>
	
	<div id='display'>
		<div id='selectionPanel' class='panel'>
			<label for='Album'>Album: </label>
			<select id='Album' class="">
				<option value='All'>&gt;&gt; All &lt;&lt;</option>
			</select>
			<hr/>
			<span id='imageTypePanel'>Image Types: </span>
			<hr/>
			<span id='searchFeature'>Keyword Search: <input type='text' id='searchKeywordField' placeholder='Search keywords'/></span>
		</div>
		<div id='contentPanel' class=''>
			
		</div>
	</div>
</body>
</html>
<?php


?>
