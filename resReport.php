<?php
require_once("config/db.php");

if (!(isset($_GET['pID']) && isset($_GET['pName']))) {
    header('location: projects.php');
} else {
    if (ProjectIDNotExist($_GET['pID'])) {
        header('location: projects.php');
    }
    if (ProjectNameNotExist($_GET['pName'])) {
        header('location: projects.php');
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
function printResReport()
{
    global $conn, $projectID, $projectName;
    $rates = 0;
    $sqlRes = "SELECT * FROM resource WHERE project_id='$projectID'";
    $result = mysqli_query($conn, $sqlRes);
    $num = mysqli_num_rows($result);
    if ($num != 0) {
        echo "<tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['type'] . "</td>
            <td>" . $row['max'] . "</td>
            <td>" . $row['rate'] . "</td>
          </tr>";
            $rates += $row['rate'];
        }
        echo "
        </tbody>
        <tfoot>
            <tr>
                <th colspan='4'>Number of Resources:</th>
                <th>".$num."</th>
            </tr>
            <tr>
            <th colspan='4'>Sum of standard rate:</th>
            <th>".$rates." ($/hr)</th>
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

    <title>Tasks Resources | Project Professional</title>
</head>

<body>
    <!-- navbar-->
    <?php require_once 'nav.php';
    printNavbar('reptab', $projectID, $projectName); ?>

    <div class="container-fluid up-footer">
        <h1 class="text-center">Resources Report</h1>
        <div class="form-add col-sm-10 offset-1">

            <div class="table-project col-sm-10 offset-1">
            <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> print this page</button>
                <table id="example" class="table table-striped table-bordered table-project" style="width:100%">
                    <thead>
                        <tr>
                            <th>Resource ID</th>
                            <th>Resource Name</th>
                            <th>Type</th>
                            <th>Max commitment (%)</th>
                            <th>St.Rate ($/hr)</th>
                        </tr>
                    </thead>

                    <?php
                    if(!printResReport()){
                        echo "No resources added.";
                    }
                    ?>



                </table>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <?php printFooter($projectID, $projectName); ?>



</body>

</html>