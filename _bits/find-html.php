<?php

include('config.php');

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
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

$location = str_replace('/[id]/','',$check_url);


 $row = 0;
    $oldMMU = 1;
if (($handle = fopen("student-list/".$student_list, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//    $num = count($data);
//print_r($data);
    $row++;

    $checkclass= file_exists('/Users/derren/Desktop/check/'.$data[2]);
      

    


        echo '<li class="listing__item is_'.$checkclass.'">';
      
      $mmu_id = trim($data[2]); 

//echo '/Users/derren/Desktop/check/'.$mmu_id.'/*';

      foreach (rglob("/Users/derren/Desktop/check/".$mmu_id."/*.html") as $filename) {
        echo "$filename size " . filesize($filename) . "\n";
    }


      if($oldMMU>$mmu_id){echo '<span class="error ">ORDER INCORRECT HERE</span></li><li class="listing__item">';}

      echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      $name = trim(str_replace("&#65279;", "", $data[0])). ', ' .trim($data[1]);
      $name = ucwords(strtolower($name));
      
      echo '<p><span class="listing__id">'.$mmu_id.'</span><span class="listing__name">'.$name.'</span>
      <span class="listing__group">
      <a class="listing__link" href="'.$location.'/'.$mmu_id.'">View site</a><a class="listing__link" href="marksheet.php?for='.$mmu_id.'&amp;name='.urlencode($name).'">Automated marking</a></span></p>';
            
      echo '</li>';
        

  }
  fclose($handle);
}
    
    ?>
    </ol>
    <p><strong><?php echo $row; ?></strong> pieces of work to mark</p>
<footer>&copy; <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></footer>
</main>

</body>
</html>