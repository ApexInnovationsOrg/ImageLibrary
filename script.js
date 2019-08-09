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
					
				});				
				
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
		var selectedAlbum = ($('#Album').val()).toLowerCase();
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
					var parentFolder = ((folder.split('/'))[0]).toLowerCase();
											
					var searchData = [folder,title,imgID,imgSrc];
					
					searchData.forEach(function(value,index,array){
						
						if(!value.includes(searchedWord) || (parentFolder != selectedAlbum && selectedAlbum != 'all')){
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

	$('#imageTypePanel').append('|&nbsp;&nbsp;');

	$('.imgPanel').each(function(){
		var imageType = $(this).attr("data-imgType");
		
		if(imageTypes.indexOf(imageType) == -1){
			imageTypes.push(imageType);
			
			if(imageType != "ai"){
				$('#imageTypePanel').append('<input class="imageTypeSelector" id="imageType-' + imageType +  '" type="checkbox" checked="checked" />' + imageType + '&nbsp;&nbsp;|&nbsp;&nbsp;');
			}else{
				$('#imageTypePanel').append('<input class="imageTypeSelector" id="imageType-' + imageType +  '" type="checkbox" />' + imageType + '&nbsp;&nbsp;|&nbsp;&nbsp;');
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
				// if(folderNames.indexOf(subFolder[0]) == -1){
					// folderNames.push(subFolder[0]);
					// $('#Album').append('<option value="' + subFolder[0] + '">' + subFolder[0] + '</option>');
				// }
				
				// var folderIndent = folderSelectIndent(subFolder.length);
				// $('#Album').append('<option value="' + folder + '">' + folderIndent + subFolder[subFolder.length - 1] + '</option>');
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
function createImagePanel(location,filename){
	//IDs cant have . in them, pull filename without extension
	var newID = filename.split(".");
	var newIDSpaceRemoved = newID[0].replace(/\s+/g, '');

	//Need to make IDs unique, add + for number of folders in
	var uniqueHandle = imageIDUnique(location.split("/"));

	//Get source location
	var source = 'SortedAssets/' + location + '/' + filename;
	var imgType = newID[newID.length - 1].toLowerCase();

	if(newID[newID.length - 1].toLowerCase() == "ai"){
		source = 'SortedAssets/PlaceholderImages/aiPlaceholder.png';			
	}
	$('#contentPanel').append('<div class="imgPanel" data-folder="' + location + '" data-imgType="' + imgType + '" title="' + filename + '"><div class="loader"></div><img class="hide" id="' + uniqueHandle + newIDSpaceRemoved + imgType + '" src="ImageParser.php?thumb=1&src=' + source +'"  /></div>');

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