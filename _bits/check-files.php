<?php

include('config.php');

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

      
        echo '<li class="listing__item">';
      
      $mmu_id = trim($data[2]); 
      if($oldMMU>$mmu_id){echo '<span class="error ">ORDER INCORRECT HERE</span></li><li class="listing__item">';}

      echo '<span class="listing__number">'.$row.'</span>';
      $oldMMU = $mmu_id;
      $name = trim($data[0]). ', ' .trim($data[1]);
      $name = ucwords(strtolower($name));
      
      echo '<p><span class="listing__id">'.$mmu_id.'</span><span class="listing__name">'.$name.'</span>
      </p>';

      $check = array(
          'index.html',
          'pages/stocklist.html',
          'pages/contact.html',
          'img/cheese-stacks.jpg',
          'css/style.css'
      );
     
     $to_check_url =  str_replace('[id]/','',$check_url.$mmu_id.'/');
     //echo $to_check_url;
     
     $output = '';

     $exists = 0;
     foreach($check as $c){


    
       $test = is_url_exist($to_check_url.$c);
       if($test==1){
$output .=$c.' &#10004; '; 
$exists = $exists+1;
       }else{
        $output .=$c.' &#215; '; 
       }

     }


      echo '<p class="content result-grade-'.$exists.'">'.$output.'</p>';
            
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