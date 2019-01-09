<?php
/*
Plugin Name: Image-Tagger
Description: A plugin that tags multiple images
Version: 0.0.1
*/
add_action('admin_menu', 'plugin_menu');

function wptp_add_tags_to_attachments(){
	register_taxonomy_for_object_type('post_tag', 'attachment');
}
add_action('init','wptp_add_tags_to_attachments');

function plugin_menu(){
	add_options_page('Options', 'Image-Tagger', 'manage_options', 'ITagger', 
		'plugin_options');
}

function plugin_options(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap">
	<div><h1><b>Image-Tagger</b></h1></div>
	<div><form id="tag_form" method="post" enctype="multipart/form-data"><table id="tag_table">
	<th>Tags</th>
	<th id="gallery_header">Galleries</th>
	<tr id="tagsRow"><td id="tags_section"><button type="button" id="add_tag_button" name="add_tag" onclick="addTag()">Add Tag</button><br>
	<ul id="tagList"></ul></td>
	<td id="galleries_section"><button type="button" id="add_gallery_button" onclick="addGallery()">
	Add Gallery</button>
	<ul id="galleryList"></ul></td>
	</tr>
	</table>
	</div>
	<div><table id="image_table" style="width:100%"><th id="images_header">Gallery Images<button type="button">Filter</button></th>
                <tr id="images_row"><td><button type="button" name="tagButton" id="tag_button" onclick="tagImage()">Tag Images</button><br><br>
	<div id="imageList"></div></td>
	</tr>
	</table></form></div>
	<?php
}

function table_styles(){
	?><style type="text/css">
	
	#tag_table{
	width: 100%;
	border: 1px solid black;
	text-align: left;
	}

	#saveTagButton{
	float: right;
	}

	#image_table{
	width: 100%;
	border: 1px solid black;
	text-align: left;
	height: 400px;
	}

	#images_header{
	text-align: center;
	}

	th{
	text-align: left;
	}

	#tags_section{
	width: 25%;
	border: 1px solid black;
	padding: 7px 7px 7px 7px;
	}

	#images_row{
	height: 350px;
	vertical-align: top;
	}

	#tagsRow{
	height: 300px;
	vertical-align: top;
	}

	#gallery_header{
	text-align: center;	
	}

	#galleries_section{
	border: 1px solid black;
	padding: 7px 7px 7px 7px;
	}

	button{
	float:right;
	}
        
        .imageContainers{
        display: inline-block;
        width: 200px;
        height: 200px;
        margin-left: 10px;
        padding: 5px 5px 5px 5px;
        border: 1px solid black;
        }
        
        .imageCB{
        position: relative;
        left: 10px;
        bottom: 12px;
        }

        .labels{
        position: relative;
        left: 10px;
        bottom: 14px;
        }
        
	</style>
	<?php
}

add_action('admin_head','table_styles');

function add_content(){
	?>
	<script type="text/javascript">
	var gallery;
        var galleryCount = 1;
	var galleryCheck;
	var galleryTitle;
	var upload;
	var linebreak;
	var tagName;
	var tagCheckbox;
	var tags;
	var images;
	var imageFiles;
	var displayedImage;
	var galChecks;
	var uploads;
	var delImages;
	var imageCheckbox;
	var imageChecks;
	var tagChecks;
	var tagNames;
        var formData;
        var allChildElements;
        var name;
        var nameNum;
        var newName;
        var id;
        var cbLabel;
        var fd;
        var tagsArray;

	function addTag(){
	tags = document.getElementById("tagList");
	tagCheckbox = document.createElement("input");
	tagCheckbox.type = "checkbox";
	tagCheckbox.name = "tag_checks";
	tagName = document.createElement("input");
	tagName.name = "tag_name";
	linebreak = document.createElement("br");
	tags.appendChild(tagCheckbox);
	tags.appendChild(tagName);
	tags.appendChild(linebreak);
	return false;
	}

	function addGallery(){
	gallery = document.getElementById("galleryList");
	galleryCheck = document.createElement("input");
	galleryCheck.type = "checkbox";
	galleryCheck.name = "gallery_checks";
	galleryTitle = document.createElement("input");
	galleryTitle.value = "Gallery " + galleryCount.toString();
        galleryCount++;
	galleryTitle.name = "Gallery_Title";
	upload = document.createElement("input");
	upload.type = "file";
	upload.name = "uploads";
	linebreak = document.createElement("br");

	galleryCheck.onclick = function(){
		try{
			galChecks = document.getElementsByName("gallery_checks");
			uploads = document.getElementsByName("uploads");
			images = document.getElementById("imageList");
			images.innerHTML = "";

			for(a = 0;a < galChecks.length;a++){
			if(galChecks[a].checked){
				for(i = 0;i < uploads[a].files.length;i++){
                                        imageContainer = document.createElement("div");
                                        imageContainer.className = "imageContainers";
					displayedImage = new Image(175,175);
					displayedImage.name = "gallery " + a.toString();
					displayedImage.className = "galleryImages";
					displayedImage.style.padding = "10px 10px 10px 10px";
					displayedImage.src = window.URL.createObjectURL(uploads[a].files[i]);
					imageCheckbox = document.createElement("input");
					imageCheckbox.type = "checkbox";
					imageCheckbox.name = "image_checkbox";
					imageCheckbox.checked = true;
					imageCheckbox.className = "imageCB";
					imageContainer.appendChild(displayedImage);
                                        imageContainer.appendChild(imageCheckbox);
                                        cbLabel = document.createElement("label");
                                        cbLabel.className = "labels";
                                        cbLabel.innerHTML = uploads[a].files[i].name;
                                        imageContainer.appendChild(cbLabel);
                                        images.appendChild(imageContainer);
                                        displayedImage.id = i.toString();
                                        formData = new FormData();
                                        formData.append('Wordpress_File',<?php echo json_encode(get_home_path());?>);
                                        formData.append('image_file', uploads[a].files[i], uploads[a].files[i].name);
                                        var xmlhttp = new XMLHttpRequest();
                                        xmlhttp.open("POST", "/wp-content/plugins/image-tagger/uploadGallery.php", true);
                                        xmlhttp.send(formData);
					displayedImage.onload = function(){
						window.URL.revokeObjectURL(this.src);
				};
			}

		}else if(!galChecks[a].checked){
			delImages = document.getElementsByName("gallery " + a.toString());
			for(b = 0;b < delImages.length;b++){
                            images.removeChild(delImages[b]);
			}
		}
	}
		}catch(err){
			alert('No images to upload!');
		}
	};

	upload.multiple = "true";
	gallery.appendChild(galleryCheck);
	gallery.appendChild(galleryTitle);
	gallery.appendChild(upload);
	gallery.appendChild(linebreak);
	}

	function tagImage(){
		imageChecks = document.getElementsByName("image_checkbox");
		tagChecks = document.getElementsByName("tag_checks");
		tagNames = document.getElementsByName("tag_name");
                allChildElements = document.getElementsByClassName("imageContainers");
                tagsArray = [];
                
                fd = new FormData();
                fd.append('Wordpress_File',<?php echo json_encode(get_home_path());?>);
                
                for(var divElem = 0;divElem < allChildElements.length;divElem++){
		for (var index = 0;index < allChildElements[divElem].children.length;index++){
                    if(allChildElements[divElem].children[index].tagName.toLowerCase() === "img"){
                        name = allChildElements[divElem].children[index].name;
                        nameNum = name.split(" ");
                        newName = nameNum[1];
                        id = allChildElements[divElem].children[index].id;
                    }
			if(imageChecks[divElem].checked){ 
                            fd.append('Image_File',uploads[newName].files[id],uploads[newName].files[id].name); 
			for(var y = 0;y < tagChecks.length;y++){
                            if(tagChecks[y].checked){
                                tagsArray.push(tagNames[y].value);
                            }
			}
                        fd.append('Tags',tagsArray);
                    }
		}
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "/wp-content/plugins/image-tagger/tagImage.php", true);
                xhttp.send(fd);
            }
            }
	</script>
	<?php
}

add_action('admin_head','add_content');