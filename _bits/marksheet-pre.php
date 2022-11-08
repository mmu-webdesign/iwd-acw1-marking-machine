<?php
error_reporting(E_ALL); ini_set('display_errors', 'on');


$student_number = $_GET['for'];
$student_name = $_GET['name'];

include('config.php');
include('includes/colour-contrast.php');
include('includes/colours.php');

require_once('includes/vendor/autoload.php'); 



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


function createtask($letter, $description){
    echo '<tr class="task">
    <td contenteditable="true"><span class="marker">'.$letter.'</span> '.$description.'</td>
    <td></td>
    <td></td>
</tr>';
}

function reporttask_automatic($taskname, $score, $possible){
    echo '<tr class="result">
    <td contenteditable="true">'.$taskname.'</td>
    <td contenteditable="true"><strong><span class="add-to-mark">'.$score.'</span></strong></td>
    <td>'.$possible.'</td>
</tr>';
}

function reporttask_manual($taskname, $range, $readout="", $presentation="just-text"){

    if($readout!==""){
        if($presentation == 'just-text'){echo '<tr class="noprint">
            <td colspan="3">'.$readout.'</td>
        </tr>';}else{echo '<tr class="noprint">
            <td colspan="3"><pre><code class="language-markup">'.str_replace('<', '&lt;', str_replace('&', '&amp;', $readout)).'</code></pre></td>
        </tr>';}
    


}

echo '
<tr class="result">
    <td contenteditable="true">'.$taskname.'</td>
    <td><input type="number" class="get-result" min="0" max="'.$range.'" /></td>
    <td>'.$range.'</td>
</tr>';
}

function introduce_task($content)
{echo '
    <tr class="result">
        <td contenteditable="true">'.$content.'</td>
        <td></td>
        <td></td>
    </tr>';}

    $cp = array();
    
    $check_url = str_replace('[id]',$student_number, $check_url);

    foreach($pages as $p){
    $cp[$p] = @file_get_contents($check_url.$p);
    $dom[$p] = new DOMDocument;
    @$dom[$p]->loadHTML($cp[$p]);  
    }


    // $cp is the array of files
// $dom is the same page as dom elements



function validate_page($url){
    $validator_url = 'https://validator.w3.org/nu/?doc='.$url.'&out=json';
    $ch = curl_init(); 
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    
    // set url 
    curl_setopt($ch, CURLOPT_URL, $validator_url); 
    
    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MMU HTML validator'); 
    
    // $output contains the output string 
    $output = curl_exec($ch); 

    $vr='';
    $vr = json_decode($output, true);
    $v_score = 0;

    if(isset($vr['messages'][0]['type'])&&$vr['messages'][0]['type']=="non-document-error"){
        $v_score = "validator unavailable";
        return $v_score;
}else{
    if(count($vr['messages'])==0){
        $v_score = 5;
        return $v_score;
        
    }else{
        return $v_score;
        
    }
}
}


function validate_css($url){
    $validator_url = 'https://jigsaw.w3.org/css-validator/validator?uri='.$url.'&warning=0&amp;profile=css3&amp;output=json';


    $ch = curl_init(); 
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    
    // set url 
    curl_setopt($ch, CURLOPT_URL, $validator_url); 
    
    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MMU CSS validator'); 
    
    // $output contains the output string 
    $output = curl_exec($ch); 
   //var_dump(json_decode($output));
    $vr='';
    $vr = json_decode($output, true);


    $v_score = 0;
// $vr will only be equal to NULL if the validator's not available/offline

  if($vr!==NULL){

    if($vr['cssvalidation']['result']['errorcount']==0){
        $v_score = 5;
        return $v_score;
    }else{
        return $v_score;

    }
}else{
    $v_score = "validator unavailable";
    return $v_score;
}
}

function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}


function getTextBetweenTags($string, $tagname)
 {
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);
    return @$matches[1];
 }



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $student_number; ?></title>
    <link rel="stylesheet" href="css/prism.css">
<script src="js/prism.js"></script>
<link rel="stylesheet" type="text/css" href="css/mark.css">


</head>

<body>

    <h1>Manchester Metropolitan University<br />Department of Languages, Information and Communications</h1>
    <h2>
        <span class="course"><?php echo $unit_name; ?></span>Assignment Two Feedback</h2>

    <ul class="information">
        <li>Date <strong><?php echo date('jS F Y'); ?></strong></li>
        <li>Name <strong><?php echo $student_name; ?></strong></li>
        <li>Student number <strong><?php echo $student_number; ?></strong></li>
    </ul>

<?php 
    $location = str_replace('/[id]/','',$check_url);
    $check = array(
          'index.html',
          'pages/stocklist.html',
          'pages/contact.html',
          'img/cheese-stacks.jpg',
          'css/style.css'
      );
     
     $check_url =  'http://www.ico.mmu.ac.uk/staff/55060509/mm-inyear/'.$student_number.'/';
     $to_check_url =  str_replace('[id]/','',$check_url.'/');
     $output = '';

     $exists = 0;
     foreach($check as $c){


    
       $test = is_url_exist($to_check_url.$c);
       if($test==1){
$output .='<a href="'.$to_check_url.$c.'">'.$c.'</a> &#10004; '; 
$exists = $exists+1;
       }else{
        $output .='<a href="'.$to_check_url.$c.'">'.$c.'</a> &#215; '; 
       }

     }


      echo '<div class="noprint"><p class="content result-grade-'.$exists.'">'.$output.'</p></div>';

      ?>
    <table>
        <tr>
            <th>Task</th>
            <th>Mark</th>
            <th>Max</th>
        </tr>

      
       

<?php createtask('A','Three HTML5 files'); ?>

<?php 
//reporttask_automatic($taskname, $score, $possible)

$find_file = 0;

if(isset($cp['index.html'])&&$cp['index.html']!==""){$find_file = 1;}

reporttask_automatic('Correct filename for index.html',$find_file,1); 
$find_file = 0;
?>


<?php createtask('B','Semantic mark-up – applied to supplied content for index.html'); ?>
        
<?php

$the_main_html = '';
//$main_html = $dom['index.html']->getElementsByTagName('main');


$the_main_html = getTextBetweenTags($cp['index.html'],'main');
// foreach ($main_html as $main) {
//     $the_main_html.= $main->ownerDocument->saveHTML($main);
// }
$the_main_html = str_replace('  ', '', $the_main_html);
$the_main_html = str_replace('  ', '', $the_main_html);

$the_main_html = str_replace(PHP_EOL, '', $the_main_html);
$the_main_html = str_replace('</ul>', '</ul>'."\n", $the_main_html);
$the_main_html = str_replace('</h2>', '</h2>'."\n", $the_main_html);
$the_main_html = str_replace('</p>', '</p>'."\n", $the_main_html);
$the_main_html = str_replace('</ol>', '</ol>'."\n", $the_main_html);

reporttask_manual('Headings, sub-headings and paragraphs marked up correctly',6,$the_main_html,'html-box'); 
$the_main_html = '';
?>

<?php
//reporttask_manual($taskname, $range, $readout)
reporttask_manual('Un-ordered-list (as defined)',3); 
?>


<?php
//reporttask_manual($taskname, $range, $readout)
reporttask_manual('Ordered list (as defined)',3); 
?>




<?php 


createtask('C','Place logo applied to header on all pages (with working link)'); 


$logo_alt = array();

introduce_task('Inserted image including height and width size attributes');

$main_html = new DOMXPath($dom['index.html']);
$get_tag = $main_html->query('//header //img');
$score = 0;
foreach ($get_tag as $tag) {
    array_push($logo_alt, trim($tag->getAttribute('alt')));
    if(trim($tag->getAttribute('src'))=="img/logo.gif" && 
    trim($tag->getAttribute('width'))=='220' &&
    trim($tag->getAttribute('height'))=='84')
 {$score = 2;}   

}

reporttask_automatic('index.html',$score,2);
$score = 0;
$hyperlink_score = 0;
$main_html = new DOMXPath($dom['pages/stocklist.html']);
$get_tag = $main_html->query('//header //a');
$score = 0;

// var_dump($get_tag);
// echo $get_tag->length;
// if($get_tag->length);


foreach ($get_tag as $tag) {
    
    
    if(trim($tag->getAttribute('href'))=="../index.html")
 {$score = 1; $hyperlink_score++;}   

}

$get_tag = $main_html->query('//header //a //img');

foreach ($get_tag as $tag) {
    array_push($logo_alt, trim($tag->getAttribute('alt')));
        if(trim($tag->getAttribute('src'))=="../img/logo.gif" && 
    trim($tag->getAttribute('width'))=='220' &&
    trim($tag->getAttribute('height'))=='84')
 {$score = $score +1;}    

}

reporttask_automatic('stocklist.html',$score,2);
$score = 0;


$main_html = new DOMXPath($dom['pages/contact.html']);
$get_tag = $main_html->query('//header //a');
$score = 0;
foreach ($get_tag as $tag) {
    if(trim($tag->getAttribute('href'))=="../index.html")
 {$score = 1;
$hyperlink_score++;}   

}

$get_tag = $main_html->query('//header //a //img');

foreach ($get_tag as $tag) {
    array_push($logo_alt, trim($tag->getAttribute('alt')));
    if(trim($tag->getAttribute('src'))=="../img/logo.gif" && 
    trim($tag->getAttribute('width'))=='220' &&
    trim($tag->getAttribute('height'))=='84')
 {$score = $score +1;}    

}

reporttask_automatic('contact.html',$score,2);

reporttask_manual('Appropriate ALT description',2,implode(', ',$logo_alt)); 

reporttask_automatic('Working hyperlink as required on stocklist.html and contact.html',$hyperlink_score,2);

$hyperlink_score = 0;
?>


<?php createtask('D','Create active external link in content as instructed'); 


// find the href of the hyperlink inside main

$main_html = new DOMXPath($dom['index.html']);
$get_tag = $main_html->query('//main //a');
$link_check="";
$link_text="";
foreach ($get_tag as $tag) {
    $link_text = $tag->nodeValue;
    $link_check = trim($tag->getAttribute('href'));
}

$score =0;

if($link_check=='http://cheese.com'){$score = 2;}
if($link_check=='http://cheese.com/'){$score = 2;}
if($link_check=='http://www.cheese.com'){$score = 2;}
if($link_check=='http://www.cheese.com/'){$score = 2;}
if($link_check=='https://www.cheese.com'){$score = 2;} 
if($link_check=='https://www.cheese.com/'){$score = 2;}
if($link_check=='https://cheese.com'){$score = 2;} 
if($link_check=='https://cheese.com/'){$score = 2;} 


reporttask_automatic('Working hyperlink to cheese.com',$score,2);

$score =0;

reporttask_manual('Relevant link text, following good practice',2,$link_text); 
?>



<?php 

createtask('E','
Picture – cropping, optimizing & export/save for web as JPEG'); 


$main_html = new DOMXPath($dom['index.html']);
$get_tag = $main_html->query('//main //img');
$img_src='';
$img_width='';
$img_height='';
$img_alt='';
foreach ($get_tag as $tag) {
    $img_src = trim($tag->getAttribute('src'));
    $img_width = trim($tag->getAttribute('width'));
    $img_height = trim($tag->getAttribute('height'));
    $img_alt = trim($tag->getAttribute('alt'));
}


reporttask_manual('Image DSC_2679.jpg cropped (approximately as illustrated)',2,'<div class="img__measure"></div><img src="'.$check_url.$img_src.'"/>'); 
$image_details = @getimagesize($check_url.$img_src);
$image_exif = @exif_imagetype($check_url.$img_src); // 2 for jpg
$head = array_change_key_case(get_headers($check_url.$img_src, TRUE));
$filesize = $head['content-length'];


$score = 0;
if($image_details[0]=='600'){$score = 2;}

reporttask_automatic('Image re-sized (width 600px)',$score,2);

$score = 0;
if($filesize<$jpg_file_size){$score = 2;}

reporttask_automatic('Image optimised while keeping image quality high (file size '.formatBytes($jpg_file_size).' or below): actual size '.formatBytes($filesize),$score,2);


$score = 0;
if(($img_src == 'img/cheese-stacks.jpg')&&($image_exif==2)){$score = 2;}
reporttask_automatic('Exported in jpeg file format and saved as cheese-stacks.jpg',$score,2);
$score = 0;


$score = 0;
if($img_width=='600'&&(isset($img_height)&&$img_height!=='')){$score = 2;}
reporttask_automatic('Inserted image including height and width size attributes',$score,2);
$score = 0; 

reporttask_manual('Appropriate ALT description',2,$img_alt); 


?>

<?php 


createtask('F','Navigation system on all pages (unordered list)'); 

introduce_task('Working hyperlinks for navigation between homepage and sub-pages');

$link_show = array();
$i=0;
foreach($dom as $d){
$main_html = new DOMXPath($d);
$get_tag = $main_html->query('//nav //ul //li //a');
foreach ($get_tag as $tag) {
    $link_show[$i][trim($tag->nodeValue)] = trim($tag->getAttribute('href'));
}
$i++;
}


// $main_html = new DOMXPath($dom['index.html']);
// $get_tag = $main_html->query('//link');
// foreach ($get_tag as $tag) {
//     var_dump(trim($tag->getAttribute('href')));
// }


reporttask_manual('index.html',2,@implode(' &middot; ',$link_show[0]));
reporttask_manual('stocklist.html',2,@implode(' &middot; ',$link_show[1]));
reporttask_manual('contact.html',2,@implode(' &middot; ',$link_show[2]));


?>


<?php 

// mark the table
// TODO: none?

createtask('G','Table (stocklist.html)'); 

$the_table_html = '';
// $table_html = $dom['pages/stocklist.html']->getElementsByTagName('table');
// foreach ($table_html as $table) {
//     $the_table_html.= $table->ownerDocument->saveHTML($table);
// }
$the_table_html = '<table>'.getTextBetweenTags($cp['pages/stocklist.html'],'table').'</table>';

reporttask_manual('Insert stock table in stocklist.html replicating stocklist.xlsx
',10,$the_table_html, 'html_output');
$the_table_html = '';

introduce_task('<span style="color:#777;">Includes the table, table header, table row and table column elements</span>');
introduce_task('Add Special characters');

$score = 0;
if(strstr($cp['pages/stocklist.html'], '&amp;')){$score = 2;}
reporttask_automatic('Ampersand',$score,2);
$score = 0;
if(strstr($cp['pages/stocklist.html'], '&pound;')){$score = 2;}
reporttask_automatic('UK pound',$score,2);
$score=0;


?>

</table>
<p class="pagebreak"></p>
<table>

<?php 

// find the stylesheets
// TODO:none?

createtask('H','
Attach external style sheet'); 

introduce_task('<span style="color:#777;">Create style.css using template.css</span>');

introduce_task('Style.css attached correctly on all pages');

$check_css_link = array();
foreach($pages as $p){
    $link_check = $dom[$p]->getElementsByTagName('link');
    foreach($link_check as $l){
        $check_css_link[$p]=$l->getAttribute('href');
    
}
}

//print_r($check_css_link);
$score=0;
if(@$check_css_link['index.html']=='css/style.css'){$score=1;}
reporttask_automatic('index.html',$score,1);
$score=0;
if(@$check_css_link['pages/stocklist.html']=='../css/style.css'){$score=1;}
reporttask_automatic('stocklist.html',$score,1);
$score=0;
if(@$check_css_link['pages/contact.html']=='../css/style.css'){$score=1;}
reporttask_automatic('contact.html',$score,1);
$score = 0;
?>


<?php 

// validate pages
// TODO: check this works, add check for inner pages

createtask('I','HTML Validation'); 
$score = validate_page($check_url.'index.html');
reporttask_automatic('index.html validates',$score,5);
$score = 0;
$extratext = '';
if(
    strstr($cp['pages/stocklist.html'],'logo.gif')&&
    strstr($cp['pages/stocklist.html'],'<ul>')&&
    strstr($cp['pages/stocklist.html'],'style.css')&&
    strstr($cp['pages/stocklist.html'],'<table>'))
    {
$score = validate_page($check_url.'pages/stocklist.html');
}else{$extratext ='<strong>&#215; These were not found</strong>'; $score = 0;}
reporttask_automatic('stocklist.html validates with logo, navigation and table '. $extratext,$score,5);


$score = 0;
$extratext = '';
if(
    strstr($cp['pages/contact.html'],'logo.gif')&&
    strstr($cp['pages/contact.html'],'style.css')&&
    strstr($cp['pages/contact.html'],'<ul>'))
    {
$score = validate_page($check_url.'pages/contact.html');}
else{$extratext ='<strong>&#215; These were not found</strong>'; $score = 0;}
reporttask_automatic('contact.html validates with logo and navigation '.$extratext,$score,5);
$score = 0;


?>


 <?php 
 
 $check_css = @file_get_contents($check_url.'css/style.css');
 echo '<tr class="noprint">
            <td colspan="3"><pre><code class="language-markup">'.str_replace('<', '&lt;', str_replace('&', '&amp;', $check_css)).'</code></pre></td>
        </tr>';


 // CSS editing
 // TODO: add colour check

 createtask('J','Design – Edit CSS'); 



if($check_css === FALSE) {
    $CssParser = new Sabberworm\CSS\Parser(file_get_contents('http://55060509.webdevmmu.uk/test/notfound/style.css'));

}
else{

$CssParser = new Sabberworm\CSS\Parser(file_get_contents($check_url.'/css/style.css'));
 
}
$CssDocument = $CssParser->parse();
 
 $selectors=$CssDocument->getAllRuleSets();
 $content=$CssDocument->getContents();

 $usefulcontent = Array();
 
 foreach($selectors as $selector => $val)
 {       
 $css_selector=$val->getSelectors();
 
 $tmp=$val->getRules();
 $store_value = Array();
     foreach($tmp as $content => $attrib)
 
     {       
     $css_property= $attrib->getRule();
   $css_property = str_replace(" ", "", $css_property);
     $css_value= $attrib->getValue();
     $store_value[$css_property] = (string)$css_value;  // must cast this to a string   
     }    
     
     $usefulcontent[implode($css_selector,',')]=$store_value;
 }




$css_score = 0;

// get comment

$comments=$CssDocument->getContents();
foreach($comments as $selector => $val){
    $css_comments = $val->getComments();
    if(strstr(implode($css_comments), 'border')){
        $css_score = 1;}
    
}



 reporttask_automatic('CSS comments - Comment out the rule from the stylesheet that sets a 1px red border on all elements (do not delete).',$css_score,1);
 $css_score = 0;
 
 ?>


 <?php createtask('K','Typography');
 
 reporttask_manual('Typography with CSS - set a web safe font family for all of the body content. Use at least two typeface names per CSS rule, ending with a third generic family, e.g. &#8216;serif&#8217;.',3,@$usefulcontent['body']['font-family']);

 // if they split them - assume they've split into h1 and h2 - copy h1 as is more likely to be completed

 if(isset($usefulcontent['h1'])&&$usefulcontent['h1']!==""){
     $usefulcontent['h1,h2'] = $usefulcontent['h1'];
 }


 reporttask_manual('Heading Typography with CSS - set a different but harmonious web safe font family for h1 and h2 elements. Use at least two typeface names per CSS rule, ending with a third generic family, e.g. &#8216;serif&#8217;.',3,@$usefulcontent['h1,h2']['font-family']);
 

 
 ?>




 <?php createtask('L','Colour'); 
 
 //https://github.com/gdkraus/wcag2-color-contrast

 $colour_test = 'ffffff'; // assume we will fail

 $colour_start = @$usefulcontent['.main-nav ul']['background-color'];
 
if(!isset($colour_start)&&$colour_start==""){
    $colour_start = @$usefulcontent['.main-nav ul']['background'];
}


if (substr($colour_start,0,1)!=="#"){

    $key = array_search($colour_start, $css_colours);
    $colour_test = @$css_colours[($key-1)];

}else{ 
$colour_test =  str_replace('#', '', $colour_start);



}

if (strlen($colour_test) == 3) {
    $colour_test[5] = $colour_test[2]; // f60##0
    $colour_test[4] = $colour_test[2]; // f60#00
    $colour_test[3] = $colour_test[1]; // f60600
    $colour_test[2] = $colour_test[1]; // f66600
    $colour_test[1] = $colour_test[0]; // ff6600
}

$results = evaluateColorContrast($colour_test,'ffffff');
$score = 0;

if($results['levelAALarge']=='pass'){$score = 1;}
if($results['levelAANormal']=='pass'){$score = 2;}
if($results['levelAAANormal']=='pass'){$score = 3;}


 reporttask_automatic('Colour – Navigation (background-color) – apply a colour scheme using the background-color CSS rule to the .main-nav navigation bar. <span class="no-print" style="background-color: #'.$colour_test.'">&nbsp;&nbsp;&nbsp;</span>',$score,3);
 $score = 0;
 

 $colour_test_link = 'ffffff'; // assume we will fail
 
 $colour_start_link = @$usefulcontent['.main-nav ul a:hover']['color'];
 
// double colours

if (substr($colour_start_link,0,1)!=="#"){

    $key = array_search($colour_start_link, $css_colours);
    $colour_test_link = @$css_colours[($key-1)];

}else{ 
    $colour_test_link =  str_replace('#', '', $colour_start_link);
}


if (strlen($colour_test_link) == 3) {
    $colour_test_link[5] = $colour_test_link[2]; // f60##0
    $colour_test_link[4] = $colour_test_link[2]; // f60#00
    $colour_test_link[3] = $colour_test_link[1]; // f60600
    $colour_test_link[2] = $colour_test_link[1]; // f66600
    $colour_test_link[1] = $colour_test_link[0]; // ff6600
}


 $results = evaluateColorContrast($colour_test,$colour_test_link);
 $score = 0;
 if($results['levelAALarge']=='pass'){$score = 1;}
 if($results['levelAANormal']=='pass'){$score = 2;}
 if($results['levelAAANormal']=='pass'){$score = 3;}

 reporttask_automatic('Colour – Navigation (a:hover) – add an a:hover state for the hyperlink text in your navigation. <span class="no-print" style="background-color: #'.$colour_test_link.'">&nbsp;&nbsp;&nbsp;</span>',$score,3);
 $score = 0;
 

 introduce_task('<span style="color:#777;">Note – For both marks all colour choices should pass AAA for small text.</span>');
 
 
 ?>




 <?php createtask('M','Styling content with a CSS Class'); 
 
 $li_html = $dom['index.html']->getElementsByTagName('li');

 $human_output='';
 foreach($li_html as $li){
    //var_dump($li->getAttribute('class'));
     if($li->getAttribute('class')=='top-wine'){
         $human_output = 'class exists in HTML &middot; Text in li is &#8216;'.$li->nodeValue.'&#8217; &middot; ';
         break;
     }else{
        $human_output = 'class does not exist in HTML or is not attached to li &middot; ';
     }
 }

$garden_rule = @implode(' &middot; ', $usefulcontent['.top-wine']);

 reporttask_manual('Edit your stylesheet to write a CSS rule to style and apply it to an element on the homepage as directed below:<ul><li>Create a class called top-wine</li><li>Apply this to the number one wine list item (Yellow Tail Shiraz)</li><li>Change the colour</li><li>Change the font weight to bold</li></ul>',5,$human_output.$garden_rule);

 
 ?>


 <?php createtask('N','CSS Validation');
 
 //http://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fwww.w3.org%2F&warning=0&profile=css2

 $check_css = @file_get_contents($check_url.'css/style.css');

if($check_css === FALSE) {
    
    $score =0;
}
else{

    $score =0;
    $score = validate_css($check_url.'css/style.css');
 
}

 reporttask_automatic('Style.css validates',$score,5);
 $score =0;

 
 ?>




       



        <tr class="final-result">
            <td colspan="3">TOTAL <span class="result-readout error">incomplete</span>/100</td>

        </tr>

    </table>

    <p contenteditable="true"><strong>Tutor&#8217;s comments</strong> <span class="js-comments"></span></p>
    <p><strong>Marked by</strong> RE, DJW, SMcG</p>






 





</body>

<!-- scan through all span.add-to-mark and input.get-result and add them to the score. Only update span.result-readout when all are full. -->


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script type="text/javascript" src="js/marking.js"></script>


</html>