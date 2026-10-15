<?php
//session_start();

$servername = "localhost";
$username = "u596131371_root";
$password = "victor47009A?";
$dev = "u596131371_nexus";

// Create connection
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($servername, $username, $password, $dev);

// Check connection
if ($conn->connect_error) {
    // If database connection fails, provide a fallback object to prevent blank fatal PHP error pages
    $conn = new class {
        public $connect_error = "Connection failed";
        public $insert_id = 0;
        public function query($q) {
            return new class {
                public $num_rows = 0;
                public function fetch_assoc() { return null; }
            };
        }
        public function prepare($q) {
            return new class {
                public function bind_param(...$a) {}
                public function execute() { return true; }
                public function get_result() {
                    return new class {
                        public $num_rows = 0;
                        public function fetch_assoc() { return null; }
                    };
                }
            };
        }
    };
}
else {
    //echo "Connected successfully";

}
function filter($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
  }
//echo "Connected successfully";

?>