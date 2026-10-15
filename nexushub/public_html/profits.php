<?php include 'config.php'; include 'cdn.php';

if(isset($_POST['pft'])) {
	$user_to_add = $_POST['uid'];
	$amount_add = $_POST['pft'];
	

	//get user bal first


			$get_prev_bal = $conn->query("select * from bal where uid='$user_to_add'");

		if($get_prev_bal->num_rows>0) {
			while($tr=$get_prev_bal->fetch_assoc()) {

						
						$old = $tr['bal'];
            $new = $tr['bal']+$amount_add;
	$update_bal= $conn->query("update bal set bal='$new' where uid='$user_to_add'");
if($update_bal) {
 ?>

<script>//window.location.reload();</script><?php
}
					}	
				}
//$update = $conn->query("update transactions set txn_status='approved' where uid='$user_to_approve' and txn_id='$trans_id'");

				





}

?><div class="sidenav">
<a href="/admin"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M264-216h96v-204q0-15.3 10.35-25.65Q380.7-456 396-456h168q15.3 0 25.65 10.35Q600-435.3 600-420v204h96v-348L480-726 264-564v348Zm-72-12v-336q0-16.85 7.5-31.92Q207-611 221-622l216-162q20-14 43-14t43 14l216 162q14 11 21.5 26.08Q768-580.85 768-564v336q0 34.65-24.67 59.32Q718.65-144 684-144H564q-15.3 0-25.65-10.35Q528-164.7 528-180v-204h-96v204q0 15.3-10.35 25.65Q411.3-144 396-144H276q-34.65 0-59.32-24.68Q192-193.35 192-228Zm288-243Z"/></svg></a><br>
<a href="/profits"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M433.33-453.33V-382q0 11.67 8.5 20.17 8.5 8.5 20.17 8.5h36.67q11.9 0 19.95-8.5 8.05-8.5 8.05-20.17v-71.33h72q11.9 0 19.95-8.5 8.05-8.5 8.05-20.17v-36.67q0-11.9-8.05-19.95-8.05-8.05-19.95-8.05h-72v-72q0-11.9-8.05-19.95-8.05-8.05-19.95-8.05H462q-11.67 0-20.17 8.05-8.5 8.05-8.5 19.95v72H362q-11.67 0-20.17 8.05-8.5 8.05-8.5 19.95V-482q0 11.67 8.5 20.17 8.5 8.5 20.17 8.5h71.33ZM480-84q-5.88 0-10.92-1T459-88q-137.67-46.33-218.33-167.83Q160-377.33 160-520.74v-193.59q0-21.17 12.08-38.11 12.09-16.94 31.25-24.56l253.34-94.67q12-4.33 23.33-4.33 11.33 0 23.33 4.33L756.67-777q19.16 7.62 31.25 24.56Q800-735.5 800-714.33v193.59q0 143.41-80.67 264.91Q638.67-134.33 501-88q-5.04 2-10.08 3T480-84Zm0-66q111.33-36.33 182.33-139.67 71-103.33 71-231v-193.66L480-809.67l-253.33 95.34v193.66q0 127.67 71 231Q368.67-186.33 480-150Zm0-330Z"/></svg></a><br>


<a href="/user_management">
    <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M400-483.33q-66 0-109.67-43.67-43.66-43.67-43.66-109.67t43.66-109.66Q334-790 400-790t109.67 43.67q43.66 43.66 43.66 109.66T509.67-527Q466-483.33 400-483.33Zm-320 254v-33.32Q80-297 97.33-326q17.34-29 49.34-44 58-27 119.56-43.17 61.56-16.16 126.1-16.16 14.17 0 23.75 9.66 9.59 9.67 9.59 23.89 0 14.21-9.59 23.83-9.58 9.62-23.75 9.62-57.33 0-111.83 14t-105.83 39.66q-12.67 7-20.34 19.11-7.66 12.1-7.66 26.89v33.34H393q14.17 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71t-23.75 9.5H146.67q-27.5 0-47.09-19.58Q80-201.83 80-229.33ZM654-146l-6.67-38q-15.33-5-30.5-13.17Q601.67-205.33 590-216l-35 8.33q-9.67 2.67-18.52-1.06t-13.81-11.94l-6.58-11.13q-5.76-9.53-4.09-19.53 1.67-10 9.33-17l29.34-27q-2-10.67-2-25.34 0-14.66 2-25.33l-29.34-27q-7.66-6.33-9.33-16.5t4-18.83l7-13q5-8.34 13.33-11.67 8.34-3.33 18-.67l35.67 8.34q11.67-10.67 26.83-18.84 15.17-8.16 30.5-13.16l6.67-38.34q2.37-11.59 10.66-18.63 8.29-7.03 19.34-7.03h10.67q11.05 0 19.34 7.31t10.66 18.69l6.66 38q15.34 5 30.5 13.33 15.17 8.33 26.84 19.33L823-433q10-3 19.06.64 9.06 3.63 13.94 12.36l6.58 11.13q5.75 9.54 4.09 19.54-1.67 10-9.34 17l-29.33 27q2 10 2 25t-2 25l29.33 27q7.67 6.33 9.34 16.5 1.66 10.16-4 18.83l-7 13q-5 8.33-13.34 11.67-8.33 3.33-18 .66L788.67-216q-11.67 10.67-26.84 18.83-15.16 8.17-30.5 13.17l-6.66 38.33q-2.37 11.59-10.66 18.63-8.29 7.04-19.34 7.04H684q-11.05 0-19.34-7.31T654-146Zm35.33-94.67q35 0 57.5-22.5t22.5-57.5q0-35-22.5-57.5t-57.5-22.5q-35 0-57.5 22.5t-22.5 57.5q0 35 22.5 57.5t57.5 22.5ZM400-550q37 0 61.83-24.83 24.84-24.84 24.84-61.84t-24.84-61.83Q437-723.33 400-723.33t-61.83 24.83q-24.84 24.83-24.84 61.83t24.84 61.84Q363-550 400-550Zm0-86.67Zm10 407.34Z"/></svg>
</a>

<a href='/admin_deposits/new'>
    
    <style> .badge { background:red; margin-top:-95%;}</style>
<svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M312-146.67h336v-124.66q0-70-49-118.67t-119-48.67q-70 0-119 48.67t-49 118.67v124.66ZM193.33-80q-14.16 0-23.75-9.62-9.58-9.61-9.58-23.83 0-14.22 9.58-23.72 9.59-9.5 23.75-9.5h52v-124.66q0-67.67 36.17-124.17t97.17-84.5q-61-28.67-97.17-85.17t-36.17-124.16v-124h-52q-14.16 0-23.75-9.62-9.58-9.62-9.58-23.83 0-14.22 9.58-23.72 9.59-9.5 23.75-9.5h573.34q14.16 0 23.75 9.62 9.58 9.61 9.58 23.83 0 14.22-9.58 23.72-9.59 9.5-23.75 9.5h-52v124q0 67.66-36.17 124.16T581.33-480q61 28 97.17 84.5t36.17 124.17v124.66h52q14.16 0 23.75 9.62 9.58 9.62 9.58 23.83 0 14.22-9.58 23.72-9.59 9.5-23.75 9.5H193.33Z"/></svg>


<?php
              $sqlll = "SELECT count(txn_id) as tx FROM withdrawals";
              $ress = $conn->query($sqlll);
              
              if ($ress->num_rows > 0) {
                  while($roo = $ress->fetch_assoc()) {
                      echo "<span class='badge'>".$roo["tx"]."</span>";
                     
                  }
              } else {
                  echo "";
              }
    ?>
    

<br>
</a>
<a href="messenger.php"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M240-240 136.67-136.67Q121-121 100.5-129.6 80-138.21 80-160.33v-653q0-27 19.83-46.84Q119.67-880 146.67-880h666.66q27 0 46.84 19.83Q880-840.33 880-813.33v506.66q0 27-19.83 46.84Q840.33-240 813.33-240H240Zm-28.67-66.67h602v-506.66H146.67v575l64.66-68.34Zm-64.66 0v-506.66 506.66Zm126.66-92.66H522q14.17 0 23.75-9.62t9.58-23.83q0-14.22-9.58-23.72-9.58-9.5-23.75-9.5H273.33q-14.16 0-23.75 9.62-9.58 9.61-9.58 23.83 0 14.22 9.58 23.72 9.59 9.5 23.75 9.5Zm0-126.67h413.34q14.16 0 23.75-9.62 9.58-9.61 9.58-23.83 0-14.22-9.58-23.72-9.59-9.5-23.75-9.5H273.33q-14.16 0-23.75 9.62-9.58 9.62-9.58 23.83 0 14.22 9.58 23.72 9.59 9.5 23.75 9.5Zm0-126.67h413.34q14.16 0 23.75-9.61 9.58-9.62 9.58-23.84 0-14.21-9.58-23.71-9.59-9.5-23.75-9.5H273.33q-14.16 0-23.75 9.61-9.58 9.62-9.58 23.84 0 14.21 9.58 23.71 9.59 9.5 23.75 9.5Z"/></svg><br></a><br>


<a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="white"><path d="M480-80q-82.33 0-155.33-31.5-73-31.5-127.34-85.83Q143-251.67 111.5-324.67T80-480q0-74.33 25.5-140.83Q131-687.33 176-740q9.67-12 22.83-11.33Q212-750.67 221-742q9 8.67 11.83 22 2.84 13.33-9.16 27-36 43.67-56.5 97.83Q146.67-541 146.67-480q0 138.67 97.33 236 97.33 97.33 236 97.33 139.33 0 236.33-97.33t97-236q0-61-20.16-115.17Q773-649.33 737-693q-12-13.67-9.5-27t11.5-22q9-8.67 22.17-9.33Q774.33-752 784-740q45 52.67 70.5 119.17T880-480q0 82.33-31.5 155.33-31.5 73-85.5 127.34Q709-143 636-111.5T480-80Zm0-360q-14.33 0-23.83-9.5-9.5-9.5-9.5-23.83v-373.34q0-14.33 9.5-23.83 9.5-9.5 23.83-9.5 14.33 0 23.83 9.5 9.5 9.5 9.5 23.83v373.34q0 14.33-9.5 23.83-9.5 9.5-23.83 9.5Z"/></svg></a><br>
</div>


  <div class="content">
    <h2 style="text-align: center; color: gray;"> Admin <img src='https://static.vecteezy.com/system/resources/previews/000/290/610/non_2x/administration-vector-icon.jpg' width='35px' style='border-radius:100%; position:absolute; display:block; margin-top:-8%;  margin-left: 1%; '/>
    </h2><br>
    
    <h3>Profit Adder</h3>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <table class="w3-table w3-bordered">
    <tr>
      <th>UID</th>
      <th>Name</th>
      <th>Balance</th>
      <th>Profit</th>
      
    </tr>
    <?php
                                      $sql = "SELECT * from user_details order by uid desc";
                                      $result = $conn->query($sql);
                                      
                                      if ($result->num_rows > 0) {
                                          while($row = $result->fetch_assoc()) {
                                              
$bal = $conn->query("select * from bal where uid ='{$row["uid"]}'");
if($bal->num_rows>0) {
  while($ref=$bal->fetch_assoc()) {
    //echo $row["uid"].$ref["balance"];
 
?>

<tr>
      <td><?php echo $row['uid']*456;?></td>
      <td><?php echo $row['uname'];?></td>
      <td><or><?php echo '$'.number_format($ref['bal'], 2);?></or></td>
      <td>
        <form action='/profits' method='post'>
          <input type='number' name='pft' required/><br>
          <input type='hidden' name='uid' value='<?php echo $row['uid'];?>'/>
          <button>Add</button>
      </form>
    </td>
    </tr>

                                        <?php 
                                               }
                                              }  
                                      }
                                        }
                                        ?>
                                        </table>

</div>
<style>
  or { color:orange;}
  input {
    border:hidden;
    border:1px solid #ccc;
    background:transparent;
    color:black;
    width:200%;
  }
  button {
    border:hidden;
    background:white;
    padding:8%;
    width: 140%;
  }
     .content {
        padding: 3%;
            width: 80%;
            margin-left: 20%;
        }
     body {
            color: black;
            font-family: 'SF Pro Display', sans-serif;
                
    background:#e6e6e6;
        }
        .sidenav {
  height: 100%; /* Full-height: remove this if you want "auto" height */
  width: 70px; /* Set the width of the sidebar */
  position: fixed; /* Fixed Sidebar (stay in place on scroll) */
  z-index: 1; /* Stay on top */
  top: 0; /* Stay at the top */
  left: 0;
  background-color: #111; /* Black */
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 20px;
}

/* The navigation menu links */
.sidenav a {
  padding: 6px 8px 6px 16px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
}

/* When you mouse over the navigation links, change their color */
.sidenav a:hover {
  color: #f1f1f1;
}

/* Style page content */
.main {
  margin-left: 160px; /* Same as the width of the sidebar */
  padding: 0px 10px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidebar (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

</style>