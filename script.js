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
				var folder = $(this).attr("data-folder");
				var title = $(this).attr("title");				
				
				//Determine if file is .ai converted				
				if(title.substring(title.length-3) == ".ai"){
					PopupCenter('SortedAssets/' + folder + '/' + title,'xtf','900','500');	
					title = title.substring(0,(title.length-3)) + ".png";			
					setTimeout(function(){ 
						PopupCenter('ConvertedAssets/' + folder + '/' + title,'xtf','900','500');
					}, 500);					 		
				}else{					
					PopupCenter('SortedAssets/' + folder + '/' + title,'xtf','900','500');  
				}				
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

function PopupCenter(url, title, w, h) {
	// Fixes dual-screen position                         Most browsers      Firefox
	var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
	var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

	var systemZoom = width / window.screen.availWidth;
	var left = (width - w) / 2 / systemZoom + dualScreenLeft
	var top = (height - h) / 2 / systemZoom + dualScreenTop
	var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

	// Puts focus on the newWindow
	if (window.focus) newWindow.focus();
}

//Create image type selector section
function createImageTypes(){
	var imageTypes = [];	
	
	$('.imgPanel').each(function(){
		var imageType = $(this).attr("data-imgType");
		
		if(imageTypes.indexOf(imageType) == -1){
			imageTypes.push(imageType);
			
			$('#imageTypePanel').append('<div class="cursor"><label class="cursor smPadding"><input class="imageTypeSelector" id="imageType-' + imageType +  '" type="checkbox" checked="checked" />' + imageType + '</label></div>');			
		}
	});
	
	$('#smallLoad').remove();	
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
			if(subFolder.length == 0){				
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
	
	var imgIcon = '';
	if(newID[1] == "ai"){
		imgIcon = '<img class="aiIcon" src="aiPlaceholder.png" />';
	}
	
	$('#contentPanel').append('<div class="imgPanel" data-folder="' + location + '" data-imgType="' + imgType + '" title="' + filename + '"><div class="loader"></div>' + imgIcon + '<img class="hide" id="' + uniqueHandle + newIDSpaceRemoved + imgType + '" src="ImageParser.php?thumb=1&src=' + source +'"  /></div>');

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