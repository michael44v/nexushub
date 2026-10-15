<?php
include 'config.php';

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid INT NOT NULL,
    sender VARCHAR(50) DEFAULT 'user',
    message TEXT NOT NULL,
    tm TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if (isset($_POST['send_msg'])) {
    $uid = intval($_POST['uid']);
    $msg = filter($_POST['msg']);
    $sender = 'admin';
    if (!empty($msg) && $uid > 0) {
        $stmt = $conn->prepare("INSERT INTO messages (uid, sender, message) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $uid, $sender, $msg);
            $stmt->execute();
        }
    }
}

// Fetch users with messages or all users
$users_with_msgs = $conn->query("SELECT DISTINCT u.id, u.uname, u.uemail FROM user_details u LEFT JOIN messages m ON u.id = m.uid ORDER BY m.tm DESC");

include 'admin_header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Active User Chats List -->
    <div class="admin-card p-6">
        <h3 class="font-syne font-800 text-lg text-white mb-4 flex items-center gap-2">
            <i class="fas fa-comments text-amber-400"></i> Active Trader Conversations
        </h3>

        <div class="space-y-2 max-h-[500px] overflow-y-auto">
            <?php
            if ($users_with_msgs && $users_with_msgs->num_rows > 0):
                while ($u = $users_with_msgs->fetch_assoc()):
                    $chatId = $u['id'];
                    $activeClass = (isset($_GET['chat']) && $_GET['chat'] == $chatId) ? 'bg-amber-500/10 border-amber-500/40 text-amber-400' : 'bg-black/40 border-white/5 hover:bg-white/5 text-gray-300';
            ?>
                <a href="?chat=<?php echo $chatId; ?>" class="block p-3 rounded-xl border transition <?php echo $activeClass; ?>">
                    <div class="flex items-center justify-between">
                        <span class="font-700 text-sm"><?php echo htmlspecialchars($u['uname']); ?></span>
                        <span class="text-[10px] text-gray-500">UID: #<?php echo $chatId; ?></span>
                    </div>
                    <span class="block text-[11px] text-gray-400 mt-0.5 truncate"><?php echo htmlspecialchars($u['uemail']); ?></span>
                </a>
            <?php
                endwhile;
            else:
            ?>
                <div class="text-center py-8 text-gray-500 text-xs">No active conversations found.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Live Chat View -->
    <div class="lg:col-span-2 admin-card p-6 flex flex-col justify-between min-h-[550px]">
        <?php
        if (isset($_GET['chat'])):
            $chat_uid = intval($_GET['chat']);
            $msgs = $conn->query("SELECT * FROM messages WHERE uid = '$chat_uid' ORDER BY tm ASC");
            $u_info_res = $conn->query("SELECT uname, uemail FROM user_details WHERE id = '$chat_uid'");
            $u_info = $u_info_res ? $u_info_res->fetch_assoc() : ['uname' => 'Trader', 'uemail' => ''];
        ?>
            <div>
                <!-- Chat Header -->
                <div class="p-3 bg-black/40 rounded-xl border border-white/5 flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 font-800 font-syne flex items-center justify-center text-xs">
                            <?php echo strtoupper(substr($u_info['uname'] ?? 'T', 0, 1)); ?>
                        </div>
                        <div>
                            <h4 class="font-syne font-800 text-sm text-white"><?php echo htmlspecialchars($u_info['uname'] ?? 'User #'.$chat_uid); ?></h4>
                            <p class="text-[10px] text-gray-400"><?php echo htmlspecialchars($u_info['uemail'] ?? ''); ?></p>
                        </div>
                    </div>
                    <span class="text-[10px] bg-green-500/10 text-green-400 px-2 py-0.5 rounded-full font-800">Online</span>
                </div>

                <!-- Messages Stream -->
                <div class="p-4 bg-black/60 rounded-xl border border-white/5 h-[380px] overflow-y-auto space-y-3">
                    <?php
                    if ($msgs && $msgs->num_rows > 0):
                        while ($m = $msgs->fetch_assoc()):
                            $isAdmin = ($m['sender'] === 'admin');
                    ?>
                        <div class="flex flex-col <?php echo $isAdmin ? 'items-end' : 'items-start'; ?>">
                            <div class="max-w-[75%] p-3 rounded-2xl text-xs leading-relaxed <?php echo $isAdmin ? 'bg-amber-500 text-black font-600 rounded-br-none' : 'bg-white/10 text-white rounded-bl-none'; ?>">
                                <?php echo htmlspecialchars($m['message']); ?>
                                <span class="block text-[9px] mt-1 <?php echo $isAdmin ? 'text-black/60' : 'text-gray-400'; ?>">
                                    <?php echo htmlspecialchars($m['tm'] ?? ''); ?>
                                </span>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <div class="text-center py-16 text-gray-500 text-xs">
                            No message history with this user yet. Type below to send a message.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Send Input -->
            <form method="POST" action="?chat=<?php echo $chat_uid; ?>" class="mt-4 flex items-center gap-2">
                <input type="hidden" name="uid" value="<?php echo $chat_uid; ?>">
                <input type="text" name="msg" class="flex-1 bg-black/60 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-amber-500" placeholder="Type response to trader..." required>
                <button type="submit" name="send_msg" class="bg-gradient-gold text-black font-800 text-xs py-3 px-6 rounded-xl hover:opacity-90 transition flex items-center gap-2 shadow-lg shadow-amber-500/20">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </form>

        <?php else: ?>
            <div class="flex-1 flex flex-col items-center justify-center text-center p-12 text-gray-500 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-2xl">
                    <i class="fas fa-comments"></i>
                </div>
                <h4 class="font-syne font-800 text-base text-white">Select a Conversation</h4>
                <p class="text-xs max-w-sm">Choose a trader from the left panel to view message history and send live support responses.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'admin_footer.php'; ?>
