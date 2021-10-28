<?php
require_once("config/db.php");

$errors = array();
$messages = array();
// if(isset($_POST['btn-enter'])){
//     $pName=$_POST['btn-enter'];
//     $_SESSION['pName']=$pName;
//     echo $_SESSION['pName'];

//     header('location:home.php');
// }
if (isset($_POST['deleteProject'])) {
    deleteProject($_POST['deleteProject']);
}
if (isset($_POST['projectName'])) {

    $projectName = $_POST['projectName'];

    if (checkNameAvailable($projectName)) {
        insertProject($projectName);
    } else {
        $errors['projectName'] = "Project name already exists";
    }
}
function deleteProject($id)
{
    global $conn;
    global $messages;
    global $errors;
    $deleteQuery = "DELETE FROM project WHERE id='$id'";
    $resultQuery = mysqli_query($conn, $deleteQuery);
    $resultAff = mysqli_affected_rows($conn);
    if ($resultQuery == true) {
        if ($resultAff > 0) {
            $messages['taskDelete'] = 'Project with id ' . $id . ' has been deleted';
        } else {
            $errors['taskAffect'] = 'No rows are affected' . $id;
        }
    } else {
        $errors['taskDelete'] = 'Unable to delete project' . $id;
    }
}
function insertProject($name)
{
    global $conn;
    global $errors;
    global $messages;

    $taskQuery = "INSERT INTO project(name) VALUES('$name')";
    $resultTask = mysqli_query($conn, $taskQuery);
    if ($resultTask == true) {
        $messages['projectInsert'] = 'Project added successfully!';
    } else {
        $errors['projectInsert'] = 'Unable to add the Project';
    }
}
function checkNameAvailable($name)
{
    global $conn;
    $nameQuery = "SELECT * FROM project WHERE name=? LIMIT 1";
    $stmt = $conn->prepare($nameQuery);
    $stmt->bind_param('s', $name); //s- string , add email instead of ?
    $stmt->execute();
    $result = $stmt->get_result();
    $nameCount = $result->num_rows;
    $stmt->close();
    if ($nameCount == 0) {
        return true;
    } else {
        return false;
    }
}
function printErrors()
{
  global $errors;
  if (count($errors) > 0) {
    echo '<div class="alert alert-danger">';
    foreach ($errors as $error) {
      echo '<li>' . $error . '</li>';
    }
    echo '</div>';
  }
}
function printMessages()
{
  global $messages;
  if (count($messages) > 0) {
    echo '<div class="alert alert-success">';
    foreach ($messages as $message) {
      echo '<li>' . $message . '</li>';
    }
    echo '</div>';
  }
}
function printProjects()
{
    global $conn;
    $sqlProject = "SELECT * FROM project";
    $result = mysqli_query($conn, $sqlProject);
    $num = mysqli_num_rows($result);
    if ($num != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            echo "<tr>
              <td>" . $row['id'] . "</td>
              <td>" . $row['name'] . "</td>
              <td>
              <a class='btn btn-sm btn-success' href='home.php?pID=" . $row['id'] . "&pName=" . $row['name'] . "'><i class='fas fa-door-open'></i> Enter</a>
              <form class='d-inline' action='index.php' method='post'>
              <button class='btn btn-delete btn-danger btn-sm' type='submit' name='deleteProject' value='" . $row['id'] . "'><i class='fas fa-trash'></i> Delete</button></form></td>
              </td>
            </tr>";
        }
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- font awesome icons-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">



    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <title>Project Professional</title>
</head>

<body class="project-body text-center">
    <div class="container">

        <div class="project-welcome">
            <h1 class="font-weight-bold display-3">Welcome to <br>The Project Professional</h1>
            <p>Create or select a Project from the list down below to manage tasks and resources and much more!</p>
        </div>
        <div class="form-add col-sm-8 offset-2">
            <?php printErrors();
                  printMessages();
            ?>
            <form class="form-inline" action="index.php" method="post">
                <div class="form-group">
                    <label class="font-weight-bold">Enter Project name: </label>
                    <input type="text" class="form-control" id="text" placeholder="Project name" name="projectName" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Create Project</button>
            </form>
        </div>
        <br>
        <table class="table col-sm-8 offset-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(!printProjects()){
                    echo "<div>No projects added in the database</div>";
                }
                ?>
            </tbody>
        </table>

    </div>





</body>

</html>