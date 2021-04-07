<?php 
function printNavbar($active,$pid,$pname){
    $par='?pID='.$pid.'&pName='.$pname.'';
    echo '
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="navbar-brand nav-project"><?php echo $projectName; ?>'.$pname.' project</div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">';
      $taskstab=($active=='home')?'active':'';
        echo '<li class="nav-item '.$taskstab.'">
          <a class="nav-link" href="home.php'.$par.'"><i class="fas fa-calendar-alt"></i> Tasks</a>
        </li>';
        $restab=($active=='restab')?'active':'';
        echo '<li class="nav-item '.$restab.'">
          <a class="nav-link" href="resources.php'.$par.'"><i class="fas fa-briefcase"></i> Resources</a>
        </li>';
        $reptab=($active=='reptab')?'active':'';
        echo '<li class="nav-item dropdown '.$reptab.'">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-chart-bar"></i> Reports
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="tasksReport.php'.$par.'">Tasks Report</a>
            <a class="dropdown-item" href="resReport.php'.$par.'">Resources Report</a>
            <a class="dropdown-item" href="allocRes.php'.$par.'">Allocated resources Report</a>
            <a class="dropdown-item" href="tasksCostReport.php'.$par.'">Tasks cost Report</a>
            <a class="dropdown-item" href="projectCostReport.php'.$par.'">Project cost Report</a>

          </div>
        </li>';
        echo ' </ul>
      <form class="form-inline my-2 my-lg-0">
        <a class="btn btn-outline-danger nav-btn" href="projects.php"><i class="fas fa-sign-out-alt"></i> Exit project</a>
      </form>
    </div>
  </nav>';

}
function printFooter($pid,$pname){
  $par='?pID='.$pid.'&pName='.$pname.'';
    echo ' <footer class="bg-dark text-center text-lg-start text-light">
    <!-- Grid container -->
    <div class="container p-4">
      <!--Grid row-->
      <div class="row">
        <!--Grid column-->
        <div class="col-sm-4">
          <h5 class="text-uppercase">About</h5>

          <p>
            Developed by:<br>
            Omar Alnuwaysir<br>
            Bander Elrubaiaan<br>
            Waleed Alharbi
          </p>
        </div>
        <!--Grid column-->

        <!--Grid column-->
        <div class="col-sm-4">
          <h5 class="text-uppercase">Project</h5>

          <ul class="list-unstyled mb-0">
            <li>
              <a href="home.php'.$par.'" class="text-light">Tasks</a>
            </li>
            <li>
              <a href="resources.php'.$par.'" class="text-light">Resources</a>
            </li>
            </li>
          </ul>
        </div>
        <!--Grid column-->

        <!--Grid column-->
        <div class="col-sm-4">
          <h5 class="text-uppercase mb-0">Report</h5>

          <ul class="list-unstyled">
            <li>
              <a href="tasksReport.php'.$par.'" class="text-light">Task report</a>
            </li>
            <li>
              <a href="resReport.php'.$par.'" class="text-light">Resources report</a>
            </li>
            <li>
              <a href="allocRes.php'.$par.'" class="text-light">Allocated resources report</a>
            </li>
            <li>
              <a href="tasksCostReport.php'.$par.'" class="text-light">Tasks cost report</a>
            </li>
            <li>
              <a href="projectCostReport.php'.$par.'" class="text-light">Project cost report</a>
            </li>
          </ul>
        </div>
        <!--Grid column-->
      </div>
      <!--Grid row-->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
      © 2021: Project Professional
    </div>
    <!-- Copyright -->
  </footer>';
}
?>