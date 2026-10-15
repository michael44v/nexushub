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
    // Ensure KYC columns exist
    $checkCol = $conn->query("SHOW COLUMNS FROM `user_details` LIKE 'kyc_status'");
    if ($checkCol && $checkCol->num_rows == 0) {
        $conn->query("ALTER TABLE `user_details` ADD `kyc_status` VARCHAR(50) DEFAULT 'Not Submitted'");
        $conn->query("ALTER TABLE `user_details` ADD `kyc_doc_type` VARCHAR(50) DEFAULT NULL");
        $conn->query("ALTER TABLE `user_details` ADD `kyc_doc_url` VARCHAR(500) DEFAULT NULL");
        $conn->query("ALTER TABLE `user_details` ADD `kyc_submitted_at` TIMESTAMP NULL DEFAULT NULL");
        $conn->query("ALTER TABLE `user_details` ADD `kyc_rejection_reason` TEXT DEFAULT NULL");
    }
}
function filter($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
  }
//echo "Connected successfully";

?>