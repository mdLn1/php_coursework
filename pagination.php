<?php require "classes/oldConnect.php"; ?>
<html>

<head>
    <title>Pagination</title>
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>

<body>
    <?php

    if (isset($_GET['pageno'])) {
        $pageno = $_GET['pageno'];
    } else {
        $pageno = 1;
    }
    $no_of_records_per_page = 5;
    $offset = ($pageno - 1) * $no_of_records_per_page;

    $total_pages_sql = "SELECT COUNT(*) FROM students WHERE ID REGEXP '^000' ORDER BY ID";
    $result = $mysqli->query($total_pages_sql);
    $total_rows = $result->fetch_array()[0];
    if ($total_rows > 0) {
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $sql = "SELECT * FROM students WHERE ID REGEXP '^000' ORDER BY ID LIMIT $offset, $no_of_records_per_page";
        $res_data = $mysqli->query($sql);
        while ($row = $res_data->fetch_array()) {
            echo "<h1>" . $row["ID"] . "</h1>";
        }
    }

    $mysqli->close();

    if ($total_rows < 1) { ?>
        <h1>0 results found</h1>
    <?php } else { ?>
        <ul class="pagination">
            <li><a href="?pageno=1">First</a></li>
            <li class="<?php if ($pageno <= 1) {
                                echo 'disabled';
                            } ?>">
                <a href="<?php if ($pageno <= 1) {
                                    echo '#';
                                } else {
                                    echo "?pageno=" . ($pageno - 1);
                                } ?>">Prev</a>
            </li>
            <li class="<?php if ($pageno >= $total_pages) {
                                echo 'disabled';
                            } ?>">
                <a href="<?php if ($pageno >= $total_pages) {
                                    echo '#';
                                } else {
                                    echo "?pageno=" . ($pageno + 1);
                                } ?>">Next</a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
        </ul>
    <?php } ?>
</body>

</html>