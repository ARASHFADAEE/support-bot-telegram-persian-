<?php

error_reporting(0);

define('API',"5767501871:AAEKLYy4RrYSCyIgb2AxnNZkhEwGfNjdelI"); 

function SendMessage($ci, $text, $message_id, $reply){
	$data  = "https://api.telegram.org/bot".API."/SendMessage?";
	$data .= "chat_id=".$ci."&text=".urlencode($text)."&reply_to_message_id=".$message_id."&reply_markup=".$reply;
	file_get_contents($data);
}
function sendDocument($method, $data=[]){
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot".API."/".$method);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       $res = curl_exec($ch);
       curl_close($ch);
}
function EditMessageText($ci, $text, $message_id, $reply){
	$data  = "https://api.telegram.org/bot".API."/EditMessageText?";
	$data .= "chat_id=".$ci."&text=".urlencode($text)."&message_id=".$message_id."&reply_markup=".$reply;
	file_get_contents($data);
}
function GetFile($id){
	$data  = "https://api.telegram.org/bot".API."/GetFile?";
	$data .= "file_id=".$id;
	return file_get_contents($data);
}
function is_admin($id){
	global $sudo;
	return in_array($id, $sudo);
}
function zarin($price, $from){
$MerchantID = 'مرچند'; //Required
$Amount = $price; // Amount will be based on Toman - Required
$Description = 'شارژ حساب برای فروشگاه'; // Required
$Email = 'hoseinfd2002@gmail.com'; // Optional
$Mobile = '09140065379'; // Optional
$CallbackURL = "http://arashfadaee.ir/shop/verify.php?pay=".$price."&from=".$from; // Required


$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

$result = $client->PaymentRequest(
[
'MerchantID' => $MerchantID,
'Amount' => $Amount,
'Description' => $Description,
'Email' => $Email,
'Mobile' => $Mobile,
'CallbackURL' => $CallbackURL,
]
);

//Redirect to URL You can do it also by creating a form
if ($result->Status == 100) {
return ('https://www.zarinpal.com/pg/StartPay/'.$result->Authority);
//برای استفاده از زرین گیت باید ادرس به صورت زیر تغییر کند:
//Header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority.'/ZarinGate');
} else {
return 'ERR: '.$result->Status;
   }
   
}
function Receipt($length = 5){
 $am = array_merge(range('A','Z'), range('a','z'), range(0, 9));
 for(; @$c < $length; @++$c)
  @$o .= $am[array_rand($am)];
 return $o;
}
function Upload($id,$dir){
    $api = GetFile($id);
    $js = json_decode($api);
    $path = $js->result->file_path;
    $link = "https://api.telegram.org/file/bot".API."/".$path;
    file_put_contents($dir, file_get_contents($link));
}
function deleteFolder($path){
  if(function_exists('exec')){
    exec("rm -rf $path");
    return !(is_file($path) || is_dir($path));
  }else{
    error_reporting(-1);
    if(rmdir($path) || unlink($path))
      return true;
    else{
      $glb = glob($path.'/*');
      foreach($glb as $value){
        if(is_dir($value))
          deleteFolder($value);
        else if(is_file($value))
          unlink($value);
      }
      return rmdir($path);
    }
  }
}
//Keyboards
$keys = json_encode(['keyboard'=>[
[['text'=>"نمایش مشاوره ها"]],
[['text'=>"حساب کاربری"],['text'=>"شارژ حساب"]]
],'resize_keyboard'=>true]);
$back = json_encode(['keyboard'=>[
[['text'=>"برگشت"]]
],'resize_keyboard'=>true]);
$exit = json_encode(['keyboard'=>[
[['text'=>"خروج"]]
],'resize_keyboard'=>true]);
$share = json_encode(['keyboard'=>[
[['text'=>"اشتراک شماره",'request_contact'=>true]],
[['text'=>"برگشت"]]
],'resize_keyboard'=>true]);
$panel = json_encode(['keyboard'=>[
[['text'=>"آمار"],['text'=>"محصولات"]],
[['text'=>"افزودن محصول"],['text'=>"حذف محصول"]]
],'resize_keyboard'=>true]);
//Updates
$up = json_decode(file_get_contents('php://input'));
$message = $up->message;
$text = $message->text;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$from_id = $message->from->id;
@$contact = $message->contact;
@$phoneNumber = $message->contact->phone_number;
@$userId = $message->contact->user_id;
@$doc = $message->document;
@$file_id = $doc->file_id;
@$file_name = $doc->file_name;
//Data Callback
$cal = $up->callback_query;
$Data = $cal->data;
$fromid = $cal->from->id;
$chatid = $cal->message->chat->id;
$messageid = $cal->message->message_id;
//Sudo 
$sudo = [859469070]; //ایدی عددی ادمین
$admin = "@arashfdadaee";
//Receipt
$rec = Receipt();
//Check File & Folder
if(!is_file("users.txt")){touch("users.txt");}
if(!is_dir("data")){mkdir("data");}
//Check User
@$user = file_get_contents("users.txt");
$users = explode("\n",$user);
@$last = file_get_contents("data/$from_id/last.txt");
@$coin = file_get_contents("data/$from_id/price.txt");
@$phone = file_get_contents("data/$from_id/phone.txt");
@$step = file_get_contents("data/$from_id/step.txt");
@$cat = file_get_contents("data/$from_id/receipt.txt");
//Start
if(preg_match('/^\/(start)$/i',$text)){
	if(!in_array($from_id,$users)){
		$f = fopen('users.txt','a');
		fwrite($f,"$from_id\n");
		fclose($f);
		mkdir("data/$from_id");
		file_put_contents("data/$from_id/last.txt","");
		file_put_contents("data/$from_id/price.txt",0);
		file_put_contents("data/$from_id/step.txt","none");
		file_put_contents("data/$from_id/phone.txt","");
   }
   $answer = "سلام به ربات مشاوره ایران  آوسبیلدونگ خوش امدید ";
   SendMessage($chat_id,$answer,$message_id,$keys);
}
//Back
elseif($text == "برگشت"){
	$answer = "به منوی اصلی خوش امدید.";
	SendMessage($chat_id,$answer,$message_id,$keys);
	file_put_contents("data/$from_id/step.txt","none");
}
//Account
elseif($text == "حساب کاربری"){
	$answer = "حساب کاربری شما باز شد :\nپول شما : $coin تومان\nشناسه کاربری : $from_id";
	SendMessage($chat_id,$answer,$message_id,$keys);
}
//Charge
elseif($text == "شارژ حساب"){
	if($phone == ""){
		$answer = "لطفا حساب خود را تایید کنید به دلیل مسائل امنیتی فقط با دکمه زیر";
		SendMessage($chat_id,$answer,$message_id,$share);
		file_put_contents("data/$from_id/step.txt","share");
     }else{
        $answer = "لطفا مبلغی که میخواهید حساب خود را شارژ کنید را به تومان وارد کنید\nمثال : 10000";
        SendMessage($chat_id,$answer,$message_id,$back);
        file_put_contents("data/$from_id/step.txt","charge");
   }
}
elseif($step == "share" && isset($contact)){
      if(preg_match('/^(?:98|\+98|0098|0)?9[0-9]{9}$/',$phoneNumber)){
         if($from_id == $userId){
       $answer = "شماره شما تایید شد مجدد درخواست شارژ حساب کنید.";
       SendMessage($chat_id,$answer,$message_id,$keys);
	   file_put_contents("data/$from_id/step.txt","none");
	   file_put_contents("data/$from_id/phone.txt","+".$phoneNumber);   
   }else{
       $answer = "لطفا از شماره تلفن خود استفاده کنید نه شخص دیگری";
       SendMessage($chat_id,$answer,$message_id,$share);  
  }
 }else{       
      $answer = "لطفا فقط از شماره کشور ایران استفاده کنید: 98";
       SendMessage($chat_id,$answer,$message_id,$share);
 }
}
elseif($step == "charge"){
      if($text >= 10000 && $text <= 100000){
        SendMessage($chat_id,"در حال ساخت لینک پرداخت برای شما صبور باشید....",$message_id,null);
         $link = zarin($text,$from_id);
         $charge = json_encode(['inline_keyboard'=>[
         [['text'=>"پرداخت کنید",'url'=>$link]]]]);       
         $answer = "لینک پرداخت شما ساخته شد با دکمه زیر پرداخت کنید";
         SendMessage($chat_id,$answer,$message_id,$charge);
         file_put_contents("data/$from_id/step.txt","none");
   }else{
         $answer = "لطفا مبلغ را بین 10000 تومان و 100000 هزار تومان وارد کنید";
         SendMessage($chat_id,$answer,$message_id,$back);
 }
}
//Last 
elseif($text == "لیست مشاوره ها"){
        $answer = "هنوز هیچ مشاوره ای نداشتید.";
        SendMessage($chat_id,$answer,$message_id,$keys);
}
//Product
elseif($text == "نمایش مشاوره ها"){
     $dir = array_diff(scandir('category/'),['.','..']);
     if(count($dir) > 0){
     foreach($dir as $value){
        $get = file_get_contents("category/$value/name.txt");
        $key[] = [['text'=>$get,'callback_data'=>"id_$value"]];
    }
    $answer = "لیست محصولات فروشگاه آی بوک";
    SendMessage($chat_id,$answer,$message_id, json_encode(['inline_keyboard'=>$key]));
}else{
    $answer = "مشاوره ای برای نمایش وجود ندارد";
    SendMessage($chat_id,$answer,$message_id, $keys);
}
}
elseif(preg_match('/^(id_)(.*)/si',$Data)){
      preg_match('/^(id_)(.*)/si',$Data,$match);
      $info = file_get_contents("category/".$match[2]."/info.txt");
      $price = file_get_contents("category/".$match[2]."/price.txt");
      $name = file_get_contents("category/".$match[2]."/name.txt");
      $id = $match[2];
      $answer = "📍محصول : $name\n\n 📖 درباره محصول :\n $info\n\n💰 قیمت محصول : $price\n\n 💳 خرید محصول : /exec_$id";
      EditMessageText($chatid,$answer,$messageid,null);
}
elseif(preg_match('/^\/(exec_)(.*)/si',$text)){
      preg_match('/^\/(exec_)(.*)/si',$text,$match);
      if(is_dir("category/".$match[2])){
         $price = file_get_contents("category/".$match[2]."/price.txt");
         $fnm = file_get_contents("category/".$match[2]."/file_name.txt");         
         if($price <= $coin){
          $negative = $coin - $price;
          $answer = "ممنون بابت خرید شما فایل اندکی دیگر ارسال میشود....\n درصورت ارسال نشدن با آیدی @$admin تماس حاصل فرمایید";
          SendMessage($chat_id,$answer,$message_id,null);
          sendDocument('sendDocument',['chat_id'=>$chat_id, 'document'=> new CURLFILE("category/".$match[2]."/file/".$fnm), 'caption'=>"😄 اینم فایل شما تشکر بابت خرید شما"]);
          file_put_contents("data/$from_id/price.txt", $negative);
    }else{
          $answer = "مبلغ شما کم تر از قیمت محصول است.";
          SendMessage($chat_id,$answer,$message_id,null);
     }
    }else{
          $answer = "چنین محصولی موجود نیست!";
          SendMessage($chat_id,$answer,$message_id,null);
  }
}
//Manager
elseif(preg_match('/^[\/]?(panel|پنل|خروج)$/ui',$text) && is_admin($from_id)){
	$answer = "سلام ادمین به پنل مدیریت خوش اومدی.";
	SendMessage($chat_id,$answer,$message_id,$panel);
	file_put_contents("data/$from_id/step.txt","none");
}
elseif($text == "آمار" && is_admin($from_id)){
	$usr = explode("\n",$user);
	$cUser = count($usr)-1;
	$answer = "آمار کاربران با احتساب خودتان $cUser نفر است.";
	SendMessage($chat_id,$answer,$message_id,$panel);
}
elseif($text == "افزودن محصول" && is_admin($from_id)){
    $answer = "لطفا نام محصول خود را وارد کنید:\nاین نام به جای دکمه استفاده میشود و باید کمتر از 30 کاراکتر باشد\nمثال : سورس ربات ساز";
    SendMessage($chat_id,$answer,$message_id,$exit);
    file_put_contents("data/$from_id/step.txt","product_name");
}
elseif($step == "product_name" && isset($text)){
    if(strlen($text) <= 30){
         $answer = "خب حالا قیمت محصول خود را وارد کنید به تومان مثال : 20000";
         SendMessage($chat_id,$answer,$message_id,$exit);
         @mkdir("category/$rec");
         file_put_contents("data/$from_id/step.txt","product_price");
         file_put_contents("data/$from_id/receipt.txt","$rec");
         file_put_contents("category/$rec/name.txt","$text");
    }else{
         $answer = "طول نام بیشتر از 30 کاراکتر است!";
         SendMessage($chat_id,$answer,$message_id,$exit);
  }
}
elseif($step == "product_price" && isset($text)){
    if(is_numeric($text)){
         $answer = "خب حالا توضیحاتی در مورد محصول خود وارد کنید...";
         SendMessage($chat_id,$answer,$message_id,$exit);
         file_put_contents("category/$cat/price.txt","$text");
         file_put_contents("data/$from_id/step.txt","product_info");
    }else{
         $answer = "لطفا مبلغ را به عدد انگلیسی و به تومان وارد کنید.";
         SendMessage($chat_id,$answer,$message_id,$exit);
  }
}
elseif($step == "product_info" && isset($text)){
         $answer = "رسیدیم به آخرین مرحله لطفا فایل خود را ارسال کنید...";
         SendMessage($chat_id,$answer,$message_id,$exit);
         file_put_contents("category/$cat/info.txt","$text");
         file_put_contents("data/$from_id/step.txt","product_file");
}
elseif($step == "product_file" && isset($doc)){         
         SendMessage($chat_id,"درحال آپلود فایل...",$message_id,$exit);
         @mkdir("category/$cat/file");  
         file_put_contents("category/$cat/file_name.txt","$file_name");
         Upload($file_id,"category/$cat/file/$file_name");
         $answer = "محصول شما با موفقیت به لیست محصولات اضافه گردید...";
         SendMessage($chat_id,$answer,$message_id,$exit);              
         file_put_contents("data/$from_id/step.txt","none");
         file_put_contents("data/$from_id/receipt.txt","");
 }
elseif($text == "محصولات" && is_admin($from_id)){
    $dir = scandir("category");
    $diff = array_diff($dir, ['.','..']);
    foreach($diff as $value){
       $name = file_get_contents("category/".$value."/name.txt");
       $answer .= "📖 نام محصول : $name \n 🎫 کد محصول : $value\n➖➖➖➖➖➖➖➖➖\n";
    }
       SendMessage($chat_id,"📍لیست محصولات شما :\n\n".$answer,$message_id,$panel);    
}
elseif($text == "حذف محصول" && is_admin($from_id)){
      $answer = "کد محصولی که میخواهید حذف شود را ارسال میتوانید این کد را از پنل مدیریت بخش محصولات دریافت کنید";
      SendMessage($chat_id,$answer,$message_id,$exit);
      file_put_contents("data/$from_id/step.txt","delete");
}
elseif($step == "delete"){
    if(is_dir("category/".$text)){ 
        SendMessage($chat_id,"درحال انجام عملیات حذف....",$message_id,$exit);
        deleteFolder("category/".$text);
        $answer = "محصول با کد $text حذف گردید";
        SendMessage($chat_id,$answer,$message_id,$exit);
        file_put_contents("data/$from_id/step.txt","none");
    }else{
        $answer = "چنین محصولی با کد $text یافت نشد.";
        SendMessage($chat_id,$answer,$message_id,$exit);
  }
}

?>