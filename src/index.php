<?php
function close($con) {
  $thread = $con->thread_id;
  $con->close();
  $con->kill($thread);
}

function create_db($con) {
  $sql = 'CREATE DATABASE IF NOT EXISTS forum;';
  $con->query($sql);

  return $con;
}

function create_table($con) {
  $query = "SELECT id FROM messages";
  $result = $con->query($query);

  if(empty($result)) {
    $query = "CREATE TABLE messages (
              id int not null AUTO_INCREMENT primary key,
              user varchar(25) NOT NULL,
              message text NOT NULL,
              date timestamp);";
    $result = $con->query($query);

    if ($result == false) {
      echo 'Error: Could not create table messages';
    }
  }
}

function connect($host, $user, $passwd, $table) {
  if ($table == '') {
    $con = @new mysqli($host, $user, $passwd);

  } else {
    $con = @new mysqli($host, $user, $passwd, $table);
  }

  if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  return $con;
}

/************************************
 ** CONNECT TO MYSQL AND CREATE DB **
 ***********************************/
$mysqli = connect("mysql", "root", "admin", "");
create_db($mysqli);
close($mysqli);

/************************************
 ** CONNECT TO DB AND CREATE TABLE **
 ***********************************/
$mysqli = connect("mysql", "root", "admin", "forum");
create_table($mysqli);

if (isset($_GET['message'])) {

    $user=$mysqli->real_escape_string($_GET['user']);
    $message=$mysqli->real_escape_string($_GET['message']);
    $date=date('Y-m-d H:i:s');

    $sql="INSERT INTO messages(id, user, message, date) VALUES(0,'$user','$message','$date')";
    $mysqli->query($sql);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHP and MySQL Docker demo</title>
</head>
<body>
<h2>Test Message Board</h2>
<?php
$sql = "SELECT * FROM messages";
$result = $mysqli->query($sql);

if ($result == false) {
  echo 'false';
}

while($row = $result->fetch_assoc()) {
  echo $row['user'].',  '.$row['date'].' <br />';
  echo $row['message'].'<br />';
  echo '************************<br />';
}
?>

  <form method="get" action="index.php">
  <p>User:
    <label for="user"></label>
    <input type="text" name="user" id="user" />
    <br />
  </p>
  <p>Message: <br />
    <label for="message"></label>
    <textarea name="message" id="message" cols="45" rows="5"></textarea>
  </p>
  <p>
    <input type="submit" name="submit" id="submit" value="Post message" />
  </p>
  </form>

</body>
</html>
