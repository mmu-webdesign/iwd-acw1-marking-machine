<?php

include('config.php');

function formatBytes($bytes, $precision = 2) { 
    $units = array('b', 'kb', 'mb', 'gb', 'tb'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
     $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function getElementByClass(&$parentNode, $tagName, $className, $offset = 0) {
    $response = false;

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    $tagCount = 0;
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute('class'), $className) !== false) {
            if ($tagCount == $offset) {
                $response = $temp;
                break;
            }

            $tagCount++;
        }

    }

    return $response;
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<title>Marking student work</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    
<link rel="stylesheet" type="text/css" href="css/mark.css">
</head>
<body>
<main class="report">
    
<header>
    <h1>Student Work Marking System</h1>  
    </header> 


<?php

$id = $_GET['for'];
$name =  $_GET['name'];
    
$check_url = str_replace('[id]',$id, $check_url);

echo '<h2 class="report__person"><span class="report__id">'.$id.'</span><span class="report__name">'.$name.'</span></h2>';
    
echo '<p><a class="report__link" href="'.$check_url.'">Visit this website</a></p>';

    // colours are not right yet
    
echo '<div class="colors"><h3>Colours set in stylesheet (best to check as this does not catch all of them)</h3>';
    
    $css = file_get_contents($check_url.'style.css');
    
    $pattern = "/color:\s*(#[0-9a-f]+);/";
    
    preg_match_all($pattern, $css, $matches, PREG_PATTERN_ORDER);

  foreach($matches[1] as $m){
      
      echo '<div class="color-box" style="background-color:'.$m.'"></div>';
      
  }
    
    echo '</div>';
    
foreach($pages as $p){

echo '<section class="report__section">';
$current_check = $check_url.'/'.$p;
echo '<h3 class="report__header">Report for '.$p.'</h3>';

    
$curl=curl_init();
curl_setopt($curl, CURLOPT_URL, $current_check);
curl_setopt($curl, CURLOPT_FILETIME, true);
curl_setopt($curl, CURLOPT_NOBODY, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_VERBOSE, 1);

$response = curl_exec($curl);

// Then, after your curl_exec call:
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
    
    
if(strstr($header, '404 Not Found')){
 
    echo '<p class="report__error">'.$p.' not uploaded</p>';
}else{
    
    echo '<p class="report__ok"><a href="'.$current_check.'">'.$p.'</a> page available</p>';
    
    
    
$page = file_get_contents($current_check);
    
 

$dom = new DOMDocument;
@$dom->loadHTML($page);

echo '<div class="report__block"><h4 class="report__block-title">Metadata</h4>';
$i=1;
$meta = $dom->getElementsByTagName('meta');
foreach ($meta as $m) {
        echo '<div class="report__sub-block">';

    echo '<h6 class="report__counter">'.$i.'</h6>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">name</h5><span class="report__attribute-value">'.$m->getAttribute('name').'</span></div>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">content</h5><span class="report__attribute-value">'.$m->getAttribute('content').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">charset</h5><span class="report__attribute-value">'.$m->getAttribute('charset').'</span></div>';
    echo '</div>';
    $i++;
}

    echo '</div>';
    
echo '<div class="report__block"><h4 class="report__block-title">Images</h4>';
$i=1;
$img = $dom->getElementsByTagName('img');
foreach ($img as $im) {
    echo '<div class="report__sub-block"><h6 class="report__counter">'.$i.'</h6>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">src</h5><span class="report__attribute-value">'.$im->getAttribute('src').'</span></div>'; // add link to this

    echo '<div class="report__attribute"><h5 class="report__attribute-name">alt</h5><span class="report__attribute-value">'.$im->getAttribute('alt').'</span></div>';
    
    echo '<div class="report__attribute"><h5 class="report__attribute-name">width and height as set in HTML</h5><span class="report__attribute-value">';
    
    $image_width = $im->getAttribute('width');
    
    if(isset($image_width)&&$image_width!==""){
        
        echo $im->getAttribute('width');
        
    }else{echo '<b>not set</b>';}
    
    echo ' &#215; ';
       
     $image_height = $im->getAttribute('height');
    
    if(isset($image_height)&&$image_height!==""){
        
        echo $im->getAttribute('height');
        
    }else{echo '<b>not set</b>';}
       
       
     echo '</span></div>';
    
    $image_url = $check_url.$im->getAttribute('src');
    
    @$size = getimagesize($image_url);
 

    
   if(!isset($size[0])){
       
       echo '<div class="report__attribute"><h5 class="report__attribute-name">width and height of actual image file</h5><span class="report__attribute-value"><strong class="report__error">Image not found/not raster image</strong></div>';
       
   }else{
    
           
$headers  = get_headers($image_url, 1);

$this_filesize    = $headers['Content-Length'];
       
    
    echo '<div class="report__attribute"><h5 class="report__attribute-name">width and height of actual image</h5><span class="report__attribute-value">'.$size[0].' &#215; '.$size[1].' &middot; '.formatBytes($this_filesize).'</span>
    </div>';
       
       if(($size[0]==$im->getAttribute('width'))&&($size[1]==$im->getAttribute('height'))){
           
           echo '<span class="report__ok">OK</span>';
       }else{
           echo '<span class="report__error">Resized or not set</span>';
       }
    
        
   }
   
    
    echo '</div>';
    $i++;
}
echo '</div>';
    
       
echo '<div class="report__block"><h4 class="report__block-title">Hyperlinks</h4>';


$hy = $dom->getElementsByTagName('a');
    $i=1;
foreach ($hy as $h) {
        echo '<div class="report__sub-block">';

    echo '<h6 class="report__counter">'.$i.'</h6>';
   
    echo '<div class="report__attribute"><h5 class="report__attribute-name">href</h5><span class="report__attribute-value attr-small">'.$h->getAttribute('href').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">link text</h5><span class="report__attribute-value">'.$h->nodeValue.'</span></div>';
      echo '<div class="report__attribute"><h5 class="report__attribute-name">title text</h5><span class="report__attribute-value">'.$h->getAttribute('title').'</span></div>';
     echo '<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">'.$h->getAttribute('class').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">ID</h5><span class="report__attribute-value">'.$h->getAttribute('id').'</span></div>';
    echo '</div>';
    $i++;

}
echo '</div>';
    
echo '<div class="report__block"><h4 class="report__block-title">CSS</h4>';

$css = $dom->getElementsByTagName('link');
    $i=1;
foreach ($css as $c) {
        echo '<div class="report__sub-block"><h6 class="report__counter">'.$i.'</h6>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">src</h5><span class="report__attribute-value">'.$c->getAttribute('src').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">rel</h5><span class="report__attribute-value">'.$c->getAttribute('rel').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">href</h5><span class="report__attribute-value">'.$c->getAttribute('href').'</span></div>';
 echo '<div class="report__attribute"><h5 class="report__attribute-name">media</h5><span class="report__attribute-value">'.$c->getAttribute('media').'</span></div>';
    echo '</div>';
    $i++;

}
echo '</div>';
    

echo '<div class="report__block"><h4 class="report__block-title">&lt;div&gt; class, ID and role attributes</h4>';

$div = $dom->getElementsByTagName('div');
    $i=1;
foreach ($div as $d) {
    echo '<div class="report__sub-block">';
        echo '<h6 class="report__counter">'.$i.'</h6>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">'.$d->getAttribute('class').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">id</h5><span class="report__attribute-value">'.$d->getAttribute('id').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">role</h5><span class="report__attribute-value">'.$d->getAttribute('role').'</span></div>';
    echo '</div>';
    $i++;

}
echo '</div>';
    
echo '<div class="report__block"><h4 class="report__block-title">&lt;main&gt; class, ID and role attributes</h4>';


$main = $dom->getElementsByTagName('main');
    $i=1;
foreach ($main as $m) {
    echo '<div class="report__sub-block">';
        echo '<h6 class="report__counter">'.$i.'</h6>';

    echo '<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">'.$m->getAttribute('class').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">id</h5><span class="report__attribute-value">'.$m->getAttribute('id').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">role</h5><span class="report__attribute-value">'.$m->getAttribute('role').'</span></div>';
    echo '</div>';
$i++;
}

echo '</div>';
 
    
    if($p=="index.html"){
    echo '<div class="report__block"><h4 class="report__block-title">Class1 and Class2 usage</h4>';

    echo '<div class="report__sub-block">';
    


    $innerHTML = '';
$classname = 'class1';
$finder = new DomXPath($dom);
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$tmp_dom = new DOMDocument(); 
foreach ($nodes as $node) 
    {
    $tmp_dom->appendChild($tmp_dom->importNode($node,true));
    }
$innerHTML.=trim($tmp_dom->saveHTML()); 
    if($innerHTML!==""){
        
        $innerHTML = str_replace('<', '&lt;', $innerHTML);
        echo '<pre>'.$innerHTML.'</pre>';
    
    } else {echo '<p><strong>Class1</strong> not found</p>';}
    
        $innerHTML = '';
$classname = 'class2';
$finder = new DomXPath($dom);
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$tmp_dom = new DOMDocument(); 
foreach ($nodes as $node) 
    {
    $tmp_dom->appendChild($tmp_dom->importNode($node,true));
    }
$innerHTML.=trim($tmp_dom->saveHTML()); 
    
    if($innerHTML!==""){echo '<pre>'.$innerHTML.'</pre>'; } else {echo '<p><strong>Class2</strong> not found</p>';}
    
    
    echo '</div>';
    
    }
//style.css
    
?>

<!-- 
testing out structure and design
<div class="report__block">
<h4 class="report__block-title">Hyperlinks</h4>
<h6 class="report__counter">1</h6>
<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">this-is-a-stupidly-long-class-name</span></div>
<div class="report__attribute"><h5 class="report__attribute-name">link text</h5><span class="report__attribute-value">click here to do another thing you might want to do</span></div>
<div class="report__attribute"><h5 class="report__attribute-name">href</h5><span class="report__attribute-value">/path/to/a/resource/goes/here</span></div>

<h6 class="report__counter">2</h6>
<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">this-is-a-stupidly-long-class-name</span></div>
<div class="report__attribute"><h5 class="report__attribute-name">link text</h5><span class="report__attribute-value">click here to do another thing you might want to do</span></div>
<div class="report__attribute"><h5 class="report__attribute-name">href</h5><span class="report__attribute-value">/path/to/a/resource/goes/here</span></div>

</div>
-->


<?php





// let's validate it
// curl request
// create curl resource 
$validator_url = 'https://validator.w3.org/nu/?doc='.$current_check.'&out=json';
$ch = curl_init(); 
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

// set url 
curl_setopt($ch, CURLOPT_URL, $validator_url); 

//return the transfer as a string 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'MMU HTML validator'); 

// $output contains the output string 
$output = curl_exec($ch); 

//var_dump(json_decode($output));
$vr='';
$vr = json_decode($output, true);
    
if(count($vr['messages'])>0){
    
    echo '<p class="report__error">'.$p.' fails validation &middot; '.count($vr['messages']).' error(s)</p>';
    
        echo '<ul class="report__validator">';

    foreach($vr['messages'] as $m){
    
 echo '<li class="report__error-item"><span class="report__error-type-'.$m['type'].'">'.$m['type'].'</span> '.$m['message'].'</li>';

}
    echo '</ul>';

}else{
    echo '<p class="report__ok">'.$p.' page validates</p>';

}
    

    
    //type
    //message
    

// close curl resource to free up system resources 
curl_close($ch);      

    // primatise this? at least make it openable
echo '<div class="report__raw js-show-hide">';
echo '<textarea rows="50" cols="200">';
echo $page;
echo '</textarea>';
echo '</div>';
    
        
    echo '</section>';
}
}
    
    
?>
<p class="report__end"><a class="report__link" href="index.php">Go back</a></p>
<footer>&copy; <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></footer>
</main>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('.js-show-hide').hide();
    $('.js-show-hide').before('<p><a href="#" class="report__link js-show-report">show full source</a></p>');
    $('.js-show-report').on('click', function(){
       
        $(this).parent().next('.js-show-hide').slideToggle('300');
         $(this).remove();
        return false;
        
    });
    
});
    
</script>

</body>
</html>