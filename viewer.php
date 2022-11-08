<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page viewer</title>
    <link href="css/prism.css" rel="stylesheet" />
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-size:90%;
        }
        </style>
</head>
<body>
    
<?php 

$url = $_GET['source'];
$type = $_GET['type'];

if($type=='html'){

$code = file($url);
$code = implode('',$code);
$code = str_replace('<', '&lt;', $code);
echo '<pre><code class="language-html">'.$code.'</code></pre>';

} 

if($type=='css'){
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

foreach($check_css_link as $c){

    if(!strstr($c, 'google')
    &&!strstr($c, 'font-awesome')
    &&!strstr($c, 'fontawesome')
    &&!strstr($c, 'favicon')
    &&!strstr($c, 'ico')
    &&!strstr($c, 'png')
    ){
$url = str_replace('index.html', '', $url);
$code = file($url.$c);
$code = implode('', $code); 
        $css_files .= '/* --------- CSS FILE: '.$c.' ---------- */'."\r\n\r\n".$code;
    }
    
}





    echo '<pre><code class="language-css">'.$css_files.'</code></pre>';
}

?>
<script src="js/prism.js"></script>
</body>
</html>