<?php

include('config.php');


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
    <main>
    <header>
    <h1>Student Work Marking System</h1>  
    </header>  
        <p>We are checking the students in <strong><?php echo $student_list; ?></strong></p>
    <ul class="pages"><?php



    foreach($pages as $p){
        
        echo '<li class="pages__list">'.$p.'</li>';
        
    }
    
    ?>
    </ul>
<p>for</p>
<ol class="listing">
<?php
 $row = 0;
    $oldMMU = 1;
if (($handle = fopen("student-list/".$student_list, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//    $num = count($data);
    $row++;

      
        echo '<li class="listing__item"><span class="listing__number">'.$row.'</span>';
      
      $mmu_id = trim($data[2]); 
      if($oldMMU>$mmu_id){echo '<span class="error">ORDER INCORRECT HERE</span>';}
      $oldMMU = $mmu_id;
      $name = trim($data[0]). ', ' .trim($data[1]);
      $name = ucwords(strtolower($name));
      
      echo '<p><a class="listing__link" href="marksheet.php?for='.$mmu_id.'&amp;name='.urlencode($name).'"><span class="listing__id">'.$mmu_id.'</span><span class="listing__name">'.$name.'</span></a></p>';
            
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