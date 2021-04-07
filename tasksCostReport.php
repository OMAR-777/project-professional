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
function getResAllocated($taskID)
{
    global $conn;
    $resultQuery = mysqli_query($conn, "SELECT * FROM resalloc WHERE task_id='$taskID'");
    $numRows = mysqli_num_rows($resultQuery);
    if ($numRows != 0) {
        $row = mysqli_fetch_assoc($resultQuery);
        return $row;
    }
    return null;
}

function getRes($resID){
    global $conn;
    $resultQuery = mysqli_query($conn, "SELECT * FROM resource WHERE id='$resID'");
    $numRows = mysqli_num_rows($resultQuery);
    if ($numRows != 0) {
        $row = mysqli_fetch_assoc($resultQuery);
        return $row;
    }
    return null;
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
function printTasksReport()
{

    global $conn, $projectID, $projectName;
    $totalDuration = 0;
    $nAlloc=0;
    $sqlTask = "SELECT * FROM task WHERE project_id='$projectID'";
    $result = mysqli_query($conn, $sqlTask);
    $num = mysqli_num_rows($result);
    if ($num != 0) {
        echo "<tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $resAllocRow = getResAllocated($row['id']);
            if ($resAllocRow != null) {
                $resRow=getRes($resAllocRow['resource_id']);
                $duration = calculateDuration($row['start'], $row['finish']);
                $totalCost=8*$duration*$resRow['rate']*($resRow['max']/100);
                echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $duration . " days</td>
            <td>" . $row['start'] . "</td>
            <td>" . $row['finish'] . "</td>
            <td>" .$resAllocRow['resource_name']. "</td>
            <td>" .$totalCost. "</td>
          </tr>";
                $totalDuration += $duration;
                $nAlloc++;
            }
        }
        echo "        
        </tbody>
        <tfoot>
        <tr>
        <th colspan='6'>Number of allocated resources:</th>
        <th>" . $nAlloc . "</th>
    </tr>
    <tr>
        <th colspan='6'>Total duration for all allocated resources:</th>
        <th>" . $totalDuration . "</th>
    </tr>
        </tfoot>";
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

    <title>Tasks Cost Report | Project Professional</title>
</head>

<body>
    <!-- navbar-->
    <?php require_once 'nav.php';
    printNavbar('reptab', $projectID, $projectName); ?>

    <div class="container-fluid up-footer">
        <h1 class="text-center">Tasks Cost Report</h1>
        <div class="form-add col-sm-10 offset-1">

            <div class="table-project col-sm-10 offset-1">
            <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> print this page</button>
                <table id="example" class="table table-striped table-bordered table-project" style="width:100%">
                    <thead>
                        <tr>
                            <th>Task ID</th>
                            <th>Task Name</th>
                            <th>Duration</th>
                            <th>Start</th>
                            <th>Finish</th>
                            <th>Resource name</th>
                            <th>Total cost ($)</th>
                        </tr>
                    </thead>

                    <?php
                    if (!printTasksReport()) {
                        echo "No tasks added.";
                    }
                    ?>



                </table>
                <p class="text-secondary">Total cost calculated as: 8*stdrate*duration*(maxCommit/100). (where 8 is the number of hours each day)</p>
            </div>
            
        </div>

    </div>

    <!-- Footer -->
    <?php printFooter($projectID, $projectName); ?>



</body>

</html>