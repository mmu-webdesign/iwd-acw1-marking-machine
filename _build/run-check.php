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

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

$id = $_GET['for'];
$name =  $_GET['name'];
$valid_message = array();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title><?php echo $name; ?> &middot; Marking student work</title>
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/mark.css"> </head>

    <body>
        <main class="report">
            <header>
                <h1>Student Work Marking System</h1> </header>
            <?php



$check_url = str_replace('[id]',$id, $check_url);

echo '<h2 class="report__person"><span class="report__id">'.$id.'</span><span class="report__name">'.$name.'</span></h2>';

echo '<p><a class="report__link" href="'.$check_url.'">Visit this website</a></p>';


// get the HTML of the pages we need

$cp = array();

foreach($pages as $p){
$cp[$p] = file_get_contents($check_url.'/'.$p);
$dom[$p] = new DOMDocument;
@$dom[$p]->loadHTML($cp[$p]);  
}

// begin producing results

// TITLE TAG AND METADATA
    
echo '<div class="report__section"><h3 class="report__header">Title tags and metadata</h3>';

    
foreach($pages as $p){
    
    $title = $dom[$p]->getElementsByTagName('title');
    
    $meta[$p] = $dom[$p]->getElementsByTagName('meta');
foreach ($meta[$p] as $m) {
    
    if($m->getAttribute('name')=="description"){
        
        $meta_content = $m->getAttribute('content');
    }
}
    ?>
                <table class="data__table">
                    <tr>
                        <td rowspan="2" class="data__area">
                            <h4 class="data__page"><?php echo $p; ?></h4></td>
                        <td class="data__subhead">Page Title</td>
                        <td class="data__result">
                            <?php echo $title->item(0)->textContent; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="data__subhead">Meta description</td>
                        <td class="data__result monospace">
                            <?php echo $meta_content; ?>
                        </td>
                    </tr>
                </table>
                <?php

        
}

    echo '</div>';
    


    echo '</div>';
    
// CHECK HOMEPAGE MARKUP
    
echo '<div class="report__section"><h3 class="report__header">Semantic markup of homepage copy</h3>';

echo '<div class="report__raw js-show-hide js-html">';
echo '<textarea rows="50" cols="200">';
echo $cp['index.html'];
echo '</textarea>';
echo '</div>';
    
echo '</div>';
  
    
// HYPERLINKS
            
            
       
echo '<div class="report__section hyperlinks"><h3 class="report__header">Hyperlinks</h3>';
$remove_links = array("axa", "ncc","validator");
            
foreach($pages as $p){
    echo '<h4>'.$p.'</h4>';
$hy = $dom[$p]->getElementsByTagName('a');
    $i=1;
    
foreach ($hy as $h) {
    
//    $echo = 0;
//    
//    if($p!=="index.html"&&strstr($h->getAttribute('href'),"mmu.ac.uk")){$echo = 1;}
//    if($p=="index.html"){$echo =1 ;}
//       if($echo==1){
    
    if(!strposa($h->getAttribute('href'),$remove_links)){
        
        if(!strstr($h->nodeValue, "MMU 201")){
        echo '<div class="report__sub-block">';

    echo '<h6 class="report__counter">'.$i.'</h6>';
   
    echo '<div class="report__attribute"><h5 class="report__attribute-name">href</h5><span class="report__attribute-value attr-small">'.$h->getAttribute('href').'</span></div>';
    echo '<div class="report__attribute"><h5 class="report__attribute-name">link text</h5><span class="report__attribute-value">'.$h->nodeValue.'</span></div>';
      echo '<div class="report__attribute"><h5 class="report__attribute-name">title text</h5><span class="report__attribute-value">'.$h->getAttribute('title').'</span></div>';
//     echo '<div class="report__attribute"><h5 class="report__attribute-name">class</h5><span class="report__attribute-value">'.$h->getAttribute('class').'</span></div>';
//    echo '<div class="report__attribute"><h5 class="report__attribute-name">ID</h5><span class="report__attribute-value">'.$h->getAttribute('id').'</span></div>';
    echo '</div>';
            }
    $i++;
        }
        //   }

}

    
}
  echo '</div>';
            
echo '</div>';
            
            
            
            
    // CHECK IMAGE

   
echo '<div class="report__section"><h3 class="report__header">Image usage</h3>';

    
foreach($pages as $p){
    $valid_message[$p] = ' <span class="error">but logo has not been added</span>';
    ?>
                    <?php
$img = $dom[$p]->getElementsByTagName('img');
    $rowspan = count($img);
     $i = 1;
    
    ?>
                        <table class="data__table">
                            <tr>
                                <td rowspan="<?php echo $rowspan; ?>" class="data__area">
                                    <h4 class="data__page"><?php echo $p; ?></h4></td>
                                <td>
                        <?php
    
    
    
foreach ($img as $im) {
    echo '<div class="report__sub-block"><h6 class="report__counter">'.$i.'</h6>';

     if($im->getAttribute('src')=="images/logo.jpg"){
        
        $valid_message[$p] = " and logo has been added";
    }
    
       if($im->getAttribute('src')=="images/student.jpg"||$im->getAttribute('src')=="images/students.jpg"){
        
        
            echo '<div class="report__attribute"><h5 class="report__attribute-name">src</h5><span class="report__attribute-value"><a href="'.$check_url.$im->getAttribute('src').'">'.$im->getAttribute('src').'</a></span></div>';
           
    }else{
    
    echo '<div class="report__attribute"><h5 class="report__attribute-name">src</h5><span class="report__attribute-value">'.$im->getAttribute('src').'</span></div>'; // add link to this

   }
    
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
    echo '</td></tr>';

     
 }
    echo '</table>';
echo '</div>';    
    
// CHECK STYLESHEET

echo '<div class="report__section"><h3 class="report__header">Attach external stylesheet</h3>';
?>
                            <table class="data__table">
                                <?php 
 foreach($pages as $p){
     
    $css = $dom[$p]->getElementsByTagName('link');
     $css_output='No';
     if ($css[0]->getAttribute('href')=="style.css"){ $css_output = 'Yes'; }

         ?>
                                    <tr>
                                        <td class="data__area">
                                            <h4 class="data__page"><?php echo $p; ?></h4></td>
                                        <td class="data__subhead">Attached?</td>
                                        <td class="data__result">
                                            <?php echo $css_output; ?>
                                        </td>
                                    </tr>
                                    <?php
    
     
 }
    ?>
                            </table>
                            <?php

    echo '</div>';
    
// VALIDATION
    
echo '<div class="report__section"><h3 class="report__header">Page validation</h3>';

foreach ($pages as $p){
        
        $current_check = $check_url.'/'.$p;
            
    
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

    /* $travers_image = ": image has not been added";
    if($p=="travers.html")&&$travers_found ==1){ $travers_image = ': image has been added';}
*/
    
if(count($vr['messages'])>0){
    
    echo '<p class="report__error">'.$p.' fails validation &middot; '.count($vr['messages']).' error(s)</p>';
    
        echo '<ul class="report__validator">';

    foreach($vr['messages'] as $m){
    
 echo '<li class="report__error-item"><span class="report__error-type-'.$m['type'].'">'.$m['type'].'</span> '.$m['message'].'</li>';

}
    echo '</ul>';

}else{
    echo '<p class="report__ok">'.$p.' page validates '.$valid_message[$p].'</p>';

}
    

    
    //type
    //message
    

// close curl resource to free up system resources 
curl_close($ch);      
    
    }
?>

<?php

echo '<div class="report__section"><h3 class="report__header">CSS editing</h3>';

$css = file_get_contents($check_url.'style.css');
   
echo '<div class="report__raw js-show-hide js-css">';
echo '<textarea rows="50" cols="200">';
echo $css;
echo '</textarea>';
echo '</div>';

echo '</div>';

            echo '<div class="report__section"><h3 class="report__header">Class1 and Class2 usage</h3>';


// IS CLASS1 and 2 in index.html?
    
    

echo '<div class="simple"><h4 class="simple__header">Class 1 context</h4>';
$innerHTML = '';
$classname = 'class1';
$finder = new DomXPath($dom['index.html']);
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$tmp_dom = new DOMDocument(); 
foreach ($nodes as $node) 
    {
    $tmp_dom->appendChild($tmp_dom->importNode($node,true));
    }
$innerHTML.=trim($tmp_dom->saveHTML()); 
    if($innerHTML!==""){
        
        $innerHTML = str_replace('<', '&lt;', $innerHTML);
        echo '<div class="report__raw--inline"><textarea>'.$innerHTML.'</textarea></div>';
    
    } else {echo '<p class="simple__content"><span class="report__error--inline">Not found</span>. Check the HTML to be sure.</p>';}
    
            echo '</div>';
            
            echo '<div class="simple"><h4 class="simple__header">Class 2 context</h4>';

            
        $innerHTML = '';
$classname = 'class2';
$finder = new DomXPath($dom['index.html']);
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$tmp_dom = new DOMDocument(); 
foreach ($nodes as $node) 
    {
    $tmp_dom->appendChild($tmp_dom->importNode($node,true));
    }
$innerHTML.=trim($tmp_dom->saveHTML()); 
    
    if($innerHTML!==""){echo '<div class="report__raw--inline"><textarea>'.$innerHTML.'</textarea></div>'; } else {echo '<p class="simple__content"><span class="report__error--inline">Not found</span>. Check the HTML to be sure.</p>';}
    
    
echo '</div>';
    
    
/* 
// colours are not right yet

echo '<h4>Attempting to find colours set in stylesheet</h4>';

$pattern = "/color:\s*(#[0-9a-f]+);/";

preg_match_all($pattern, $css, $matches, PREG_PATTERN_ORDER);

foreach($matches[1] as $m){
  
echo '<div class="color-box" style="background-color:'.$m.'"></div>';
  
}

echo '</div>';

*/

echo '</div>';

?>
                                    <p class="report__end"><a class="report__link" href="index.php">Go back</a></p>
                                    <footer>&copy;
                                        <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></footer>
        </main>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>
//            $(document).ready(function () {
//                $('.js-show-hide').hide();
//                $('.js-show-hide.js-css').before('<p><a href="#" class="report__link js-show-report">show full CSS file</a></p>');
//                $('.js-show-hide.js-html').before('<p><a href="#" class="report__link js-show-report">show full HTML source</a></p>');
//                $('.js-show-report').on('click', function () {
//                    $(this).parent().next('.js-show-hide').slideToggle('300');
//                    $(this).remove();
//                    return false;
//                });
//            });
        </script>
    </body>

    </html>