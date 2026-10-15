<?php
include 'config.php';
include 'cdn.php';

if (isset($_POST['send_msg'])) {
    $uid = $_POST['uid'];
    $msg = filter($_POST['msg']);
    $sender = 'admin';
    $conn->query("INSERT INTO messages (uid, sender, message) VALUES ('$uid', '$sender', '$msg')");
}

$users_with_msgs = $conn->query("SELECT DISTINCT u.uid, u.uname FROM user_details u JOIN messages m ON u.uid = m.uid ORDER BY m.tm DESC");
?>
<div class="sidenav">
    <a href="/admin"><i class="fa fa-home text-white"></i></a><br>
    <a href="/profits"><i class="fa fa-money text-white"></i></a><br>
    <a href="/user_management"><i class="fa fa-users text-white"></i></a><br>
    <a href="/admin_deposits/new"><i class="fa fa-bank text-white"></i></a><br>
    <a href="/messenger" style="background:orange;"><i class="fa fa-envelope text-white"></i></a><br>
</div>

<div class="content">
    <h2 style="text-align: center; color: gray;">Messenger Admin</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3">
                <h4>Chats</h4>
                <div class="list-group">
                    <?php while($u = $users_with_msgs->fetch_assoc()): ?>
                        <a href="?chat=<?php echo $u['uid']; ?>" class="list-group-item list-group-item-action">
                            <?php echo $u['uname']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <?php if(isset($_GET['chat'])):
                $chat_uid = $_GET['chat'];
                $msgs = $conn->query("SELECT * FROM messages WHERE uid = '$chat_uid' ORDER BY tm ASC");
                $u_info = $conn->query("SELECT uname FROM user_details WHERE uid = '$chat_uid'")->fetch_assoc();
            ?>
                <div class="card">
                    <div class="card-header bg-dark text-white"><?php echo $u_info['uname']; ?></div>
                    <div class="card-body chat-box" style="height: 400px; overflow-y: auto;">
                        <?php while($m = $msgs->fetch_assoc()): ?>
                            <div class="mb-2 <?php echo $m['sender'] == 'admin' ? 'text-right' : ''; ?>">
                                <div class="d-inline-block p-2 rounded <?php echo $m['sender'] == 'admin' ? 'bg-primary text-white' : 'bg-light'; ?>">
                                    <?php echo $m['message']; ?>
                                    <br><small style="font-size: 10px;"><?php echo $m['tm']; ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="card-footer">
                        <form method="POST">
                            <input type="hidden" name="uid" value="<?php echo $chat_uid; ?>">
                            <div class="input-group">
                                <input type="text" name="msg" class="form-control" placeholder="Type a message..." required>
                                <div class="input-group-append">
                                    <button type="submit" name="send_msg" class="btn btn-warning">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Select a chat to start messaging</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .content { padding: 3%; width: 80%; margin-left: 20%; }
    body { background: #e6e6e6; font-family: sans-serif; }
    .sidenav { height: 100%; width: 70px; position: fixed; z-index: 1; top: 0; left: 0; background-color: #111; padding-top: 20px; }
    .sidenav a { padding: 15px; text-decoration: none; font-size: 20px; color: #818181; display: block; text-align: center; }
</style>
