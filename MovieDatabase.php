<?php
ini_set('display_errors', 'On');
include 'password.php';

$mysqli = new mysqli("localhost", "root", $password, "vid_inventory");

if($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli -> connect_errno . ") " . $mysqli -> connect_error;
} else {
    //echo "Connection worked! <br>";
}?>

<!DOCTYPE html>
<html>
<head>
  <title>Movie Database</title>
  <meta charset="UTF-8">
</head>
<body>
<h1>Movie Database</h1>
<h3>Please enter a movie:</h3>
<form action="http://savvyg.me/Test%20Stuff/test.php" method="post">
    <p>Name:
      <input type="text" name="name">
      Category:
      <input type="text" name="category">
      Length (min):
      <input type="text" name="length">
      <input type="submit" name = "adding" onClick="parent.location='http://savvyg.me/Test%20Stuff/test.php'" value='Add Video'>
    </p>
</form>

<?php
//if (!$mysqli->query("DROP TABLE IF EXISTS inventory") ||
//   !$mysqli->query("CREATE TABLE inventory(id INT auto_increment PRIMARY KEY, 
//    name VARCHAR(255) NOT NULL UNIQUE, category VARCHAR(255), length INT unsigned, rented BIT NOT NULL default 0)")) {
//    echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
//}


if(isset($_POST['adding'])){
    if($_POST['name'] == '' || !is_string($_POST['name'])){
        if($_POST['name'] == ''){
            echo "A name is required to add the video.<br>";
        }else{
            echo "Sorry, invalid input. Please enter a valid title.<br>";
        }
    } else{
        $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    }
    if(!is_string($_POST['category'])){
        echo "Sorry, invalid input. Please enter a valid category.<br>";
    } else{
        $category = mysqli_real_escape_string($mysqli, $_POST['category']);
    }
    if($_POST['length'] != '' && !ctype_digit($_POST['length'])){
        if (($_POST['length']) < 0){
            echo "Sorry, length must be positive.<br>";
        } else {
        echo "Sorry, invalid input. Please enter a number for the length.<br>";
        }
    } else{
        $length = mysqli_real_escape_string($mysqli, $_POST['length']);
    }
    if(isset($name) && isset($category) && isset($length)){
        if (!($stmt = $mysqli->prepare("INSERT INTO inventory(name, category, length) 
        VALUES (?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("ssd", $name, $category, $length)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
    }
}

echo "<table>";
echo "<tbody>";
echo "<tr><td>Id</td><td>Name</td><td>Category</td><td>Length (min)</td><td>Rented</td></tr>";
while ($stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . $out_id . "</td>";
    echo "<td>" . $out_name . "</td>";
    echo "<td>" . $out_category . "</td>";
    echo "<td>" . $out_length . "</td>";
    if($out_rented == 0) {
        $availability = "Available";
    }else{
        $availability = "Checked Out";
    }
    echo "<td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type=\"hidden\" name=\"availability\" value=\"".$out_id."\"/>
    <input type =\"submit\" name=\"checked\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='$availability'></form></td>";
    echo "<td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type=\"hidden\" name=\"delete1\" value=\"".$out_id."\"/>
    <input type =\"submit\" name=\"delete\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='Delete'></form></td>";
    echo "</tr>";
}
echo "<tr><td></td><td></td><td></td><td></td><td></td><td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type =\"submit\" name=\"deleteAll\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='Delete All'></form>";
echo "</tbody>";
echo "</table>";
?>