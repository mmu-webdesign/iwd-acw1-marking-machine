<?php
//error_reporting(E_ALL); 
//ini_set('display_errors', 'on');
error_reporting('E_NONE');

$student_number = $_GET['for'];
$student_name = $_GET['name'];
$student_group = $_GET['group'];
$student_url = $_GET['url'];

$local_location ='student-sites/iwd-2020/'.$student_number;

include('config.php');

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




function validate_page($html){
   
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
         array_push($testing, str_replace("<", "&lt;", $v['message']));  
        }
        
    }
    
    return $testing;

}



function validate_css($code){
 


        
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
         @array_push($testing, "<p>&#215; Line ".$possible_errors[$i]['mline'].": ".$possible_errors[$i]['merrortype']." for ". $possible_errors[$i]['mcontext']."</p>");  
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


function check_code($page, $list, $code, $text){
  $code = strtolower($code);
  $listing=explode(',', $list);
  
  $wrong = 0;
  echo '<p style="margin-top: 0.5rem;">Checking for '.count($listing).' code elements on '.$page.'&#8230;</p>';
  

  foreach($listing as $l){
    
    if(!strstr($code ,'<'.$l )&&!strstr($code,'</'.$l)){
      echo '<p>&#215; '.$l . ' not found</p>'; $wrong++;
    }
    
  }
  
  if($wrong==0){echo '<p>&#10004; Evidence of all elements found</p>';}
  
//echo $code;
  
  if(!strstr($code, $text)){echo '<p>&#215; <b>Text string</b> &#8216;'.$text.'&#8217; not found.</p>';}else{echo '<p>&#10004; <b>Text string</b> &#8216;'.$text.'&#8217; in place.</p>';}
  
}


function display_code($readout){
    
  $readout = str_replace('  ', ' ', $readout);
  $readout = str_replace('  ', '', $readout);
  $readout = str_replace(PHP_EOL, '', $readout);

  echo '<div class="coding"><pre><code class="language-markup">' . str_replace('<', '&lt;', str_replace('&', '&amp;', $readout)) . '</code></pre></div>';
}




function findCSS($dir)
{
  global $css_files;
    $ffs = @scandir($dir);
    
    $array = array(".css", ".html");
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
              

                //Users/derren/Desktop/IWD-ACW2/05151884/images/film.jpg

                $getlink = $dir . '/' . $ff;
                $getlink = str_replace('//', '/', $getlink);
                //$getlink = str_replace("/Users/derren/Desktop/check/", 'http://www.55060509.webdevmmu.uk/check/', $getlink);
                
                if(strstr($getlink, '.html')){
                  
                  $extract_css = implode('', file($getlink));
                  preg_match_all('/<style>(.*?)<\/style>/is', $extract_css, $possible_css);
                  $possible_css = array_merge(...$possible_css);
                  // I have no idea why it's the first item in the array
                 if(isset($possible_css[1])&&$possible_css[1]!==""){
                
                 array_push($css_files, $possible_css[1]);
                }
                }else{
                
if(!strstr($getlink, 'fonts')){

               array_push($css_files, implode('', file($getlink)));
              }
            }
            }
        }

        if (is_dir($dir . '/' . $ff)) {
            findCSS($dir . '/' . $ff);
        }

    }
return $css_files;
}

function check_css_code($code, $list, $number){
  
  $code = strtolower($code);
  
  $wrong = 0;
  echo '<p style="margin-top: 0.5rem;">Checking for '.count($list).' CSS properties in '.$number.' files&#8230;</p>';
  

  foreach($list as $l){
    
    if(!strstr($code, $l)){
      echo '<p>&#215; '.$l . ' not found</p>'; $wrong++;
    }
    
  }
  
  if($wrong==0){echo '<p>&#10004; Evidence of all properties found</p>';}
  
  
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php 
    
    $title_name = explode(',', strtolower($student_name));
    echo $title_name[0]; 
    echo '-';
    echo substr(trim($title_name[1]),0,1);
    
    ?>-<?php echo $student_number; ?></title>
    <link rel="stylesheet" href="css/prism.css" />
    <script src="js/prism.js"></script>
    <link rel="stylesheet" type="text/css" href="css/mark.css" />
    <style type="text/css">
      .range#metadata h3 {
        border-bottom: 1px solid #eee;
        padding: 0.3rem 0;
      }
      .range#metadata p + h3 {
        margin-top: 0.5rem;
      }
    </style>
  </head>

  <body>
    <h1>Manchester Metropolitan University iSchool</h1>
    <h2>
        <span class="course"><?php echo $unit_name; ?></span>Assignment One Feedback</h2>

        <ul class="information">
        <li>Date <strong><?php echo date('jS F Y'); ?></strong></li>
        <li>Name <strong><?php echo $student_name; ?></strong></li>
        <li>Student ID and Group <strong><?php echo $student_number; ?> &middot; <?php echo $student_group; ?></strong></li>
    </ul>

   

    <h2 class="subheading">HTML Exercises</h2>
    <div class="noprint">
    Information for these exercises
    </div>
    <section class="range" id="htmlexercises">
      <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          ><b>Choose a mark first&#8230;</b><br />
          0-19 Not present or not participated &middot;
          40-49 Major errors or omissions<br />
          60-69 Minor errors or omissions &middot;
          86-100 Exercises are completed accurately</span
        >
      </div>

      <h3>What you did well</h3>
      <p contenteditable="true">[Feedback on your work]</p>
      <h3>What you should look at again</h3>
      <p contenteditable="true">[Feed-forward on your work for assessment 2]</p>
    </section>

 
    
    <h2 class="subheading">CSS Exercises</h2>
    <div class="noprint">
  <?php
  
  $css_files = array();
  
  findCSS($local_location.'/css-book-1/');
  
 findCSS($local_location.'/css-book-2/');

 $found_files = count($css_files);
 
$css_to_check = implode('', $css_files);

$list= array('color','border','width','visited','font-size',',','background-','list-style','font-style','font-family','@font-face','box-sizing','position','display','margin','padding');

//echo $css_to_check;

check_css_code($css_to_check, $list, $found_files);
$css_to_check = str_replace('PHP_EOL', ' ', $css_to_check);
$css_to_check = str_replace('{ ', '{', $css_to_check);
$css_to_check = str_replace('} ', '}', $css_to_check);
$css_to_check = str_replace('; ', ';', $css_to_check);
$css_to_check = str_replace("\r\n",'', $css_to_check);
$css_to_check = str_replace("\n",'', $css_to_check);
$css_to_check = str_replace("\r",'', $css_to_check);
//echo $css_to_check;
$css_result = validate_css($css_to_check);

if(count($css_result)==0){
  
echo '<p style="margin-top: 0.5rem;">&#10004;  <strong>Collected CSS validates</strong></p>';
}else{
  
echo '<p style="margin-top: 0.5rem;">&#215; <strong>Collected CSS does not validate</strong></p>';
foreach($css_result as $r){
  echo $r;
}
}


  ?>
    
    
    </div>
    <section class="range" id="cssexercises">
      <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          ><b>Choose a mark first&#8230;</b><br />
          0-19 Not present or not participated &middot;
          40-49 Major errors or omissions<br />
          60-69 Minor errors or omissions &middot;
          86-100 Exercises are completed accurately</span
        >
      </div>
      <h3>What you did well</h3>
      <p contenteditable="true">[Feedback on your work]</p>
      <h3>What you should look at again</h3>
      <p contenteditable="true">[Feed-forward on your work for assessment 2]</p>
    </section>

  
    
 <h2 class="subheading">Image optimisation exercise</h2>
 <div class="noprint"><p><b>Images found</b> in image-optimisation/optimised</p><ul class="image-readout">
  <?php
  
  listFolderFiles($local_location.'/image-optimisation/optimised');
  
  ?></ul>
    </div>
    <section class="range" id="imageoptimisation">
      <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          ><b>Choose a mark first&#8230;</b><br />
          0-19 Not present or not participated &middot;
          40-49 Major errors or omissions<br />
          60-69 Minor errors or omissions &middot;
          86-100 Exercises are completed accurately</span
        >
      </div>
      <h3>What you did well</h3>
      <p contenteditable="true">[Feedback on your work]</p>
      <h3>What you should look at again</h3>
      <p contenteditable="true">[Feed-forward on your work for assessment 2]</p>
    </section>
    
    <p class="pagebreak"></p>

<?php

// collect our final exercise

$pages = array();
$dom= array();
$pages['index'] = @implode('', file($local_location.'/final-exercise/index.html'));
$pages['cv'] =  @implode('', file($local_location.'/final-exercise/pages/cv.html'));
$css =  @implode('', file($local_location.'/final-exercise/style.css'));



$dom['index'] = new DOMDocument;
@$dom['index']->loadHTML($pages['index']);

$dom['cv'] = new DOMDocument;
@$dom['cv']->loadHTML($pages['cv']);

?>



    <h2 class="subheading">Final Exercise</h2>
    
    <?php
    
    echo '<div class="noprint">';
echo '<p>Index page is <b>'.strlen($pages['index']).'</b> characters (2000 expected)</p>';
echo '<p>CV page is <b>'.strlen($pages['cv']).'</b> characters (2000 expected)</p>';
echo '<p>CSS file is <b>'.strlen($css).'</b> characters (850 expected)</p>';
echo '</div>';
    
    ?>
    <div class="noprint">
    <?php 
    
    // METADATA
   
    foreach($pages as $location=>$page){
    
    $res = preg_match("/<title>(.*)<\/title>/siU", $page, $title_matches);
    $title_matches=implode('', $title_matches);
   
    echo '<p><b>Title for '.$location.'</b><br />'.$title_matches.'</p>';
    
  }

  foreach($dom as $page=>$d){
$metadata = new DOMXPath($d);
$get_tag = $metadata->query('//meta');
echo '<p><b>Metadata for '.$page.'</b></p>';
foreach ($get_tag as $tag) {
if(!strstr($tag->getAttribute('content'),'device-width')){
 echo '<p>'.$tag->getAttribute('content').'</p>';
}
}
}


    ?>
    
  
    </div>
    <section class="range" id="metadata">
      <h2>
        Metadata<span
          >Marked by reading the metadata in the HTML – the meta description tag
          and the title tag</span
        >
      </h2>
      <div class="marks-final">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section>
    <div class="noprint">
  <?php
     $find = array();
     $find['index'] = 'header,nav,ul,li,main,figure,figcaption,img,h2,p,em,strong,footer'; //img works because it finds <img though it doesn't find </img, but it's a boolean AND
     $find['cv']='header,nav,main,table,tr,td,th,ol,ul,li,footer,p';
     
     check_code('Homepage',$find['index'], $pages['index'],'student coursework');
     check_code('CV page', $find['cv'], $pages['cv'],'student coursework');
  
  
     $passed_validation = array();
     array_push($passed_validation, validate_page($pages['index']));
     array_push($passed_validation, validate_page($pages['cv']));
     
if(count(array_merge(...$passed_validation))>0){
  echo '<p style="margin-top: 0.5rem;"><b>HTML did not validate.</b> Errors may be in index.html or cv.html.</p>';
$messages = array_merge(...$passed_validation);
foreach($messages as $m){
  echo '<p>&#215; '.$m.'</p>';
}

}else{
  echo '<p style="margin-top: 0.5rem;">&#10004; <b>HTML validates</b></p>';
}

  if(count(array_merge(...$passed_validation))==0){$validation_message="<p>
    &#10004; Your site&#8217;s HTML pages validated: 
    <strong>no errors found</strong>
  </p>";}else{$validation_message='
    <p>
          &#215; Your site did/did not validate:
          <strong>'.count($passed_validation).' error(s) found</strong>
        </p>';
  }
  
  display_code($pages['index']);
  ?>
    
    </div>
    <section class="range" id="working_with_html">
      <h2>
        Working with HTML to add content as requested<span
          >Marked by validating the HTML and reading the code</span
        >
      </h2>
      <div class="marks-context" contenteditable="true">
        <?php echo $validation_message; ?>
      </div>
      <div class="marks-final">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section>
    <div class="noprint">
    <?php foreach($dom as $page=>$d){
$paths = new DOMXPath($d);
$get_paths = $paths->query('//nav //a');
echo '<p><b>Navigation links for '.$page.'</b></p>';
foreach ($get_paths as $tag) {

 echo '<p>'.$tag->getAttribute('href').'</p>';

}
}

$src = new DOMXPath($dom['index']);
    $get_src = $src->query('//img');
    echo '<p><b>Source attribute for image on homepage</b></p>';
    foreach ($get_src as $src_img) {
   {
     echo '<p>'.$src_img->getAttribute('src').'</p>';
    }
    
    }
    
?>

    </div>
    <section class="range" id="working_with_files">
      <h2>
        Working with file paths to add site navigation and external assets as requested<span
          >Marked by reading the code</span
        >
      </h2>
      
      <div class="marks-final">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section>
    <div class="noprint">
      
<?php

$strings_to_find = array('html','body','figcaption','.main-navigation','a:','table','text-transform:uppercase');
$problems = array();
$modified_css = str_replace(' ', '', $css);
$modified_css = str_replace("\n", '', $modified_css);

foreach($strings_to_find as $s){
  if(!strstr($modified_css, $s)){
    array_push($problems, $s);
  }
  
}

if(count($problems)==0){
  echo '<p>&#10004; CSS file has evidence of required additions.</p>';
  
}else{
  echo '<p>These required additions seem to be missing:</p>';
  foreach($problems as $p){
    echo '<p>&#215; '.$p.'</p>';
  }

}

  $css_result = validate_css($css);
   
  if(count($css_result)==0){
    $validation_message = '<p>
    &#10004; Your CSS validated:
    <strong>no errors found</strong>
  </p>';
  echo '<p style="margin-top: 0.5rem;"><strong>CSS validates</strong></p>';
  }else{
    $validation_message = '<p>
    &#215; Your CSS did not validate:
    <strong>'.count($css_result).' error(s) found</strong>
  </p>';
  echo '<p style="margin-top: 0.5rem;"><strong>CSS does not validate</strong></p>';
  foreach($css_result as $r){
    echo $r;
  }
  }
  
  
  display_code($css);
?>

    
    </div>
    <section class="range" id="working_with_css">
      <h2>
        Working with CSS to add styling as requested<span
          >Marked by validating and reading your CSS file</span
        >
      </h2>
      <div class="marks-context" contenteditable="true">
        <?php
        echo $validation_message;
        ?>
      </div>
      <div class="marks-final">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div> 
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section>
    <div class="noprint">
   <?php
   

    $alt = new DOMXPath($dom['index']);
    $get_alt = $alt->query('//img');
    echo '<p><b>Alt text for any images on index page</b></p>';
    foreach ($get_alt as $alt) {
   {
     echo '<p>'.$alt->getAttribute('src').' &middot; '.$alt->getAttribute('alt').'</p>';
    }
    
    }
   
   ?>
    </div>
    <section class="range" id="accessibility">
      <h2>
        Accessibility of content<span
          >Marked by reading alt text and checking for colour contrast as requested</span
        >
      </h2>
      <div class="marks-final">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section>
    <div class="noprint"><p><b>Images found</b></p><ul class="image-readout">
   <?php
   listFolderFiles($local_location.'/final-exercise/');
   ?></ul>
    </div>
    
    <section class="range" id="imageimplementation">
      <h2>
      Image optimisation<span
        > Are your images resized and optimised for efficient downloading?</span
      >
    </h2>
    <div class="marks-final">
      <ul>
        <li><label>0-19</label></li>
        <li><label>20-34</label></li>
        <li><label>35-39</label></li>
        <li><label>40-49</label></li>
        <li><label>50-59</label></li>
        <li><label>60-69</label></li>
        <li><label>70-85</label></li>
        <li><label>86-100</label></li>
      </ul>
    </div>
    <div class="range-content">
      <span contenteditable="true" class="autocontent"
        >Choose a mark first&#8230;</span
      >
    </div>
  </section>
  
  
    <section class="range" id="implementation">
        <h2>
        Overall mark for the final exercise <b style="font-weight:700;" class="final-overall">[x]</b>%<span
          > As a simple average of the marks above</span
        >
      </h2>
      <!-- <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div> -->
    </section>
    
    
      <!-- <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div> -->
    </section>

    <!-- <section class="range" id="quality_of_site_preparation">
      <h2>
        Quality of site preparation<span
          >This includes file naming and folders. Marked by viewing submission
          files</span
        >
      </h2>
      <div class="marks">
        <ul>
          <li><label>0-19</label></li>
          <li><label>20-34</label></li>
          <li><label>35-39</label></li>
          <li><label>40-49</label></li>
          <li><label>50-59</label></li>
          <li><label>60-69</label></li>
          <li><label>70-85</label></li>
          <li><label>86-100</label></li>
        </ul>
      </div>
      <div class="range-content">
        <span contenteditable="true" class="autocontent"
          >Choose a mark first&#8230;</span
        >
      </div>
    </section> -->
    
    <p class="pagebreak"></p>
    
    <h2 class="subheading">Overall marks for the 4 exercises</h2>
    <section class="box">
      <h2>Any final comments</h2>
      <p contenteditable="true">[Anything the tutors want to add about your work]</p>
    

    <div class="mark">
     <h2>Overall mark</h2>
     <ul><li><span class="what">HTML exercise 1 &amp; 2</span> <span class="my-mark html"><span>[x]</span>%</span> <span class="worth">worth 22.5%<span></li>
      <li><span class="what">CSS exercise 1 &amp; 2</span> <span class="my-mark css"><span>[x]</span>%</span> <span class="worth">worth 22.5%<span></li>
      <li><span class="what">Image exercise</span> <span class="my-mark images"><span>[x]</span>%</span> <span class="worth">worth 5%<span></li>
      <li><span class="what">Final exercise</span> <span class="my-mark final"><span>[x]</span>%</span> <span class="worth">worth 50%<span></li>
     
     <li><span>Total mark</span> <span class="my-mark all"><span>[x]</span>%</span></li>
     
      </ul>
    </div>
  </section>
    <section class="finish">Marked by IWD teaching team</section>

    <div class="average-readout noprint"></div>
  </body>

  <!-- scan through all span.add-to-mark and input.get-result and add them to the score. Only update span.result-readout when all are full. -->

  <script
    type="text/javascript"
    src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"
  ></script>

  <script type="text/javascript" src="js/marking.js"></script>
</html>
