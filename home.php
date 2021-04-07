<?php
require_once("config/db.php");

if (!(isset($_GET['pID']) && isset($_GET['pName']))) {
  header('location: index.php');
} else {
  if (ProjectIDNotExist($_GET['pID'])) {
    header('location: index.php');
  }
  if (ProjectNameNotExist($_GET['pName'])) {
    header('location: index.php');
  }
}
$projectID = $_GET['pID'];
$projectName = $_GET['pName'];
$errors = array();
$messages = array();
$taskName = "";
if (isset($_POST['deleteTask'])) {
  $taskID = $_POST['deleteTask'];
  deleteTask($taskID);
}
if (isset($_POST['taskName']) && isset($_POST['startDate']) && isset($_POST['finishDate'])) {
  $taskName = $_POST['taskName'];
  $startDate = date('Y-m-d', strtotime($_POST['startDate']));
  $finishDate = date('Y-m-d', strtotime($_POST['finishDate']));
  if (strtotime($startDate) < strtotime($finishDate)) {

    if (taskNameNotExist($taskName)) {
      insertTask($taskName, $startDate, $finishDate, $projectID);
    } else {
      $errors['taskName'] = "Task name already exists";
    }
  } else {
    $errors['date'] = 'Start date must be earlier than finish date';
  }
}
if (isset($_POST['editedName']) && isset($_POST['editedStart']) && isset($_POST['editedFinish']) && isset($_POST['editID'])) {
  $taskID = $_POST['editID'];
  $name = $_POST['editedName'];
  $start = date('Y-m-d', strtotime($_POST['editedStart']));
  $finish = date('Y-m-d', strtotime($_POST['editedFinish']));
  if (strtotime($start) < strtotime($finish)) {

    if (taskNameNotExistEdit($name, $taskID)) {
      editTask($name, $start, $finish, $taskID);
    } else {
      $errors['taskName'] = "Task name already exists";
    }
  } else {
    $errors['date'] = 'Start date must be earlier than finish date';
  }
}
if (isset($_POST['allocTaskID']) && isset($_POST['allocatedRes'])) {
  $allocTaskID = $_POST['allocTaskID'];
  $allocatedRes = $_POST['allocatedRes'];
  if(resAllocExist($allocTaskID)){
    deleteResAlloc($allocTaskID);
  }
  allocateResource($allocatedRes, $allocTaskID);
}
function allocateResource($allocatedResName, $allocTaskID)
{
  global $conn, $errors, $messages;
  $resID = getResID($allocatedResName);
  if ($resID != -1) {
    $resultQuery = mysqli_query($conn, "INSERT INTO resalloc(resource_id, task_id, resource_name) VALUES('$resID','$allocTaskID','$allocatedResName')");
    if ($resultQuery) {
      $messages['taskAlloc'] = "Resource with name '". $allocatedResName . "' has been allocated to task with ID: " . $allocTaskID;
    } else {
      $errors['taskAlloc'] = "Error allocating resource";
    }
  } else {
    $errors['resID'] = "Resource with name '" . $allocatedResName . "' can't be found";
  }
}
function resAllocExist($allocTaskID)
{
  global $conn;
  $resultQuery = mysqli_query($conn, "SELECT * FROM resalloc WHERE task_id='$allocTaskID'");
  $num = mysqli_num_rows($resultQuery);
  if ($num != 0) {
    return true;
  }
  return false;
}
function deleteResAlloc($allocTaskID)
{
  global $conn;
  $resultQuery = mysqli_query($conn, "DELETE FROM resalloc WHERE task_id='$allocTaskID'");
  $resultAff = mysqli_affected_rows($conn);
  if ($resultAff > 0) {
    return true;
  }
  return false;
}
function getResID($allocatedResName)
{
  global $conn, $errors, $messages;
  $resID = -1;
  $resultQuery = mysqli_query($conn, "SELECT * FROM resource WHERE name='$allocatedResName'");
  $num = mysqli_num_rows($resultQuery);
  if ($num != 0) {
    $row = mysqli_fetch_assoc($resultQuery);
    $resID = $row['id'];
    return $resID;
  }
  return $resID;
}
function deleteTask($taskId)
{
  global $conn, $messages, $errors;
  $resultQuery = mysqli_query($conn, "DELETE FROM task WHERE id='$taskId'");
  $resultAff = mysqli_affected_rows($conn);
  if ($resultQuery == true) {
    if ($resultAff > 0) {
      $messages['taskDelete'] = 'Task with id ' . $taskId . ' has been deleted';
    } else {
      $errors['taskAffect'] = 'No rows are affected' . $taskId;
    }
  } else {
    $errors['taskDelete'] = 'Unable to delete task' . $taskId;
  }
}
function editTask($name, $start, $finish, $taskID)
{
  global $conn, $errors, $messages;

  $resultTask = mysqli_query($conn, "UPDATE task SET name='$name', start='$start', finish='$finish'  WHERE id='$taskID'");
  if ($resultTask == true) {
    $messages['taskInsert'] = 'Task edited successfully!';
  } else {
    $errors['taskInsert'] = 'Unable to edit the task';
  }
}

function insertTask($name, $start, $finish, $projectID)
{
  global $conn, $errors, $messages;

  $resultTask = mysqli_query($conn, "INSERT INTO task(name, start, finish, project_id) VALUES('$name','$start','$finish',$projectID)");
  if ($resultTask == true) {
    $messages['taskInsert'] = 'Task added successfully!';
  } else {
    $errors['taskInsert'] = 'Unable to add the task';
  }
}
function taskNameNotExist($name)
{
  global $conn, $projectID;
  $result = mysqli_query($conn, "SELECT * FROM task WHERE name='$name' AND project_id='$projectID' LIMIT 1");
  $nameCount = mysqli_num_rows($result);
  if ($nameCount == 0) {
    return true;
  } else {
    return false;
  }
}
function taskNameNotExistEdit($name, $id)
{
  global $conn, $projectID;
  $result = mysqli_query($conn, "SELECT * FROM task WHERE name='$name' AND project_id='$projectID' AND id<>'$id' LIMIT 1");
  $nameCount = mysqli_num_rows($result);
  if ($nameCount == 0) {
    return true;
  } else {
    return false;
  }
}
function ProjectNameNotExist($name)
{
  global $conn;
  $nameQuery = mysqli_query($conn, "SELECT * FROM project WHERE name='$name' LIMIT 1");
  $nameCount = mysqli_num_rows($nameQuery);
  if ($nameCount == 0) {
    return true;
  } else {
    return false;
  }
}

function ProjectIDNotExist($id)
{
  global $conn;
  $nameQuery = mysqli_query($conn, "SELECT * FROM project WHERE id='$id' LIMIT 1");
  $nameCount = mysqli_num_rows($nameQuery);
  if ($nameCount == 0) {
    return true;
  } else {
    return false;
  }
}
function calculateDuration($date1, $date2)
{
  $diff = abs(strtotime($date2) - strtotime($date1));

  $years = floor($diff / (365 * 60 * 60 * 24));
  $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
  // $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
  $days = floor(($diff) / (60 * 60 * 24));
  return $days;
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
function getTaskRes($taskID)
{
  global $conn;
  $resultQuery = mysqli_query($conn, "SELECT * FROM resalloc WHERE task_id='$taskID'");
  $rowsCount = mysqli_num_rows($resultQuery);
  if ($rowsCount != 0) {
    $row = mysqli_fetch_assoc($resultQuery);
    return $row['resource_name'];
  }
  return '-';
}
function printResNames()
{
  global $conn, $projectID, $projectName;
  $sqlRes = "SELECT * FROM resource WHERE project_id='$projectID'";
  $result = mysqli_query($conn, $sqlRes);
  $num = mysqli_num_rows($result);
  if ($num != 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<option>" . $row['name'] . "</option>";
    }
    return true;
  } else {
    return false;
  }
}
function printTasks()
{
  global $conn, $projectID, $projectName;
  $sqlTask = "SELECT * FROM task WHERE project_id='$projectID'";
  $result = mysqli_query($conn, $sqlTask);
  $num = mysqli_num_rows($result);
  if ($num != 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $duration = calculateDuration($row['start'], $row['finish']);
      $id = $row['id'];
      echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $duration . " days</td>
            <td>" . $row['start'] . "</td>
            <td>" . $row['finish'] . "</td>
            <td>" . getTaskRes($id) . "</td>
            <td>
            <button id='btn-edit' data-toggle='modal' data-target='#myModal' class='btn btn-light btn-sm' type='submit' name=''><i class='fas fa-edit'></i> Edit</button>
            <button id='btn-alloc' data-toggle='modal' data-target='#myModal1' class='btn btn-info btn-sm' type='submit' name=''><i class='fas fa-user'></i> Allocate</button>
            <form class='d-inline' action='home.php?pID=" . $projectID . "&pName=" . $projectName . "' method='post'>
            <button class='btn btn-delete btn-danger btn-sm' type='submit' name='deleteTask' value='$id'><i class='fas fa-trash'></i> Delete</button></form></td>
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
  <link rel="stylesheet" href="styles.css">
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

  <title>Tasks | Project Professional</title>
</head>

<body>
  <!-- navbar-->
  <?php require_once 'nav.php';
  printNavbar('home', $projectID, $projectName); ?>

  <div class="container-fluid up-footer">
    <h1 class="text-center">Tasks</h1>
    <div class="form-add col-sm-10 offset-1">
      <?php
      printErrors();
      printMessages();
      ?>
      <form class="form-inline" action="home.php?<?php echo "pID=" . $projectID . "&pName=" . $projectName; ?>" method="post">
        <div class="form-group">
          <label class="font-weight-bold">Task name: </label>
          <input type="text" class="form-control" id="text" placeholder="Task name" name="taskName" required>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Start date: </label>
          <input type="date" class="form-control" placeholder="Start date" name="startDate" required>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Finish date: </label>
          <input type="date" class="form-control" placeholder="Finish date" name="finishDate" required>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Task</button>
      </form>
    </div>
    <hr>
    <div class="table-project col-sm-10 offset-1">
      <table id="example" class="table table-striped table-bordered table-project" style="width:100%">
        <thead>
          <tr>
            <th>Task ID</th>
            <th>Task Name</th>
            <th>Duration</th>
            <th>Start</th>
            <th>Finish</th>
            <th>Resource name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php
          printTasks();
          ?>


        </tbody>
        <tfoot>
          <tr>
            <th>Task ID</th>
            <th>Task Name</th>
            <th>Duration</th>
            <th>Start</th>
            <th>Finsish</th>
            <th>Resource name</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>

  <!-- The Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="editTitle"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form class="form" action="home.php?<?php echo "pID=" . $projectID . "&pName=" . $projectName; ?>" method="post">
            <div class="form-group">
              <label class="font-weight-bold">Task ID: </label>
              <input type="text" class="form-control" id="editID" name='editID' readonly>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Task name: </label>
              <input type="text" class="form-control" id="editName" placeholder="Task name" name="editedName" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Start date: </label>
              <input type="date" class="form-control" id="editStart" placeholder="Start date" name="editedStart" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Finish date: </label>
              <input type="date" class="form-control" id="editFinish" placeholder="Finish date" name="editedFinish" required>
            </div>
            <hr>
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

          </form>
        </div>

        <!-- Modal footer
        <div class="modal-footer">
        
        </div> -->

      </div>
    </div>
  </div>

  <!-- The Modal -->
  <div class="modal fade" id="myModal1">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="allocTitle"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form class="form" action="home.php?<?php echo "pID=" . $projectID . "&pName=" . $projectName; ?>" method="post">
            <div class="form-group">
              <label class="font-weight-bold">Task ID: </label>
              <input type="text" class="form-control" id="taskAllocID" name='allocTaskID' readonly>
            </div>
            <div class="form-group">
              <label for="sel1">Resource name:</label>
              <select class="form-control" name="allocatedRes" id="allocRes" required>
                <?php printResNames(); ?>
              </select>
            </div>
            <hr>
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

          </form>
        </div>

        <!-- Modal footer
        <div class="modal-footer">
        
        </div> -->

      </div>
    </div>
  </div>


  <!-- Footer -->
  <?php printFooter($projectID, $projectName); ?>

  <script>
    $(document).ready(function() {
      $('#example').DataTable();
    });
    $('body').on('click', '.btn-delete', function() {
      var id = $(this).parents('tr').find('td:eq(0)').html();
      console.log(id);
    });

    $('body').on('click', '#btn-edit', function() {

      var id = $(this).parents('tr').find('td:eq(0)').html();
      var name = $(this).parents('tr').find('td:eq(1)').html();
      var start = $(this).parents('tr').find('td:eq(3)').html();
      var finish = $(this).parents('tr').find('td:eq(4)').html();

      console.log(id);
      console.log(name);
      console.log(start);
      console.log(finish);
      document.getElementById("editTitle").innerHTML = "Edit task: " + name;
      $('#editID').val(id);
      $('#editName').val(name);
      $('#editStart').val(start);
      $('#editFinish').val(finish);

    });
    $('body').on('click', '#btn-alloc', function() {

      var id = $(this).parents('tr').find('td:eq(0)').html();
      var name = $(this).parents('tr').find('td:eq(1)').html();

      console.log(id);
      console.log(name);

      document.getElementById("allocTitle").innerHTML = "Allocate task: " + name;
      $('#taskAllocID').val(id);
      $('#allocName').val(name);

    });
  </script>


</body>

</html>