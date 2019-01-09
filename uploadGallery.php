 <?php
$wordpressPath = htmlspecialchars($_POST['Wordpress_File']);
include $wordpressPath . '/wp-load.php';

add_action('add_image_hook', 'add_image_to_uploads');
do_action('add_image_hook');

function add_image_to_uploads(){
    $fileName = $_FILES['image_file']['name'];
    
    //Upload file if it does not exist in directory
    if(check_file($fileName)){ 
        $uploadedFile = file_get_contents($_FILES['image_file']['tmp_name']);
        $upload = wp_upload_bits($fileName, null, $uploadedFile); 
        var_dump($upload);
    }
}

//Check if file exists in uploads directory
function check_file($name){
    $uploadDir = wp_upload_dir();
    $allUploads = scandir($uploadDir['path']);
    if(in_array($name, $allUploads)){
        return false;
    }else{
        return true;
    }
}