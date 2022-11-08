<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_GET['name']; ?></title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        html {
            box-sizing: border-box;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        iframe {
            height: 100vh;
            width: 33%;
            margin: 0;
            padding: 0;
            border: 0;
            resize: both;
        }
    </style>
</head>
<?php $url = $_GET['url']; ?>
<body>
    <iframe src="<?php echo $url; ?>">
    </iframe>
    <iframe src="viewer.php?type=html&amp;source=<?php echo $url; ?>"></iframe>
    <iframe src="viewer.php?type=css&amp;source=<?php echo $url; ?>"></iframe>

</body>

</html>