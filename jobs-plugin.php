<?php

/**
 *  Plugin Name: Jobs-Plugin
 *  Description: My plugin for jobs!
 *  Author: Michael
 * 
 */



function wporg_filter_title($title)
{
  return $title;
}

add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );

    /**
     * Add stylesheet to the page
     */
    function safely_add_stylesheet() {
        wp_enqueue_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    }



add_filter('the_title', 'wporg_filter_title');


/**
 * jobs shortcode
 * takes arguments (int wage, String jobtype[part, full, contract])
 */
function wporg_shortcode($atts = [])
{

  //If no arguments are given
  if (empty($atts)) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT position, jobtype, postingdate, wage, jobdescription, email FROM jobs");
    $results = $wpdb->get_results($query);



    $jobType = ""; //Holds the string that will be displayed as job type
    $color = ""; //The color of the row
    $allData = "<table>"; //Will hold all the data that will be displayed from the shortcode



    $modals = ""; //the string that will hold the modals html

    $script = ""; //the string that will hold the javascript



    $x = 0; //The incrementing variable for the while loop

    $allData .= '<tr><th>Position</th><th>Type</th><th>Date Posted</th></tr>'; //First row of table
    while ($x < sizeof($results)) {

      
      /*
        Sets the jobtype and the color of the row 
      */
      if ($results[$x]->jobtype == "full") {
        $jobType = "Full time";
        $color = "green";
      } elseif ($results[$x]->jobtype == "part") {
        $jobType = "Part time";
        $color = "yellow";
      } else {
        $jobType = "Contract";
        $color = "red";
      }

      //Following lines add to the modal html
      $modals .= "<div id='myModal' class='modal' style='border:1px solid black'><div class='modal-content'><span class='close'>&times;</span>";

      $modals .= "<p>Position: ". $results[$x]->position . "</p>" . "<p>Job type: ". $jobType . "</p>" . "<p> Description: ". $results[$x]->jobdescription . "</p>" . "<p>Wage (per hour): ". $results[$x]->wage . "</p>". "<p> Email: ". $results[$x]->email . "</p>";

      $modals .= "</div></div>";

      //Adds to the default table
      $allData .= ('<tr style="background-color:' . $color . '"><td><button id="myBtn" class="myBtn">  ' . $results[$x]->position . ' </button></td>' .
        '<td> ' . $jobType  . '</td>' .
        '<td>  ' . $results[$x]->postingdate  . '</td></tr>');

      //Fills in the javascript for the modals
      $script .= "<script>

      var modal".$x." = document.getElementsByClassName('modal')[".$x."];
  
      var btn".$x." = document.getElementsByClassName('myBtn')[".$x."];
  
      var span".$x." = document.getElementsByClassName('close')[".$x."];
      
  
      btn".$x.".onclick = function() {
        modal".$x.".style.display = 'block';
      }
      
      span".$x.".onclick = function() {
        modal".$x.".style.display = 'none';
      }
      
      window.onclick = function(event) {
        if (event.target == modal".$x.") {
          modal".$x.".style.display = 'none';
        }
      }
      </script>";

      $x++;
    }

    

    //Following lines add to the data that will be returned from the shortcode
    $allData .= "</table>";
    $allData .= $modals;
    $allData .= $script;

  } elseif (!is_null($atts[0]) && is_null($atts[1])) { //This statement handles if only the first parameter is given
    
    //Search query that handles a minimum wage
    global $wpdb;
    $query = $wpdb->prepare("SELECT position, jobtype, postingdate, wage, jobdescription, email FROM jobs WHERE wage>=%d", $atts[0]);
    $results = $wpdb->get_results($query);



    $jobType = "";

    $allData = "<table>";

    $modals = "";

    $script = "";

    $x = 0;
    $allData .= '<tr><th>Position</th><th>Type</th><th>Date Posted</th></tr>';
    while ($x < sizeof($results)) {

      

      if ($results[$x]->jobtype == "full") {
        $jobType = "Full time";
      } elseif ($results[$x]->jobtype == "part") {
        $jobType = "Part time";
      } else {
        $jobType = "Contract";
      }

      $modals .= "<div id='myModal' class='modal' style='border:1px solid black'><div class='modal-content'><span class='close'>&times;</span>";

      $modals .= "<p>Position: ". $results[$x]->position . "</p>" . "<p>Job type: ". $jobType . "</p>" . "<p> Description: ". $results[$x]->jobdescription . "</p>" . "<p>Wage (per hour): ". $results[$x]->wage . "</p>". "<p> Email: ". $results[$x]->email . "</p>";

      $modals .= "</div></div>";


      $allData .= ('<tr><td><button id="myBtn" class="myBtn">  ' . $results[$x]->position . ' </button></td>' .
        '<td> ' . $jobType  . '</td>' .
        '<td>  ' . $results[$x]->postingdate  . '</td></tr>');

        $script .= "<script>

        var modal".$x." = document.getElementsByClassName('modal')[".$x."];
    
        var btn".$x." = document.getElementsByClassName('myBtn')[".$x."];
    
        var span".$x." = document.getElementsByClassName('close')[".$x."];
        
    
        btn".$x.".onclick = function() {
          modal".$x.".style.display = 'block';
        }
        
        span".$x.".onclick = function() {
          modal".$x.".style.display = 'none';
        }
        
        window.onclick = function(event) {
          if (event.target == modal".$x.") {
            modal".$x.".style.display = 'none';
          }
        }
        </script>";

      $x++;
    }
    $allData .= "</table>";
    $allData .= $modals;
    $allData .= $script;
  } else {  //Handles if both the wage and job type are given parameters
    
    //Search query that handles a minimum wage and job type
    global $wpdb;
    $query = $wpdb->prepare("SELECT position, jobtype, postingdate, wage, jobdescription, email FROM jobs WHERE wage>=%d AND jobtype=%s", $atts[0], $atts[1]);
    $results = $wpdb->get_results($query);

    

    $allData = "<table>";

    $modals = "";

    $script = "";

    $x = 0;
    $allData .= '<tr><th>Position</th><th>Type</th><th>Date Posted</th></tr>';
    while ($x < sizeof($results)) {

      $jobType = "";

    if ($atts[1] == "full") {
      $jobType = "Full time";
    } elseif ($atts[1] == "part") {
      $jobType = "Part time";
    } else {
      $jobType = "Contract";
    }

      $modals .= "<div id='myModal' class='modal' style='border:1px solid black'><div class='modal-content'><span class='close'>&times;</span>";

      $modals .= "<p>Position: ". $results[$x]->position . "</p>" . "<p>Job type: ". $jobType . "</p>" . "<p> Description: ". $results[$x]->jobdescription . "</p>" . "<p>Wage (per hour): ". $results[$x]->wage . "</p>". "<p> Email: ". $results[$x]->email . "</p>";

      $modals .= "</div></div>";

      $allData .= ('<tr><td><button id="myBtn" class="myBtn">  ' . $results[$x]->position . ' </button></td>' .
        '<td> ' . $jobType  . '</td>' .
        '<td>  ' . $results[$x]->postingdate  . '</td></tr>');

        $script .= "<script>

        var modal".$x." = document.getElementsByClassName('modal')[".$x."];
    
        var btn".$x." = document.getElementsByClassName('myBtn')[".$x."];
    
        var span".$x." = document.getElementsByClassName('close')[".$x."];
        
    
        btn".$x.".onclick = function() {
          modal".$x.".style.display = 'block';
        }
        
        span".$x.".onclick = function() {
          modal".$x.".style.display = 'none';
        }
        
        window.onclick = function(event) {
          if (event.target == modal".$x.") {
            modal".$x.".style.display = 'none';
          }
        }
        </script>";

      $x++;
    }
    $allData .= "</table>";
    $allData .= $modals;
    $allData .= $script;
  }


  return ($allData);
}
add_shortcode('jobs', 'wporg_shortcode');


function myplugin_activate()
{
  global $wpdb;
  $wpdb->query("CREATE TABLE Jobs (
    Position varchar(255),
    JobType varchar(255),
    Email varchar(255),
    JobDescription varchar(255),
    Wage int,
    PostingDate Date
    );");
}
register_activation_hook(
  __FILE__,
  'myplugin_activate'
);



function myplugin_deactivate()
{
  global $wpdb;
  $wpdb->query("DROP TABLE Jobs;");
}
register_deactivation_hook(
  __FILE__,
  'myplugin_deactivate'
);


// Handles the wp admin page. User can add jobs to the database
function wp_Jobs_adminpage_html()
{
  
  if (!current_user_can('manage_options')) {
    return;
  }
?>


  <div class="wrap">
    <h1><?php esc_html(get_admin_page_title()); ?></h1>
    <form action="<?php admin_url('options-general.php?page=jobs-plugin/jobs.php') ?>" method="post">
      <label for="position">Position: </label><input type="text" name="position"></br>
      <label for="type">Job type:</label><input type="text" name="type"></br>
      <label for="email">Contact Email: </label><input type="text" name="email"></br>
      <label for="description">Description: </label><input type="text" name="description"></br>
      <label for="wage">Wage</label><input type="number" name="wage"></br>
      <label for="date">Date</label><input type="date" name="date"></br>
      <input type="submit">
    </form>
    <?php

    $position = $_POST['position'];
    $type = $_POST['type'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $wage = $_POST['wage'];
    $date = $_POST['date'];



    $array = array('position' => $position, 'type' => $type, 'email' => $email, 'description' => $description, 'wage' => $wage, 'date' => $date);

    $sql = "INSERT INTO jobs (`Position`, `JobType`, `Email`, `JobDescription`, `Wage`, `PostingDate`)
            VALUES($position, $type, $email, $description, $wage, TO_DATE( $date , 'DD/MM/YYYY'));"; ?>

    <p><a href="<?php admin_url('options-general.php?page=jobs/jobs-plugin.php'); ?>?page=jobs&position=<?php echo $string ?>">my link action</a></p>



    <?php addJob() ?>
    <p><?php
        try {
          $selectAll = "SELECT position, wage FROM `jobs`";
          global $wpdb;
          $result = $wpdb->get_results($selectAll);
          foreach ($result as $row) {
            echo 'Position:  ' . $row->position . '   Wage: ' . $row->wage . '</br>';
          }
        } catch (PDOException $e) {
          echo "Selection failed: " . $e->getMessage();
          exit();
        }
        ?> </p>
  </div>


<?php
}


/*
Adds a job to the jobs table
takes all of the post paremeters from the 'jobs' admin page
*/
function addJob()
{
  if (!is_null($_POST['position'])) {

    //Recive all of the post parameters after "submit" is clicked on the jobs admin page
    $position = $_POST['position'];
    $type = $_POST['type'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $wage = $_POST['wage'];
    $date = $_POST['date'];

    try {
      global $wpdb;
      $insert = "INSERT INTO jobs (`Position`, `JobType`, `Email`, `JobDescription`, `Wage`, `PostingDate`)
                                VALUES('$position', '$type', '$email', '$description', '$wage', '$date');";

      $wpdb->query($insert);


      echo "success";
    } catch (PDOException $e) {
      echo "ITS NO GOOD ";
      exit();
    }
  } else {
    echo "Add a job";
  }
}


function wp_jobs_adminpage()
{
  add_menu_page(
    'Jobs',
    'Jobs',
    'manage_options',
    'jobs',
    'wp_jobs_adminpage_html',
    '', 
    20
  );
}
add_action('admin_menu', 'wp_jobs_adminpage');
