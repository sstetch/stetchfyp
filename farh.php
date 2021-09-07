<?php

ob_start();

define('API_KEY','1885906774:AAFj4-bqgBshOB2oOSLI7HQUnu8bAJ97jH0');
echo file_get_contents("https://api.telegram.org/bot" . API_KEY . "/setwebhook?url=" . $_SERVER['SERVER_NAME'] . "" . $_SERVER['SCRIPT_NAME']);
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$settings = json_decode(file_get_contents("settings.json"),true);
$chat_id = $update->message->chat->id;
$from_id = $update->message->from->id;
$text = $update->message->text;
$type = $update->message->chat->type;
$re = $update->message->reply_to_message;
$is_user  = $message->from->username;
$is_name  = $message->from->first_name;
$from_stn = $settings[$chat_id][$from_id];
$reply_id = $message->reply_to_message->from->id;
$reply_user  = $message->reply_to_message->from->username;
$reply_name  = $message->reply_to_message->from->first_name;
$reply_message_id = $message->reply_to_message->message_id;
$from_stnInfo = json_decode(file_get_contents("http://api.telegram.org/bot".API_KEY."/getChat?chat_id=$from_stn"));
$from_stnUser = $from_stnInfo->result->username;
$from_stnName = $from_stnInfo->result->first_name;
$reply_stn = $settings[$chat_id][$reply_id];
$reply_stnInfo = json_decode(file_get_contents("http://api.telegram.org/bot".API_KEY."/getChat?chat_id=$reply_stn"));
$reply_stnUser = $reply_stnInfo->result->username;
$reply_stnName = $reply_stnInfo->result->first_name;
if($text == "/start"  && $type == "private"){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>
"- 📮꒐ أهلا بك في بـوت الزوج الخاص بالمجموعات .
- 📮꒐ اكتب الزواج لمعرفه اومر البوت",
'reply_to_message_id'=>$message->message_id,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>"اضفني الى مجموعتك", 'url'=>"http://t.me/$usernamebot?startgroup=new"]],
]])
]);   
}
if($re and $text == "تقدم" and $settings[$chat_id][$reply_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"الاسم : " .$reply_name."
المعرف : @".$reply_user."
متزوج بالفعل",
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "تقدم" and !$settings[$chat_id][$reply_id]){
$settings[$chat_id][$from_id] = $reply_id;
$settings[$chat_id][$reply_id] = $from_id;
$settings['test'][$chat_id][$reply_id] = $reply_id;
file_put_contents("settings.json",json_encode($settings));
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"الاسم : ".$is_name."
المعرف : @".$is_user."
يريد الزواج منك هل تريد الموافقه ام الرفض
قم برد عليه بكلمه موافق لاتمام الزواج او رد عليه برفض ليتم الغاء الزواج",
'reply_to_message_id'=>$reply_message_id,
]);
}
if($re and $text == "موافق" and $settings['test'][$chat_id][$from_id]){
$settings[$chat_id][$from_id] = $reply_id;
$settings[$chat_id][$reply_id] = $from_id;
unset($settings['test'][$chat_id][$reply_id]);
file_put_contents("settings.json",json_encode($settings));
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>
"الاسم : ".$reply_stnName."
المعرف : @".$reply_stnUser."
اصبح الان زوج  من 
الاسم : ".$from_stnName."
المعرف : @".$from_stnUser,
]);
}
if($text == "زوجي" or $text == "زوجتي" and !$settings[$chat_id][$from_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"انت سايق مخده سنكل",
'reply_to_message_id'=>$message_id,
]);
}
if($text == "زوجي" or $text == "زوجتي" and $settings[$chat_id][$from_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
اسم زوجك : ".$from_stnName."
معرف زوجك : @".$from_stnUser,
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "رفض" and $settings['test'][$chat_id][$from_id]){
unset($settings[$chat_id][$from_id]);
unset($settings[$chat_id][$reply_id]);
file_put_contents("settings.json",json_encode($settings));
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>
"الاسم : ".$reply_stnName."
المعرف : @".$reply_stnUser."
قام برفض الزواج من 
الاسم : ".$from_stnName."
المعرف : @".$from_stnUser,
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "طلاك" and $settings[$chat_id][$reply_id] and $from_id == $settings[$chat_id][$reply_id]){
unset($settings[$chat_id][$from_id]);
unset($settings[$chat_id][$reply_id]);
file_put_contents("settings.json",json_encode($settings));
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"تم انفصالكم بنجاح",
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "طلاك" and $settings[$chat_id][$reply_id] and $from_id != $settings[$chat_id][$reply_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"دنجب انت اخت العروس ام العريس",
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "زوج" and $settings[$chat_id][$reply_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"زوج ".$reply_name."
هو ".$reply_stnName,
'reply_to_message_id'=>$message_id,
]);
}
if($re and $text == "زوج" and !$settings[$chat_id][$reply_id]){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"غير متزوج",
'reply_to_message_id'=>$message_id,
]);
}
if($text == "الزواج"){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"قم بالرد على الشخص الذي تريد طلب الزواج منه بكلمه 'تقدم'
حين يقوم بالقبول وارسال كلمة 'موافق' بالرد عليك ستصبحون زوجين.
أذا اردتم الأنفصال فقم بالرد على زوجك بكلمة 'طلاك'.
أرسل 'زوجي' أو 'زوجتي' أذا كنت تريد أظهار الزوج.
لمعرفه زوج  صدقك قم برد عليه بكلمه 'زوج'",
'reply_to_message_id'=>$message_id,
]);
}