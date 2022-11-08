<?php

include('config.php');

$student_list = 'iwd-design-marks.csv';

// set ?screenshots=1 to output a list of screenshots for the screenshot.js file
// run node screenshot.js to get the screenshots

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student work list with screenshots in marked order</title>

    
<link rel="stylesheet" type="text/css" href="css/listing.css">
    <style type="text/css">
    .listing__item {padding: 10px;}
    ol.listing li.marking {background: #333; color:white; margin-bottom: 1em; padding: 10px; font-size: 3em; font-weight:900;}
    ol.listing li {flex: 0 0 9%; font-size: 0.7em;}
    </style>
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
$mark_type = '';
if (($handle = fopen("student-list/".$student_list, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    
$row++;

$html_content = '';

$mmu_id = trim($data[1]); 


if($data[4]!==$mark_type){
    echo '<li class="marking">'.$data[4].'</li>';
    $mark_type=$data[4];}

$name = trim(str_replace("\xEF\xBB\xBF", "", $data[2])). ', ' .trim($data[3]);

$name = ucwords(strtolower($name));


    $html_content =  '<li class="listing__item"><p class="listing__id">'.$mmu_id.' &middot; '.$data[0].'</p><h3 class="listing__person">'.$name.' <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/></svg></h3>';
    
    $html_content .= '<img class="listing__image" src="create-screenshots/screenshots/'.$mmu_id.'.png" />';
    
    $html_content .=  '</li>';   
    //array_push($screenshots, $site_url);
    $markable++;


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