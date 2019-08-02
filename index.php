<html>
<head>
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

	<style>
		body {
			background-color: #ccc;
			background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHZlcnNpb249JzEuMScgd2lkdGg9JzQwMCcgaGVpZ2h0PSc0MDAnPgoJPGRlZnMgaWQ9J2RlZnM0Jz4KCQk8ZmlsdGVyIGNvbG9yLWludGVycG9sYXRpb24tZmlsdGVycz0nc1JHQicgaWQ9J2ZpbHRlcjMxMTUnPgoJCQk8ZmVUdXJidWxlbmNlIHR5cGU9J2ZyYWN0YWxOb2lzZScgbnVtT2N0YXZlcz0nMScgYmFzZUZyZXF1ZW5jeT0nMC45JyBpZD0nZmVUdXJidWxlbmNlMzExNycgLz4KCQkJPGZlQ29sb3JNYXRyaXggcmVzdWx0PSdyZXN1bHQ1JyB2YWx1ZXM9JzEgMCAwIDAgMCAwIDEgMCAwIDAgMCAwIDEgMCAwIDAgMCAwIDYgLTQuMzUwMDAwMDAwMDAwMDAwNSAnIGlkPSdmZUNvbG9yTWF0cml4MzExOScgLz4KCQkJPGZlQ29tcG9zaXRlIGluMj0ncmVzdWx0NScgb3BlcmF0b3I9J2luJyBpbj0nU291cmNlR3JhcGhpYycgcmVzdWx0PSdyZXN1bHQ2JyBpZD0nZmVDb21wb3NpdGUzMTIxJyAvPgoJCQk8ZmVNb3JwaG9sb2d5IGluPSdyZXN1bHQ2JyBvcGVyYXRvcj0nZGlsYXRlJyByYWRpdXM9JzE1JyByZXN1bHQ9J3Jlc3VsdDMnIGlkPSdmZU1vcnBob2xvZ3kzMTIzJyAvPgoJCTwvZmlsdGVyPgoJPC9kZWZzPgoJPHJlY3Qgd2lkdGg9JzEwMCUnIGhlaWdodD0nMTAwJScgeD0nMCcgeT0nMCcgaWQ9J3JlY3QyOTg1JyBmaWxsPScjY2NjY2NjJy8+ICAgICAKCTxyZWN0IHdpZHRoPScxMDAlJyBoZWlnaHQ9JzEwMCUnIHg9JzAnIHk9JzAnIGlkPSdyZWN0Mjk4NScgc3R5bGU9J2ZpbGw6I2ZmZmZmZjtmaWx0ZXI6dXJsKCNmaWx0ZXIzMTE1KScgLz4KPC9zdmc+);
			position: relative;
			z-index: 1;
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
			background: rgba(229,106,106,.75);
			border: 2px solid rgba(121,92,92,1);
			border-radius: 10px;
			padding: 10px;
		}
		.imgPanel {
			background: rgba(255,255,255,1);
			border: 2px solid rgba(239,239,239,1);
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
	
	<div id='display' class=''>
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
