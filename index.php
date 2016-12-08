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
    <p>We are checking</p>
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
if (($handle = fopen("student-list/list.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//    $num = count($data);
    $row++;

        echo '<li class="listing__item">';
      
      $mmu_id = trim($data[1]);
      $name = trim($data[0]);
      $name = ucwords(strtolower($name));
      
      echo '<p><a class="listing__link" href="run-check.php?for='.$mmu_id.'&amp;name='.urlencode($name).'"><span class="listing__id">'.$mmu_id.'</span><span class="listing__name">'.$name.'</span></a></p>';
            
      echo '</li>';
        

  }
  fclose($handle);
}
    
    ?>
    </ol>
<footer>&copy; <?php echo @date('Y'); ?> &middot; <a href="mailto:d.j.wilson@mmu.ac.uk">d.j.wilson@mmu.ac.uk</a></footer>
</main>

</body>
</html>