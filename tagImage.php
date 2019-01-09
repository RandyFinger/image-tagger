<?php
$wordpressPath = \htmlspecialchars($_POST['Wordpress_File']);
include $wordpressPath . '/wp-load.php';

add_action('tag_image','tag_images');

do_action('tag_image');

function tag_images(){
    $dir = wp_upload_dir();
    $path = $dir['path'] . '/' . $_FILES['Image_File']['name'];
    $tagArray = \htmlspecialchars($_POST['Tags']);
    $tag_value = str_replace(",", ";", $tagArray);
    
function iptc_make_tag($rec, $data, $value)
{
    $length = strlen($value);
    $retval = chr(0x1C) . chr($rec) . chr($data);

    if($length < 0x8000)
    {
        $retval .= chr($length >> 8) .  chr($length & 0xFF);
    }
    else
    {
        $retval .= chr(0x80) . 
                   chr(0x04) . 
                   chr(($length >> 24) & 0xFF) . 
                   chr(($length >> 16) & 0xFF) . 
                   chr(($length >> 8) & 0xFF) . 
                   chr($length & 0xFF);
    }

    return $retval . $value;
}

// Set the IPTC tags
$iptc = array(
    '2#025' => $tag_value
);

// Convert the IPTC tags into binary code
$data = '';

foreach($iptc as $tag => $string)
{
    $tag = substr($tag, 2);
    $data .= iptc_make_tag(2, $tag, $string);
}

// Embed the IPTC data
$content = iptcembed($data, $path);

// Write the new image data out to the file.
$fp = fopen($path, "w");
fwrite($fp, $content);
fclose($fp);
}