<?php

include('config.php');

$nothing = array();

function slug($content){
    return str_replace(' ', '_', strtolower($content));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marking student work</title>

    
<link rel="stylesheet" type="text/css" href="css/mark.css">
    
</head>
<body>
    <main>
    <header>
    <h1>Student Work Marking System</h1>  
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

$there_is_a_site=1;
$testing = @file($site_url);

if(@count($testing)>=1){
    $there_is_a_site = 1;
}



//print_r($testing);

$checkable='no';

$name = trim(str_replace("\xEF\xBB\xBF", "", $data[1])). ', ' .trim($data[2]);
      $name = ucwords(strtolower($name));
      
      //echo $located[0];
if($there_is_a_site==1){
$total_sites ++;
$link = '<a class="listing__link view__entire-site" target="_blank" href="'.$site_url.'">View site</a>';

    $link .= '<a class="listing__link view__site" target="_blank" href="preview.php?url='.$site_url.'&amp;name='.urlencode($name).'">Site &amp; source</a>';
$checkable='yes';



    //   if($oldMMU>$mmu_id){echo '<span class="error ">ORDER INCORRECT HERE</span></li><li class="listing__item">';}

      echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      
      //'.$name.'
      echo '<p><span class="listing__id">'.$mmu_id.' <span class="'.slug($group_number).'">'.$group_number.'</span></span><span class="listing__name">'.$name.'</span>
      <span class="listing__group">'.$link.'
      <a class="listing__link" target="_blank" href="marksheet.php?for='.$mmu_id.'&amp;name='.urlencode($name).'&amp;url='.$site_url.'&amp;group='.urlencode($data[3]).'&amp;checkable='.$checkable.'">Automated marking</a></span></p>';
}

else{
    echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      
      echo '<p><span class="listing__id">'.$mmu_id.' <span class="'.slug($group_number).'">'.$group_number.'</span></span><span class="listing__name">'.$name.'</span>';
      
      if($unusual_status=="NS"||$unusual_status=="EF"||$unusual_status=="MESS"){
          if($unusual_status=="NS"){echo '<span class="listing__group"><b class="n-s">No Submission</b></span>';}
        else{
            if($unusual_status=="EF"){echo '<span class="listing__group"><b class="e-f">Exceptional Factors</b></span>';}
            else{
             
                    if($unusual_status=="MESS"){echo '<span class="listing__group"><b class="mess">Submission file unreadable</b></span>';}
            }
            
        }}else{array_push($nothing, $mmu_id.' '.$name.' '.$group_number);}
      
      
      

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
<?php sort($nothing); 
if(count($nothing)>0){
$j=1;
echo '<h3>Missing without NS/EFs</h3>';
echo '<ul class="listing">';
foreach($nothing as $n){
    echo'<li class="listing__item"><span class="listing__number">'.$j.'</span>'.$n.'</li>';
    $j++;
}
echo '</ul>';
}
?>
</body>
</html>