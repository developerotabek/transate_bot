<?php
define('API_KEY', "5539467049:AAEslUDUoe8mZoIPHE-Yv4xs0WYeEStOnrg");
function bot($method, $datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    $res = curl_exec($ch);

    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function html($tx){
    return str_replace(['<','>'],['&#60;','&#62;'],$tx);
}
function translate($source,$target,$text) {
    $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
    $fields = array(
        'sl'=>urlencode($source),
        'tl'=>urlencode($target),
        'q'=>urlencode($text)
    );
    $fields_string = "";
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}
include 'db.php';

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$type = $message->chat->type;
$miid =$message->message_id;
$name = $message->from->first_name;
$lname = $message->from->last_name;
$full_name = $name . " " . $lname;
$full_name = realstring(html($full_name));
$user = $message->from->username;
$fromid = $message->from->id;
$text = $message->text;
$title = $message->chat->title;
$chatuser = $message->chat->username;
$chatuser = $chatuser ? $chatuser : "Shaxsiy Guruh!";
$caption = $message->caption;
$text_link = $entities->type;
$left_chat_member = $message->left_chat_member;
$new_chat_member = $message->new_chat_member;
//editmessage
$callback = $update->callback_query;
$qid = $callback->id;
$mes = $callback->message;
$mid = $mes->message_id;
$cmtx = $mes->text;
$cid = $callback->message->chat->id;
$ctype = $callback->message->chat->type;
$cbid = $callback->from->id;
$cbuser = $callback->from->username;
$data = $callback->data;

$languages = json_encode([
    'inline_keyboard'=>[
        [['text'=>"🏴󠁧󠁢󠁥󠁮󠁧󠁿 ENG-UZB 🇺🇿", 'callback_data'=>"en-uz"],['text'=>"🇺🇿 UZB-ENG 🏴󠁧󠁢󠁥󠁮󠁧󠁿", 'callback_data'=>"uz-en"],],
        [['text'=>"🇷🇺 RUS-UZB 🇺🇿", 'callback_data'=>"ru-uz"],['text'=>"🇺🇿 UZB-RUS 🇷🇺",'callback_data'=>"ru-uz"],],
        [['text'=>"🏴󠁧󠁢󠁥󠁮󠁧󠁿 ENG-RUS 🇷🇺",'callback_data'=>"en-ru"],['text'=>"🇷🇺 RUS-ENG 🏴󠁧󠁢󠁥󠁮󠁧󠁿",'callback_data'=>"ru-en"],],
        [['text'=>"Auto o'zbekchaga tarjima 🇺🇿",'callback_data'=>"auto-uz"],],
        [['text'=>"Auto ruschaga tarjima 🇷🇺",'callback_data'=>"auto-ru"],],
        [['text'=>"Auto ingilizchaga tarjima 🏴󠁧󠁢󠁥󠁮󠁧󠁿",'callback_data'=>"auto-en"],],
        [['text'=>"Auto turkchaga tarjima 🇹🇷",'callback_data'=>"auto-tr"],],
    ]
]);
$startTranslate = json_encode([
    'inline_keyboard'=>[
        [['text'=>"Tilni tanlash", 'callback_data'=>"start"],],
    ],
]);
$changeLang = json_encode([
    'inline_keyboard'=>[
        [['text'=>"🔄 Tilni almashtirish", 'callback_data'=>"back"],],
    ],
]);
$keys = json_encode([
    'resize_keyboard'=> true,
    'keyboard'=>[
        [["text"=>"✅ Tarjimani boshlash"],],
       
    ]
]);

if($text == "✅ Tarjimani boshlash"){
    bot('sendMessage',[
        'chat_id'=>$fromid,
        'text'=>"Test",
        'parse_mode'=>"html",
        'reply_markup'=>$languages,
    ]);
};

$tADS = json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
        [['text'=>"Ha ✅"],['text'=>"Yo`q ❌"],],
    ],
]);
$sendADS = json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
        [['text'=>"🗣 Reklama yuborish"],],
    ],
]);

function slt($fromid) {
    global $conn;
    $slt = "SELECT id FROM tableOfOtabek WHERE fromid='{$fromid}'";
    $query = mysqli_query($conn,$slt);
    if (mysqli_num_rows($query)>0) {
        return true;
    }else{
        return false;
    }
}
function ins($fromid,$type="private") {
    global $conn, $full_name, $user;
    $ins = "INSERT INTO tableOfOtabek (fromid,name,user,account,del,menu) VALUES ('{$fromid}','{$full_name}','{$user}','{$type}','0','')";
    $query = mysqli_query($conn,$ins);
}

if ($message) {
    if ($type == "private") {
        if ($text == "/start") {
            if (slt($fromid) == false) {
                ins($fromid,"private");
            }
            bot('sendMessage',[
                'chat_id'=>$fromid,
                'text'=>"<b>Salom 👋</b><i>" . $full_name . "</i>,\n<i>Botga xush kelibsiz siz bu bot orqali ko'plab tillarga o'zaro tarjima qilishingiz mumkin! 😊</i>",
                'parse_mode'=>'html',
                'reply_markup'=>$keys,
            ]);
        }else if($text){
            $slt = "SELECT * FROM tableOfOtabek WHERE fromid='{$fromid}'";
            $query = mysqli_query($conn,$slt);
            $row = mysqli_fetch_assoc($query);
            $menu = $row["menu"];
            $exp = explode("-", $menu);
            $trans = translate($exp[0],$exp[1],$text);
            $matn = "";
            foreach ($trans->sentences as $key => $value) {
                $matn .= $value->trans;
            }
            $matn = str_replace('\n', "\n", $matn);
            bot('sendMessage',[
                'chat_id'=>$fromid,
                'text'=>$matn,
                'reply_markup'=>$changeLang,
            ]);
        }

        if ($fromid == $admin) {
            $step = file_get_contents("step/admin.step");
            $menu = file_get_contents("step/menu.txt");
            if ($text == "/admin") {
                $slt = "SELECT * FROM tableOfOtabek";
                $query = mysqli_query($conn,$slt);
                $allUsers = mysqli_num_rows($query);

                $groups_slt = "SELECT * FROM tableOfOtabek WHERE account='private'";
                $groups_query = mysqli_query($conn,$groups_slt);
                $users = mysqli_num_rows($groups_query);
                $groups = $allUsers - $users;

                $users_slt = "SELECT * FROM tableOfOtabek WHERE del='1' and account='private'";
                $users_query = mysqli_query($conn,$users_slt);
                $noactive_users = mysqli_num_rows($users_query);

                $groups_slt = "SELECT * FROM tableOfOtabek WHERE del='1' and account='group'";
                $groups_query = mysqli_query($conn,$groups_slt);
                $noactive_groups = mysqli_num_rows($groups_query);

                $noactive_groups = $noactive_groups ? $noactive_groups : '0';
                $noactive_users = $noactive_users ? $noactive_users : '0';

                $matn = "📊 Bot statistikasi:\n\n" . "👥 Barcha azolar: " . $allUsers . " ta\n👥 Guruhlar: " . $groups . "\n👤 Userlar: " . $users . "\n\nNofaollar:\n❌ Nofaol userlar: " . $noactive_users . "\n❌ Nofaol guruhlar: " . $noactive_groups;
                bot('sendMessage',[
                    'chat_id'=>$fromid,
                    'text'=>$matn,
                    'parse_mode'=> "markdown",
                    'reply_markup'=>$sendADS,
                ]);
            }
            if ($text == "🗣 Reklama yuborish") {
                bot('sendMessage',[
                    'chat_id'=>$fromid,
                    'text'=>"🗣 Reklama uchun matn yuboring!"
                ]);
                if (!file_exists("step")) mkdir("step");
                file_put_contents("step/admin.step", "0");
                file_put_contents("step/menu.txt", "senAds");
            }


            if ($step == "0" && $menu == "senAds") {
                if ($message) {
                    bot('copyMessage',[
                        'chat_id'=>$fromid,
                        'from_chat_id'=>$fromid,
                        'message_id'=>$miid,
                    ]);
                    bot('sendMessage',[
                        'chat_id'=>$fromid,
                        'text'=>"Xabarni yuborishga tayyormisiz?",
                        'reply_markup'=>$tADS,
                    ]);
                    file_put_contents("step/admin.step","1");
                    file_put_contents("step/admin.for_id",$fromid);
                    file_put_contents("step/admin.for_mid",$miid);
                }
            }
            if ($step == "1" && $menu == "senAds") {
                if ($text == "Ha ✅") {
                    bot('sendMessage',[
                        'chat_id'=>$fromid,
                        'text'=>"Yuborish boshlanmoqda...",
                        'reply_markup'=>$sendADS,
                    ]);
                    file_put_contents("step/admin.step","2");
                } else if ($text == "Yo`q ❌"){
                    array_map( 'unlink', array_filter((array) glob("step/*")));
                    bot("sendMessage",[
                       'chat_id'=>$fromid,
                       'text'=>"*Yuborish bekor qilindi va o'chirib yuborildi* \n agar reklama yubormoqchi bo'lsangiz 
                       Reklama yuborish tugmasini bosing !!!",
                       'parse_mode'=>'markdown',
                       'reply_markup'=>$sendADS,
                    ]);
                }
            }
        }

    }else if($type == "supergroup" || $type == "group"){
        if ($text == "/start") {
            if (slt($chat_id) == false) {
                ins($chat_id,"group");
            }
            bot('sendMessage',[
                'chat_id'=>$chat_id,
                'text'=>"<b>Salom 👋</b><i>" . $full_name . "</i>,\n<i>Botga xush kelibsiz siz bu bot orqali ko'plab tillarga o'zaro tarjima qilishingiz mumkin! 😊</i>",
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"Tarjimani boshlash", 'url'=>"@translate0121_bot"],],
                    ],
                ]),
            ]);
        };
    };

}

if ($callback) {
    if ($data == "start") {
        bot('editMessageText',[
            'chat_id'=>$cbid,
            'message_id'=>$mid,
            'text'=>"<b>Tilni tanlang✅:</b>",
            'parse_mode'=>'html',
            'reply_markup'=>$languages,
        ]);
    }
    if ($data == "back") {
        $upd = "UPDATE tableOfOtabek SET menu='' WHERE fromid='{$cbid}'";
        $query = mysqli_query($conn,$upd);
        bot('editMessageText',[
            'chat_id'=>$cbid,
            'message_id'=>$mid,
            'text'=>"<b>Tilni tanlang✅:</b>",
            'parse_mode'=>'html',
            'reply_markup'=>$languages,
        ]);
    }else if($data !== "start"){
        $upd = "UPDATE tableOfOtabek SET menu='{$data}' WHERE fromid='{$cbid}'";
        $query = mysqli_query($conn,$upd);
        bot('editMessageText',[
            'chat_id'=>$cbid,
            'message_id'=>$mid,
            'text'=>"*Tarjima uchun matn yuboring!*",
            'parse_mode'=>"markdown",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"🔙 Ortga", 'callback_data'=>"back"],],
                ],
            ]),
        ]);
    }
}

if ($_GET['send']) {
    $get = file_get_contents("step/admin.step");
    if ($get == "2") {
        $default = file_get_contents("step/admin.default");
        $after = $default + '60';

        $slt = "SELECT * FROM tableOfOtabek WHERE id>='{$default}' AND id<='{$after}'";
        $query = mysqli_query($conn,$slt);
        if (mysqli_num_rows($query)>0) {
            $from_id = file_get_contents("step/admin.for_id");
            $for_mid = file_get_contents("step/admin.for_mid");
            foreach ($query as $key => $value) {
                if ($value["fromid"] == $admin){}
                else {
                    bot('copyMessage',[
                        'chat_id'=>$value["fromid"],
                        'from_chat_id'=>$from_id,
                        'message_id'=>$for_mid
                    ]);
                }

            }
            file_put_contents("step/admin.default", $after);
        }else{
            bot('sendMessage',[
                'chat_id'=>$admin,
                'text'=>"Yuborish yakunlandi!",
                'reply_markup'=>$sendADS,
            ]);
            array_map( 'unlink', array_filter((array) glob("step/*")));
        }
    }
}

if ($_GET['weather']) {
    $getAPI = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=Uzbekistan&appid=351ccba9bd91ef24823bed7cf66380b8");
    $jd = json_decode($getAPI);

    
    $temp = $jd->main->temp_max - "273.15";
    $tempMin =  $jd->main->temp_min  - "273.15";
    $shamol = $jd->wind->speed;
    $davlat = $jd->sys->country;

    $info_w =  "<b>📊 O'zbekistonda bugun kutulayotgan ob-havo ma'lumotlari:</b>  \n\n <b>📍 Joylashuv:</b> "  . $davlat . " \n <b>🌡 Havo harorati:</b> " . $temp . "° \n <b>🌡 Eng past havo harorati:</b> " . $tempMin . "° \n <b>💨 Shamol Tezligi</b> " . $shamol . " m/s  ";
    $default = '0';
    $after = $default + '60';

    $slt = "SELECT * FROM tableOfOtabek WHERE id>='{$default}' AND id<='{$after}'";
    $query = mysqli_query($conn,$slt);
    if (mysqli_num_rows($query)>0) {
        foreach ($query as $key => $value) {
            if ($value["fromid"] == $admin){
                bot('sendMessage',[
                    'chat_id'=>$value["fromid"],
                    'text'=>$info_w,
                    'parse_mode'=>"html"
                ]);
            }else {
                bot('sendMessage',[
                    'chat_id'=>$value["fromid"],
                    'text'=>$info_w,
                    'parse_mode'=>"html"
                ]);
            }

        }
        $default += $after;
    }else{
        bot('sendMessage',[
            'chat_id'=>$admin,
            'text'=>"Ob havo yuborildi!",
            'parse_mode'=>"html"
        ]);
    }
}

if (($update->my_chat_member and $update->my_chat_member->new_chat_member->status == "kicked") || ($update->my_chat_member->new_chat_member->status == "left")) {
    $id = $update->my_chat_member->chat->id;
    $slt = "UPDATE tableOfOtabek SET del = '1' WHERE fromid = '{$id}'";
    $slt = "UPDATE tableOfOtabek SET menu = '' WHERE fromid = '{$id}'";
    $query = mysqli_query($conn,$slt);
}
?>