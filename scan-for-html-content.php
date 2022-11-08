<?php

include('config.php');

function slug($content){
    return str_replace(' ', '_', strtolower($content));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scanning code: counting HTML content</title>

    
<link rel="stylesheet" type="text/css" href="css/mark.css">
    
</head>
<body>
    <main>
    <header>
    <h1>Scanning for HTML content</h1>  
    </header>  
        <p>We are checking the students in <strong><?php echo $student_list; ?></strong></p>
    
<ol class="listing">



<?php
$total_sites = 0;
$location = str_replace('/[id]/','',$check_url);


 $row = 0;
    $oldMMU = 1;
if (($handle = fopen("student-list/".$student_list, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//    $num = count($data);
//print_r($data);
    $row++;


echo '<li class="listing__item">';
      
$mmu_id = trim($data[0]); 

$group_number = trim($data[3]);

// unusual status can either be NS or the weird path to the code they've submitted

if(isset($data[4])&&$data[4]!==""){
$unusual_status = $data[4];
}else{$unusual_status='';}

if($unusual_status!=="NS"){
    $site_url = $location.'/'.$mmu_id.'/'.$unusual_status;
}else{
    $site_url = $location.'/'.$mmu_id.'/index.html';
}

//echo $site_url;

$there_is_a_site=0;
$testing = @file($site_url);


if(count($testing)>1){
    $there_is_a_site = 1;
    $page = implode('', $testing);
}


$checkable='no';

$name = trim(str_replace("\xEF\xBB\xBF", "", $data[1])). ', ' .trim($data[2]);
$name = ucwords(strtolower($name));
      
if($there_is_a_site==1){
$total_sites++;
    
      echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      
      //'.$name.'
      echo '<p><span class="listing__id">'.$mmu_id.' <span class="'.slug($group_number).'">'.$group_number.'</span></span><span class="listing__name">'.$name.'</span>
      </p>';
      
      $word_count = str_word_count(strip_tags(strtolower($page)));
$total_expected = 800;

$results = 'We found about '.$word_count.' words <strong>'.round(($word_count/$total_expected)*100,1).'% of the 800 word brief</strong>';
echo $results;
}

else{
    echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      
      echo '<p><span class="listing__id">'.$mmu_id.' <span class="'.slug($group_number).'">'.$group_number.'</span></span><span class="listing__name">'.$name.'</span>';
      
      if($unusual_status=="NS"){echo '<span class="listing__group"><b class="n-s">No Submission</b></span>';}
      
      
      

}
      echo '</li>';
        

  }
  fclose($handle);
}
    
    ?>
    </ol>
    <p><strong><?php echo $total_sites; ?> markable submissions</strong> from <?php echo $row; ?> students</p>
<footer>&copy; <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></footer>
</main>

</body>
</html>