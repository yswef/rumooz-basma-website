<?php
require __DIR__.'/inc.php';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);session_start();
$T=$_SESSION['t']??=bin2hex(random_bytes(16));$F="<input type=hidden name=t value=$T>";
function put($k,$v){global $db;$db->prepare('REPLACE INTO s VALUES(?,?)')->execute([$k,$v]);}
function pg($b){echo "<!doctype html><html lang=ar dir=rtl><meta charset=utf-8><meta name=viewport content='width=device-width,initial-scale=1'><title>لوحة التحكم</title><link rel=stylesheet href=assets/s.css><link rel=stylesheet href='https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap'><body class=adm><main class=w>$b</main>";exit;}
function up($f){if(!isset($f['tmp_name'])||$f['error'])return;$i=@getimagesize($f['tmp_name']);$x=[IMAGETYPE_JPEG=>'jpg',IMAGETYPE_PNG=>'png',IMAGETYPE_WEBP=>'webp'][$i[2]??0]??0;if(!$x||$f['size']>5e6)return;$n='uploads/'.bin2hex(random_bytes(6)).".$x";move_uploaded_file($f['tmp_name'],R."/$n");return $n;}
$p=$_POST;if($p&&!hash_equals($T,$p['t']??''))pg('انتهت الجلسة، أعد تحميل الصفحة');
$hash=$db->query("SELECT v FROM s WHERE k='admin'")->fetchColumn();
if(!$hash){if(strlen($p['pw']??'')>=8){put('admin',password_hash($p['pw'],PASSWORD_DEFAULT));$_SESSION['in']=1;header('Location: admin.php');exit;}pg("<h1>إنشاء كلمة مرور المدير</h1><form method=post>$F<input type=password name=pw minlength=8 placeholder='8 أحرف على الأقل' required><button class=btn>حفظ</button></form>");}
if(!isset($_SESSION['in'])){if(isset($p['pw'])){if(($_SESSION['lock']??0)>time())pg('محاولات كثيرة، انتظر 5 دقائق');
if(password_verify($p['pw'],$hash)){session_regenerate_id(true);$_SESSION['in']=1;header('Location: admin.php');exit;}
$_SESSION['f']=($_SESSION['f']??0)+1;if($_SESSION['f']>=5){$_SESSION['lock']=time()+300;$_SESSION['f']=0;}sleep(1);}
pg("<h1>دخول المدير</h1><form method=post>$F<input type=password name=pw placeholder='كلمة المرور' required><button class=btn>دخول</button></form>");}
if(($_GET['a']??'')=='out'){session_destroy();header('Location: admin.php');exit;}
$C=['services'=>['title'=>'العنوان','slug'=>'الرابط (إنجليزي)','body'=>'الوصف','feat'=>'المميزات (كل ميزة في سطر)','kw'=>'كلمات البحث'],'projects'=>['title'=>'العنوان','city'=>'المدينة','cat'=>'التصنيف (دهانات، ترميم، ديكور، جبس، عزل، مطابخ…)']];
if($p){if(isset($p['set'])){foreach(['name','phone','wa','h1','h1b','sub','about','cities','gsc','ga'] as $k)put($k,trim($p[$k]??''));}
elseif(isset($C[$p['tb']??''])){$tb=$p['tb'];$id=(int)$p['id'];
if(isset($p['del']))$db->prepare("DELETE FROM $tb WHERE id=?")->execute([$id]);
else{$v=[];foreach($C[$tb] as $k=>$_)$v[$k]=trim($p[$k]??'');if(isset($v['slug']))$v['slug']=preg_replace('/[^a-z0-9-]/','',strtolower($v['slug']))?:'s'.time();
$im=up($_FILES['img']??[]);if($im)$v['img']=$im;
if($id)$db->prepare("UPDATE $tb SET ".implode(',',array_map(fn($k)=>"$k=?",array_keys($v))).' WHERE id=?')->execute([...array_values($v),$id]);
else{$v['img']=$v['img']??'';$db->prepare("INSERT INTO $tb(".implode(',',array_keys($v)).') VALUES('.implode(',',array_fill(0,count($v),'?')).')')->execute(array_values($v));}}}
header('Location: '.$_SERVER['REQUEST_URI']);exit;}
$t=$_GET['t']??'set';$o="<nav class=tabs><a href='admin.php?t=set'>الإعدادات</a><a href='admin.php?t=services'>الخدمات</a><a href='admin.php?t=projects'>الأعمال</a><a href='admin.php?t=leads'>الطلبات</a><a href='admin.php?a=out'>خروج</a></nav>";
if($t=='set'){$o.="<form method=post>$F<input type=hidden name=set value=1>";foreach(['name'=>'اسم المؤسسة','phone'=>'رقم الواتساب (بدون +)','wa'=>'رسالة الترحيب في واتساب','h1'=>'عنوان الصفحة الرئيسية','h1b'=>'السطر الملوّن تحت العنوان','sub'=>'الوصف تحت العنوان','about'=>'من نحن','cities'=>'مناطق الخدمة (افصل بفاصلة ،)','gsc'=>'كود التحقق من Google Search Console','ga'=>'معرّف Google Analytics (G-XXXX)'] as $k=>$l)$o.="<label>$l</label>".($k=='about'||$k=='wa'?"<textarea name=$k>".e(S($k))."</textarea>":"<input name=$k value='".e(S($k))."'>");$o.="<button class=btn>حفظ</button></form>";}
elseif(isset($C[$t])){foreach(array_merge($db->query("SELECT * FROM $t")->fetchAll(),[['id'=>0,'img'=>'']]) as $r){$o.="<form method=post enctype=multipart/form-data class=row>$F<input type=hidden name=tb value=$t><input type=hidden name=id value={$r['id']}>".($r['id']?($r['img']&&strpos($r['img'],'ph/')!==0?"<img src='".e($r['img'])."' width=70>":'<small>صورة افتراضية</small>'):'<b>+ إضافة جديد</b>');
foreach($C[$t] as $k=>$l)$o.=($k=='body'||$k=='feat')?"<textarea name=$k placeholder='$l'>".e($r[$k]??'')."</textarea>":"<input name=$k placeholder='$l' value='".e($r[$k]??'')."'>";
$o.="<input type=file name=img accept='image/*'><button class=btn>حفظ</button>".($r['id']?"<button name=del value=1 class='btn d' onclick=\"return confirm('حذف؟')\">حذف</button>":'')."</form>";}}
else{$o.='<table>';foreach($db->query('SELECT * FROM leads ORDER BY id DESC LIMIT 200') as $r)$o.='<tr><td>'.e($r['name']).'</td><td><a href="https://wa.me/'.preg_replace('/\D/','',$r['phone']).'">'.e($r['phone']).'</a></td><td>'.e($r['msg']).'</td><td>'.e($r['t']).'</td></tr>';$o.='</table>';}
pg($o);
