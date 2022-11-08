<?php

include('config.php');



// set ?screenshots=1 to output a list of screenshots for the screenshot.js file
// run node screenshot.js to get the screenshots

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student work list with screenshots</title>

    
<link rel="stylesheet" type="text/css" href="css/listing.css">
    
</head>
<body>

    <header>
    <h1>View submissions for <strong><?php echo $unit_name; ?></strong></h1>  
    </header>  
       
    <main>
    
    
<ol class="listing">



<?php
$screenshots= array();
$missing = array();
$location = str_replace('/[id]/','',$check_url);

$row = 0;
$markable = 0;
if (($handle = fopen("student-list/".$student_list, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    
$row++;

$html_content = '';

$mmu_id = trim($data[0]); 

if(isset($data[4])&&$data[4]!==""){
    $unusual_status = $data[4];
    }else{$unusual_status='';}
    
    if($unusual_status!=="NS"){
        $site_url = $location.'/'.$mmu_id.'/'.$unusual_status;
    }else{
        $site_url = $location.'/'.$mmu_id.'/index.html';
    }

$located = get_headers($site_url);

// mod_speling returns 301 if it can't find index.html

if(stripos($located[0],"301")>-1){
    $student_url = $location.'/'.$mmu_id.'/Index.html';
    $located = get_headers($site_url);
}

$name = trim(str_replace("\xEF\xBB\xBF", "", $data[1])). ', ' .trim($data[2]);

$name = ucwords(strtolower($name));

if(stripos($located[0],"200 OK")>-1){
    $html_content =  '<li class="listing__item"><a class="listing__link" href="'.$site_url.'" target="_blank"><p class="listing__id">'.$mmu_id.' &middot; '.$data[3].'</p><h3 class="listing__person">'.$name.' <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/></svg></h3>';
    
    $html_content .= '<img class="listing__image" src="create-screenshots/screenshots/'.$mmu_id.'.png" />';
    
    $html_content .=  '</a></li>';   
    array_push($screenshots, $site_url);
    $markable++;
   }else{
       
    $html_content =  '<li class="listing__item --not-found"><a class="listing__link" href="'.$site_url.'" target="_blank"><p class="listing__id">'.$mmu_id.' &middot; '.$data[3].'</p><h3 class="listing__person">'.$name.'</h3></a></li>';
    array_push($missing, $mmu_id.' ' .$name);
   }

echo $html_content;

        

  }
  fclose($handle);
}
    
    ?>
    </ol>
    <p><strong><?php echo $markable; ?>/<?php echo $row; ?></strong> pieces of work to mark</p>
    
    </main>
<footer>
<p>Source file <strong><?php echo $student_list; ?></strong></p>
<p>&copy; <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></p></footer>

<?php 

//print_r($missing);

$comma = '';
if(isset($_GET['screenshots'])&&($_GET['screenshots']==1)){
    
    echo "<textarea>var sites = [";
    
    foreach($screenshots as $s){
        
        echo "'".$s."'";
        if($comma == ""){$comma = ",";}
        echo $comma;
        
    }
    
    echo "];</textarea>";

    
}
?>

</body>
</html>