<?php 
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
$conn = new Connection();
$sql = "SELECT student_id FROM students";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["student_id"] . "<br>";
    }
} else {
    echo "0 results";
}
?>
<!DOCTYPE html>
<html>

    
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Home Page</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/custom.css">
    <style>
        form {
            margin: 1rem;
        }
    </style>
</head>

<body>
  <!-- the navbar -->
  <nav class="navbar navbar-toggleable-md navbar-light bg-info">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand text-light" href="#">Home</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item  text-light">
          <a class="nav-link text-light" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" href="#">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" href="#">Sign Up</a>
        </li>
     </ul>
     <form class="form-inline my-2 my-lg-0">
       <input class="form-control mr-sm-2" type="text" placeholder="Search">
       <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
     </form>
    </div>
    </nav>
<br/>
<form method="post" id="loginForm">
  <div class="form-group">
    <label for="ID">ID</label>
    <input type="text" class="form-control" id="ID" aria-describedby="IDHelp" placeholder="Enter ID">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
</form>
<form method="post" id="signupForm" style="display: none;">
    <div class="form-group">
    <label for="ID">ID</label>
    <input type="text" class="form-control" id="ID" aria-describedby="IDHelp" placeholder="Enter ID">
  </div>

    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" aria-describedby="email" placeholder="Enter ID">
  </div>
    <div class="form-group">
    <label for="group">Group</label>
    <select class="form-control" id="group" aria-describedby="group">
        <option value="1" selected>1</option>
        <option value="2" selected>2</option>
        <option value="3" selected>3</option>
        <option value="4" selected>4</option>
        <option value="5" selected>5</option>
        <option value="6" selected>6</option>
        <option value="7" selected>7</option>
        <option value="8" selected>8</option>
        <option value="9" selected>9</option>
        <option value="10" selected>10</option>
        </select>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-primary">Sign up</button>
</form>

</div>

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="js/tether.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="js/ie10-viewport-bug-workaround.js"></script>
<script src="js/Bootstrap_tutorial.js"></script>
</body>

</html>