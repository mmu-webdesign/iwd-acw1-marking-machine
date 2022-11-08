<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>folder size</title>
    <link href="css/prism.css" rel="stylesheet" />
    <style type="text/css">
        body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; line-height:1.35;
}

main {
    max-width: 1200px;
    margin: 0 auto;

}

p {
    margin: 0;
    padding: 0;
}

ol>li {padding-bottom: 2em;}
span.bad {color:red;}
span {font-weight:bold;}
ul {list-style-type:none; margin:0.3em 0; padding:0;}
ul li { padding: 2px; overflow:hidden;}
ul li span {float:right; display:inline-block;}
ul li span.dimensions {font-weight:normal; min-width: 100px; text-align:right;}
ul li+li {border-top:1px solid #ddd;}
p.who {font-size: 1.5em;}
a {font-weight:bold; color: blue; text-decoration:none;}
        </style>
</head>
<body>
    <main>
<h1>File sizes for each submission</h1>
<ol>
    <?php

// get all folders

$total_bad = 0;
$biggest = 0;

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
                $getlink = str_replace("/Users/derren/Desktop/check/", 'http://www.55060509.webdevmmu.uk/check/', $getlink);

                $checkfile = round($filesize / 1024, 2);
                if ($checkfile > 1024) {$addtext = 'bad';} else { $addtext = '';}
                echo '<li>' . $ff . '<span class="dimensions">' . $filedimensions[0] . ' &#215; ' . $filedimensions[1] . '</span> <span class="filesize ' . $addtext . '">' . $checkfile . 'KB</span><a href="' . $getlink . '">view</a></li>';
            }
        }

        if (is_dir($dir . '/' . $ff)) {
            listFolderFiles($dir . '/' . $ff);
        }

    }

}

function listFolderFilesJS($dir)
{

    $ffs = @scandir($dir);
    $array = array(".js", ".JS");
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

                $getlink = $dir . '/' . $ff;
                $getlink = str_replace('//', '/', $getlink);
                $getlink = str_replace("/Users/derren/Desktop/check/", 'http://www.55060509.webdevmmu.uk/check/', $getlink);

                $checkfile = round($filesize / 1024, 2);
                if ($checkfile > 1024) {$addtext = 'bad';} else { $addtext = '';}
                echo '<li>' . $ff . ' <span class="filesize ' . $addtext . '">' . $checkfile . 'KB</span><a href="' . $getlink . '">view</a></li>';
            }
        }

        if (is_dir($dir . '/' . $ff)) {
            listFolderFilesJS($dir . '/' . $ff);
        }

    }

}

function folderSize($dir)
{
    $size = 0;
    foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : folderSize($each);
    }
    return $size;
}

$path = '/Users/derren/Desktop/check/';

//$folders = glob($path.'/*/');

if (($handle = fopen("/Applications/MAMP/htdocs/marking-app-acw2/student-list/iwdd2.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {

        $f = $data[2];
        $check_url = '/Users/derren/Desktop/check/' . $f;
        $name = $data[1] . ' ' . $data[0];
        $name = "Student Name"; // remove
        $f = '12345678'; // remove
        echo '<li>';

        echo '<p class="who">' . $name . ' &middot; ' . $f . '</p>';

        $folder_size = round(folderSize($check_url) / 1024, 2);

        echo '<ul>';
        listFolderFiles($check_url);
        echo '</ul>';

        echo '<ul>';
        listFolderFilesJS($check_url);
        echo '</ul>';

        if ($folder_size > 1024) {
            echo 'Folder size <span class="bad">' . $folder_size . 'KB</span>';
            $total_bad++;
        } else {
            echo 'Folder size <span class="ok">' . $folder_size . 'KB</span>';
        }
        if ($folder_size > $biggest) {$biggest = $folder_size;}

        echo '</li>';
    }
}
?>
    </ol>
    <p>Total over 1MB <strong><?php echo $total_bad; ?></strong> &middot; Worst <strong><?php echo $biggest; ?>KB</strong></p>
</main>
</body>
</html>