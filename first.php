<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb">

<head>
    <title>A first PHP example</title>
</head>

<body>
    <h1>Welcome <?php echo $_POST['userName'] ?></h1>
    <p>
        I see that you are using a browser type:<br />
        &nbsp; &nbsp; <?php echo $_SERVER['HTTP_USER_AGENT'] ?><br />
        from IP address: <?php echo $_SERVER['REMOTE_ADDR'] ?>
        <?php
        if (isset($_POST['button'])) echo 'You pressed button ' . $_POST['button'];
        print_r($_REQUEST)
        ?>
    </p>
</body>
</html>