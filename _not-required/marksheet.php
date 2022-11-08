<?php
error_reporting(E_ALL); ini_set('display_errors', 'on');

$student_number = $_GET['for'];
$student_name = $_GET['name'];
$student_group = $_GET['group'];
$student_url = $_GET['url'];

$local_location ='student-sites/iwd-2020/'.$student_number;

include('config.php');

$standard_javascript = <<<JSJS

// OPEN THE CONSOLE BELOW TO SEE THE JAVASCRIPT RUNNING

// find out what time it is

var currenttime = new Date();
var currenthour = currenttime.getHours();

console.log(currenthour);

// do a conditional

var emailmessage = "You are <strong>OK</strong> to email me now";

if (currenthour < 9 || currenthour > 22) {
  emailmessage = "I am asleep, sorry!";
}

var getheading = document.querySelector(".contact-me h2");
console.log(getheading);
getheading.insertAdjacentHTML(
  "afterend",
  '<p class="js-message">' + emailmessage + "</p>"
);

// For extra credit, you might now think about...
// using getTimezoneOffset(); to see if the user is in the same timezone as you, and modify the message accordingly?
//
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/getTimezoneOffset
//
// or using setInterval to check every so often what the time is for the user, if they've been on your site for an hour or so?
// 
// https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setInterval

// tracking the user's interactions with the page

var report_number = 1; // The number of times we've sent information to the console
var seconds_between_reports = 5; // how often we send the information
var start_time = new Date(); // what time is it now?
var scroll_count = 0; // the amount the user has scrolled. We set this up here so the variable has global scope.
var click_count = 0; // the number of times the user has clicked on things. We set this up here so the variable has global scope.
var click_things = Array(); // a string which will contain all the things the user has clicked on, even if they aren't hyperlinks
var oldPageOffset = 0; // we need this to set up our page scrolling counter - we only want to measure the distance between each scroll, not the distance the user is from the top of the page (not confusing at all...)

function reportOnUserBehaviour() {
    var current_time = new Date(); // we can then compare this with start_time to see how long the user has been on the page.

    // We could log this to the console, as we are doing here, or (much more advanced) save it to a spreadsheet/database using AJAX to review later

    console.log('Report ' + report_number);
    console.log('In the last ' + seconds_between_reports + ' seconds, the user has...');
    console.log('* scrolled ' + scroll_count + ' pixels');
    console.log('* clicked on ' + click_count + ' things');
    if (click_count > 0) {
        for (i = 0; i < click_things.length; i++) {
            console.log('* ' + click_things[i]);
        }
    }

    console.log('* spent a total of ' + Math.round((current_time - start_time) / 1000) + ' seconds on this page');

    scroll_count = 0;
    click_count = 0; // reset these variables
    click_things = []; // empty the array
    report_number++; // increase our report number
}

// create a function to add the distance the user has scrolled to the scroll_count variable

function handlePageScroll() {
    newPageOffset = window.pageYOffset // where are we now?
    scroll_count = scroll_count + Math.abs(newPageOffset - oldPageOffset); // add the absolute difference (i.e. doesn't matter if it's positive or negative) between where we were and where we are
    oldPageOffset = newPageOffset; // make a note of where the page is so we can use this value when, or if, the user scrolls again 

}

// for this function we are passing in the click event that we have noticed happening in the browser
function handleDocumentClick(event) {
    click_count++; // increase the click_count variable
    click_things.push(event.target.tagName + ' tag'); // push the thing someone clicked on into an array for display later on
}

// set our reporting function going. This will run until the window is closed.
setInterval(function() {
    reportOnUserBehaviour();
}, seconds_between_reports * 1000);

document.onscroll = handlePageScroll; // Asynchronous code: whenever the user scrolls the document, run the function handlePageScroll, which will modify our scroll_count variable, ready to report back to the console.

document.body.onclick = handleDocumentClick; // Asynchronous code: 'listen' for when the user clicks on something in the document body, and then run the function handleDocumentClick, which will modify our click_count and click_area variables, ready to report back to the console.

JSJS;

include('includes/colour-contrast.php');
include('includes/colours.php');

require_once('includes/vendor/autoload.php'); 

$location = str_replace('/[id]/','',$check_url);


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




function validate_page($url){
    $html = @file($url);
    $html = implode('', $html);
    $ch = curl_init(); 
    
    curl_setopt_array($ch,array(
        CURLOPT_URL=>'http://validator.w3.org/nu/',
        CURLOPT_ENCODING=>'',
        CURLOPT_USERAGENT=>'PHP/'.PHP_VERSION.' libcurl/'.(curl_version()['version']),
        CURLOPT_POST=>1,
        CURLOPT_POSTFIELDS=>array(
            'out'=>'json',
            'showsource'=>'no',
            'content'=>$html,
            
        ),
        CURLOPT_RETURNTRANSFER=>1,
    ));
    
    
    // curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    
    // // set url 
    // curl_setopt($ch, CURLOPT_URL, $validator_url); 
    // curl_setopt($ch,CURLOPT_POST,1)
    // curl_setopt($ch,CURLOPT_POSTFIELDS,array(
    //     'showsource'=>'yes',
    //     'content'=>$html
    // ));
    
    //return the transfer as a string 
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP/'.PHP_VERSION.' libcurl/'.(curl_version()['version'])); 
    
    // $output contains the output string 
    $output = curl_exec($ch); 

    //print_r($output);
    
    
    
    $vr='';
    $vr = json_decode($output, true);
    //print_r($vr);
    $testing = array();
    foreach($vr['messages'] as $v){
        if( $v['type']=="error"){
         array_push($testing, "<p>".str_replace("<", "&lt;", $v['message'])."</p>");  
        }
        
    }
    
    return $testing;
    //$v_score = 0;

//     if(isset($vr['messages'][0]['type'])&&$vr['messages'][0]['type']=="non-document-error"){
//         $v_score = "validator unavailable";
//         return $v_score;
// }else{
//     if(count($vr['messages'])==0){
//         $v_score = 'pass';
//         return $v_score;
        
//     }else{
//         return $v_score;
        
//     }
// }
}



function validate_css($url){
    
    $code = file($url);
    $code = implode('',$code);
    //print_r($code);
 
    $css_files = "no CSS found";

    $dom = new DOMDocument;
@$dom->loadHTML($code);
$check_css_link = array();
$link_check = $dom->getElementsByTagName('link');
$p=0;
    foreach($link_check as $l){
        $check_css_link[$p]=$l->getAttribute('href');
        $p++;
    $css_files = '';
}
//print_r($check_css_link);
foreach($check_css_link as $c){

    if(!strstr($c, 'google')
    &&!strstr($c, 'font-awesome')
    &&!strstr($c, 'font-awsome')
    &&!strstr($c, 'fontawesome')
    &&!strstr($c, 'favicon')
    &&!strstr($c, 'ico')
    &&!strstr($c, 'png')
    &&!strstr($c, 'jpg')
    ){
$url = str_replace('index.html', '', $url);
$url = str_replace('Homepage.html', '', $url);
$url = str_replace('index2.html', '', $url);
$code = file($url.$c);
$code = implode('', $code); 

    }
    
}


        
    $validator_url = 'http://jigsaw.w3.org/css-validator/validator?text='.urlencode($code).'&warning=0&profile=css3&output=soap12';
    
    //echo $validator_url;

    
    $ch = curl_init(); 
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    
    // set url 
    curl_setopt($ch, CURLOPT_URL, $validator_url); 
    
    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MMU HTML validator'); 
    
    // $output contains the output string 
    $output = curl_exec($ch); 
    //print_r($output);
    
    $xml = $output;
// SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out

$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $xml);
$xml = simplexml_load_string($xml);
$json = json_encode($xml);
$cssInformation = json_decode($json,true);

//print_r($cssInformation);
$totalerrors = $cssInformation['envBody']['mcssvalidationresponse']['mresult']['merrors']['merrorcount'];


    

if($totalerrors == 0){
    
    $testing = array();
}else{
    $possible_errors = $cssInformation['envBody']['mcssvalidationresponse']['mresult']['merrors']['merrorlist']['merror'];
//print_r($possible_errors);
   

    $testing = array();
    
    //echo count($possible_errors);
    
    if(isset($possible_errors['mline'])&&$possible_errors['mline']!==""){
        
        @array_push($testing, "<p>Line ".$possible_errors['mline'].": ".$possible_errors['merrortype']." for ". $possible_errors['mcontext']."</p>"); 
        
    }else{
    
    for($i=0; $i<count($possible_errors); $i++){
    
        //print_r($v);
         @array_push($testing, "<p>Line ".$possible_errors[$i]['mline'].": ".$possible_errors[$i]['merrortype']." for ". $possible_errors[$i]['mcontext']."</p>");  
        }
    }
}
    

    
    return $testing;

}



function listFolderFiles($dir)
{
    
    $ffs = @scandir($dir);
    
    $array = array(".jpg", ".png", ".gif", ".bmp", '.svg', '.eps', '.psd', '.JPG', '.PNG', '.GIF', '.BMP', '.SVG', '.EPS', '.PSD', '.jpeg', '.JPEG');
    unset($ffs[@array_search('.', $ffs, true)]);
    unset($ffs[@array_search('..', $ffs, true)]);

    // prevent empty ordered elements
    if (count($ffs) <= 1) {
        return;
    }

    foreach ($ffs as $ff) {
        foreach ($array as $string) {
            if (strpos($ff, $string) !== false) {
                $filesize = filesize($dir . '/' . $ff);
                $filedimensions = getimagesize($dir . '/' . $ff);

                //Users/derren/Desktop/IWD-ACW2/05151884/images/film.jpg

                $getlink = $dir . '/' . $ff;
                $getlink = str_replace('//', '/', $getlink);
                //$getlink = str_replace("/Users/derren/Desktop/check/", 'http://www.55060509.webdevmmu.uk/check/', $getlink);

                $checkfile = round($filesize / 1024, 2);
                if ($checkfile > 1024) {$addtext = 'bad';} else { $addtext = '';}
                echo '<li><span>' . $ff . '</span><span class="dimensions">' . $filedimensions[0] . ' &#215; ' . $filedimensions[1] . '</span> <span class="filesize ' . $addtext . '">' . $checkfile . 'KB</span><a href="' . $getlink . '">view</a></li>';
            }
        }

        if (is_dir($dir . '/' . $ff)) {
            listFolderFiles($dir . '/' . $ff);
        }

    }

}


function createrange($heading, $explanation, $from_site = '', $add_to_text=''){

    $create_class = strtolower(str_replace(' ', '_', $heading));

    if($from_site!==""){

echo '<div class="content-from-site noprint">'.$from_site.'</div>';
}
    echo '<section class="range" id="'.$create_class.'">';

    echo '<h2>'.$heading.'<span>'.$explanation.'</span></h2>';

    $mark_list = array(
        
      '0-19',
      '20-34',
      '35-39',
      '40-49',
      '50-59',
      '60-69',
      '70-85',
      '86-100'
        
    );

    if($add_to_text){
        echo '<div class="marks-context" contenteditable="true">'.$add_to_text.'</div>';
    }
    
    echo '<div class="marks"><ul>';
    foreach($mark_list as $m){
        echo '<li><label>'.$m.'</label></li>';
    }
echo '</ul></div>';
echo '<div class="range-content">';



echo '<span contenteditable="true" class="autocontent">Choose a mark first&#8230;</span>';

//echo '<h2>Further comments</h2><p contenteditable="true">none</p>';

echo '</div>';
echo '</section>';
// possibly add text area in the future?


}

function createreadout($heading, $result){

if($result == 'Pass'){$result=' &#10004; '.$result;}
if($result == 'Fail'){$result=' &#215; '.$result;}
    echo '<section class="readout">';

    echo '<h2>'.$heading.'</h2>';


    echo '<p contenteditable="true">'.$result.'</p>';

    echo '</section>';


}


function createbox($heading){

    global $student_name;

    $student_name = explode(' ', $student_name);

    $student_name = $student_name[1];

    echo '<section class="box">';

    echo '<h2>'.$heading.'</h2>';


    echo '<p contenteditable="true">'.$student_name.', this is</p>';

    echo '</section>';


}

function createheading($content, $subcontent=""){
    echo '<h2 class="subheading">'.$content;
    
    if($subcontent!==""){
        echo '<i>'.$subcontent.'</i>';
    }
    
    echo '</h2>';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php 
    
    $title_name = explode(',', strtolower($student_name));
    echo $title_name[0]; 
    echo '-';
    echo substr(trim($title_name[1]),0,1);
    
    ?>-<?php echo $student_number; ?></title>
    <link rel="stylesheet" href="css/prism.css">
<script src="js/prism.js"></script>
<link rel="stylesheet" type="text/css" href="css/mark.css">


</head>

<body>

    <h1>Manchester Metropolitan University iSchool</h1>
    <h2>
        <span class="course"><?php echo $unit_name; ?></span>Assignment Two Feedback</h2>

    <ul class="information">
        <li>Date <strong><?php echo date('jS F Y'); ?></strong></li>
        <li>Name <strong><?php echo $student_name; ?></strong></li>
        <li>Student ID and Group <strong><?php echo $student_number; ?> &middot; <?php echo $student_group; ?></strong></li>
    </ul>



<?php

if($_GET['checkable']=="yes"){
$page = file($student_url);
$page=implode('', $page);
}


$html_content="";
if($_GET['checkable']=="yes"){
    $res = preg_match("/<title>(.*)<\/title>/siU", $page, $title_matches);
    $title_matches=implode('', $title_matches);
    $tags = get_meta_tags($student_url);
    
    $html_content = 'Title: '.$title_matches. '<br />';
    foreach($tags as $key=>$t){
        $html_content .='Meta tag "'.$key.'" set to "'.$t.'"<br />';
    }
    //print_r($tags);
    

}

//createheading('Technical aspects');

createrange('Metadata','Marked by reading the metadata in the HTML – the meta description tag and the title tag',$html_content);



$validation='Fail';
if($_GET['checkable']=="yes"){
$tested = validate_page($student_url);
//print_r($tested);
if(count($tested)==0){$validation="Pass"; $validation_message="<p>&#10004; Your site passed HTML validation</p>";}else{$validation_message = "<p>&#215; Your site did not validate: <strong>".count($tested).' error(s) found</strong></p>';}
}

echo '<div class="noprint"><p>HTML validation: '.$validation.'</p>';

if(count($tested)>0){
    echo '<ol>';

foreach($tested as $t){
    echo "<li>".$t."</li>";
}

echo '</ol>';

}
echo '</div>';

// createreadout('HTML 5 Code validation', '<span class="noprint">'.count($tested).' errors found</span> &#10004; PASS &#215; FAIL');

createrange('Working with HTML','Marked by validating the HTML and reading the code', '', $validation_message);


$validation='Fail';
if($_GET['checkable']=="yes"){
$tested = validate_css($student_url);
//print_r($tested);
if(count($tested)==0){$validation="Pass"; $validation_message="<p>&#10004; Your CSS validated</p>";}else{$validation_message = "<p>&#215; Your CSS did not validate: <strong>".count($tested).' error(s) found</strong></p>';}
}

echo '<div class="noprint"><p>CSS validation: '.$validation.'</p>';


if(@count($tested>0)){
    echo '<ol>';

foreach($tested as $t){
    if($t!==""){
    echo "<li>".$t."</li>";
    }
}

echo '</ol>';

}
echo '</div>';

createrange('Working with CSS','Marked by validating and reading your CSS file', '', $validation_message);


$js_report ='Warning: something has not worked';
$dom = new DOMDocument;
@$dom->loadHTML($page);
$script_check = $dom->getElementsByTagName('script');
if($script_check->length>0){
    $all_code ='';
    foreach($script_check as $s){
        if($s->getAttribute('src')!==""&&!strstr($s->getAttribute('src'),'fontawesome')){
            $get_code = @implode('',file($student_url.$s->getAttribute('src')));
            
            $all_code.=$get_code;}else{
            $all_code.=$s->nodeValue;}
        
}

$sim = similar_text($all_code, $standard_javascript, $perc);



$js_report = mb_strlen($all_code) .' characters of JavaScript found. This is '.round($perc,2).'% the same as the provided code.';

}
else{$js_report = 'No JavaScript found';}
    


echo '<p class="noprint">'.$js_report.'</p>';


createrange('Working with JavaScript', 'Marked by viewing code and visually checking submission');


$alt_text="";

if($_GET['checkable']=="yes"){
    
    $how_many = explode('<img', $page);
    
   $alt_text = '<div class="no-print"><p>'.(count($how_many)-1).' Image(s) found</p><p>Alt text found:</p><ol>';
    
    preg_match_all('/<img(.*?)alt=\"(.*?)\"(.*?)>/si', $page, $out, PREG_SET_ORDER);

  //  print_r($out);

    foreach($out as $o){
        $alt_text.='<li>'.$o[2].'</li>';
    }

    $alt_text .= '</ol></div>';
}


createrange('Accessibility','Marked by reading alt text and scanning your source code',$alt_text);


echo '<div class="noprint"><p>Images found:</p><ul class="image-readout">';
listFolderFiles($local_location);
echo '</ul></div>';
createrange('Image preparation and usage','Marked by reviewing pixel dimensions and file sizes of images');

createrange('Responsive Web Design implementation and usability','Marked by viewing site on mobile device and inspecting source code');


$listing = '<div class="no-print"><p>Folders found:</p><ol>';
$dirs = array_filter(glob($local_location.'/*'), 'is_dir');
//print_r($dirs);
foreach($dirs as $d){
    $listing.='<li>'.$d.'</li>';
    
}
$listing.='</ol></div>';



//echo '<p class="pagebreak"></p>';

//createheading('Design and content');

createrange('Design, writing and attention to detail','Marked by reading content and viewing page');

$no_js = html_entity_decode(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $page));

// echo '<textarea>'.str_replace('<', '&lt;', $no_js).'</textarea>';
// echo '<textarea>'.str_replace('<', '&lt;',strtolower($no_js)).'</textarea>';
// echo '<textarea>'.str_replace('<', '&lt;',strip_tags($no_js)).'</textarea>';
// echo '<textarea>'.strip_tags(strtolower($no_js)).'</textarea>';

//echo '<hr />'.strip_tags(strtolower($no_js)).'<hr />';

$word_count = str_word_count(strip_tags(html_entity_decode(strtolower($no_js))));

if($word_count == 0){
    $word_count = str_word_count($no_js)/1.6; // this is made up
}

$total_expected = 800;

$results = 'We found about '.$word_count.' words <strong>'.round(($word_count/$total_expected)*100,1).'% of the 800 word brief</strong>';

//echo '<textarea>'.strip_tags(strtolower($page)).'</textarea>';

//createrange('Writing and content','Marked by reading content: we found around <strong>'.$word_count.' words &middot; '.round(($word_count/$total_expected)*100,1).'%</strong> of the 800 word brief');

//createrange('Use of imagery','May include photographs and/or illustrations. Marked by viewing page');

//createrange('Usability','Marked by viewing page and interacting with it');

createrange('Quality of site preparation','This includes file naming and folders. Marked by viewing submission files', $listing); 

createheading('Overall mark', 'as a simple average of the above within the stepped marking system');

createbox('Summary comments');



?>
<p class="noprint">95 - 100 &middot; 85 80 75 72 &middot; 68 65 &middot; 62 58 55 52 48 45 42 &middot; 38 35 32 28 25 22 18 15 12 8 5 2 0</p>
<div class="mark">Overall mark <span class="final-mark" contenteditable="true">Add This%</span></div>
<section class="finish">Marked by Richard Eskins/Derren Wilson</section>


<div class="average-readout noprint"></div>
</body>

<!-- scan through all span.add-to-mark and input.get-result and add them to the score. Only update span.result-readout when all are full. -->


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script type="text/javascript" src="js/marking.js"></script>


</html>