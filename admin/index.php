<?php
/* لوحة تحكم رموز بسمة — تحليلات، زوار، طلبات، أعمال، وسائط، منشئ صفحات، إعدادات */
require __DIR__.'/../inc.php';
$base=rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])),'/\\');
session_name('rbs');session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);session_start();
const ALLOWED_IMG=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
function pw_hash(){global $db;return $db->query("SELECT v FROM s WHERE k='admin_pw'")->fetchColumn();}
function put($k,$v){global $db;$db->prepare('INSERT OR REPLACE INTO s VALUES(?,?)')->execute([$k,$v]);}
function go($u){header('Location: '.$u);exit;}
function flash($m,$t='ok'){$_SESSION['fl']=[$t,$m];}
function csrf(){return $_SESSION['csrf']??($_SESSION['csrf']=bin2hex(random_bytes(16)));}
function chk(){if(!hash_equals(csrf(),$_POST['_c']??''))die('CSRF');}
function ago($t){$d=time()-$t;return $d<60?'الآن':($d<3600?floor($d/60).' د':($d<86400?floor($d/3600).' س':floor($d/86400).' يوم'));}
function ci($ip){global $db;static $c=[];if(!isset($c[$ip])){$r=$db->prepare('SELECT * FROM geo WHERE ip=?');$r->execute([$ip]);$c[$ip]=$r->fetch()?:[];}return $c[$ip];}
function loc($ip,$cc=''){$g=ci($ip);$c=$g['cc']??$cc;$n=trim(($g['country']??'').(($g['city']??'')?' — '.$g['city']:''));return flag($c).' '.($n?:($cc?:'—'));}
function slugify($s){$s=strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));return $s?:'page-'.time();}
function upload_img($f){global $db;if(($f['error']??1)!==0||$f['size']>12*1048576)return null;$mi=mime_content_type($f['tmp_name']);if(!isset(ALLOWED_IMG[$mi]))return null;
$src=match($mi){'image/jpeg'=>@imagecreatefromjpeg($f['tmp_name']),'image/png'=>@imagecreatefrompng($f['tmp_name']),'image/webp'=>@imagecreatefromwebp($f['tmp_name']),default=>@imagecreatefromgif($f['tmp_name'])};if(!$src)return null;
$w=imagesx($src);$h=imagesy($src);if($mi=='image/jpeg'&&function_exists('exif_read_data')){$ex=@exif_read_data($f['tmp_name']);$o=$ex['Orientation']??1;if($o==3)$src=imagerotate($src,180,0);elseif($o==6){$src=imagerotate($src,-90,0);[$w,$h]=[$h,$w];}elseif($o==8){$src=imagerotate($src,90,0);[$w,$h]=[$h,$w];}}
$mk=function($max,$q,$path)use($src,$w,$h){$s=min(1,$max/max($w,$h));$nw=max(1,(int)round($w*$s));$nh=max(1,(int)round($h*$s));$d=imagecreatetruecolor($nw,$nh);imagealphablending($d,false);imagesavealpha($d,true);imagefill($d,0,0,imagecolorallocatealpha($d,255,255,255,127));imagecopyresampled($d,$src,0,0,0,0,$nw,$nh,$w,$h);$ok=function_exists('imagewebp')?imagewebp($d,$path,$q):imagejpeg($d,$path,$q);imagedestroy($d);return $ok;};
$ext=function_exists('imagewebp')?'webp':'jpg';$id=date('ymd').'-'.bin2hex(random_bytes(4));$file="uploads/$id.$ext";$th="uploads/t-$id.$ext";
if(!$mk(1800,82,R."/$file")||!$mk(520,76,R."/$th"))return null;imagedestroy($src);
$db->prepare('INSERT INTO media(file,thumb,name,w,h,size,t) VALUES(?,?,?,?,?,?,?)')->execute([$file,$th,mb_substr(preg_replace('/\.[^.]+$/','',$f['name']),0,80),$w,$h,filesize(R."/$file"),time()]);return $file;}
function rng(){$r=(int)($_GET['d']??30);return in_array($r,[1,7,30,90,365])?$r:30;}
function q1($sql,$a=[]){global $db;$s=$db->prepare($sql);$s->execute($a);return $s->fetchColumn();}
function qa($sql,$a=[]){global $db;$s=$db->prepare($sql);$s->execute($a);return $s->fetchAll();}
function pager($tot,$per,$pg){$n=max(1,ceil($tot/$per));if($n<2)return'';$q=$_GET;$o='<div class=pgn>';for($i=1;$i<=$n;$i++){if($i==1||$i==$n||abs($i-$pg)<3){$q['pg']=$i;$o.='<a'.($i==$pg?' class=on':'').' href="?'.e(http_build_query($q)).'">'.$i.'</a>';}elseif(abs($i-$pg)==3)$o.='<span>…</span>';}return $o.'</div>';}

/* ===== المصادقة ===== */
$P=$_GET['p']??'dash';if($P=='content')$P='settings';$first=!pw_hash();$ip=client_ip();$ua=mb_substr($_SERVER['HTTP_USER_AGENT']??'',0,300);
if(isset($_GET['out'])){session_destroy();setcookie('adm','',time()-3600,'/');go('index.php');}
$uok=fn($u)=>(bool)preg_match('/^[A-Za-z0-9_.\-]{3,30}$/',$u);
if($first&&$_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['setup'])){if(!$uok(trim($_POST['user']??'')))$err='اسم المستخدم 3-30 حرفاً (إنجليزي أو أرقام أو _ . -)';elseif(strlen($_POST['pw']??'')>=8){put('admin_user',strtolower(trim($_POST['user'])));put('admin_pw',password_hash($_POST['pw'],PASSWORD_DEFAULT));$_SESSION['ok']=1;$_SESSION['t']=time();setcookie('adm','1',time()+31536000,'/',"",false,true);go('index.php');}else $err='كلمة المرور يجب أن تكون 8 أحرف على الأقل';}
if(!$first&&empty($_SESSION['ok'])&&$_SERVER['REQUEST_METHOD']=='POST'&&isset($_POST['login'])){
 $bad=q1("SELECT COUNT(*) FROM logins WHERE ip=? AND ok=0 AND t>?",[$ip,time()-900]);
 if($bad>=6)$err='محاولات كثيرة. حاول بعد 15 دقيقة.';
 else{$u=strtolower(trim($_POST['user']??''));$okU=hash_equals(strtolower(S('admin_user')?:'admin'),$u);$okP=password_verify($_POST['pw']??'',pw_hash());$cc=strtoupper(substr($_SERVER['HTTP_CF_IPCOUNTRY']??'',0,2));
  if($okU&&$okP){session_regenerate_id(true);$_SESSION['ok']=1;$_SESSION['t']=time();$db->prepare('INSERT INTO logins(t,ip,ua,ok,cc,usr) VALUES(?,?,?,1,?,?)')->execute([time(),$ip,$ua,$cc,mb_substr($u,0,40)]);setcookie('adm','1',time()+31536000,'/','',false,true);go('index.php');}
  else{$db->prepare('INSERT INTO logins(t,ip,ua,ok,cc,usr) VALUES(?,?,?,0,?,?)')->execute([time(),$ip,$ua,$cc,mb_substr($u,0,40)]);$err='اسم المستخدم أو كلمة المرور غير صحيحة';}}}
if(!empty($_SESSION['ok'])&&time()-($_SESSION['t']??0)>28800){session_destroy();go('index.php');}
if(empty($_SESSION['ok'])){?><!doctype html><html lang=ar dir=rtl><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><meta name=robots content=noindex><title>دخول — لوحة التحكم</title><link rel=stylesheet href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800;900&display=swap"><link rel=stylesheet href=admin.css></head><body class=lgn>
<div class=lbox><svg class=lgo style="color:#fff;--lt:#3fb2a9"><use href="<?=$base?>/assets/logo-sprite.svg#lg-full"/></svg><h1><?=$first?'إنشاء حساب المدير':'تسجيل الدخول'?></h1><p><?=$first?'أول تشغيل: اختر اسم مستخدم وكلمة مرور قوية (8 أحرف أو أكثر).':'لوحة تحكم '.e(S('name'))?></p>
<?php if(!empty($err))echo '<div class="al bad">'.e($err).'</div>';?><form method=post><input name=user placeholder="اسم المستخدم" autofocus required autocomplete="username" dir=ltr><input type=password name=pw placeholder="كلمة المرور" required autocomplete="<?=$first?'new-password':'current-password'?>"><button class=btn name="<?=$first?'setup':'login'?>"><?=$first?'إنشاء والدخول':'دخول'?></button></form><a class=bk href="<?=$base?>/">← العودة للموقع</a></div></body></html><?php exit;}

/* ===== إجراءات POST ===== */
$post=$_SERVER['REQUEST_METHOD']=='POST';
if($post){chk();$a=$_POST['a']??'';
 if($a=='cred'){$nu=strtolower(trim($_POST['user']??''));$np=$_POST['new']??'';if(!password_verify($_POST['old']??'',pw_hash()))flash('كلمة المرور الحالية غير صحيحة','bad');elseif($nu!==''&&!$uok($nu))flash('اسم المستخدم غير صالح','bad');elseif($np!==''&&strlen($np)<8)flash('كلمة المرور الجديدة قصيرة (8 أحرف)','bad');else{if($nu!=='')put('admin_user',$nu);if($np!=='')put('admin_pw',password_hash($np,PASSWORD_DEFAULT));flash('تم تحديث بيانات الدخول');}go('?p=security');}
 if($a=='set'){foreach($_POST['s']??[] as $k=>$v){if(preg_match('/^[a-z0-9_]+$/',$k)&&$k!='admin_pw')put($k,trim($v));}flash('تم حفظ الإعدادات');go('?p='.$P.(isset($_GET['t'])?'&t='.$_GET['t']:''));}
 if($a=='up'){$n=0;foreach($_FILES['f']['name']??[] as $i=>$_){$f=['name'=>$_FILES['f']['name'][$i],'tmp_name'=>$_FILES['f']['tmp_name'][$i],'size'=>$_FILES['f']['size'][$i],'error'=>$_FILES['f']['error'][$i]];if(upload_img($f))$n++;}
  if(isset($_POST['ajax'])){header('Content-Type:application/json');echo json_encode(['n'=>$n]);exit;}flash("تم رفع $n صورة");go('?p=media');}
 if($a=='mdel'){$r=qa('SELECT * FROM media WHERE id=?',[(int)$_POST['id']])[0]??null;if($r){@unlink(R.'/'.$r['file']);@unlink(R.'/'.$r['thumb']);$db->prepare('DELETE FROM media WHERE id=?')->execute([$r['id']]);foreach(['services','projects'] as $t)$db->prepare("UPDATE $t SET img='' WHERE img=?")->execute([$r['file']]);$db->prepare("UPDATE s SET v='' WHERE v=?")->execute([$r['file']]);flash('تم حذف الصورة');}go('?p=media');}
 if($a=='prow'){$id=(int)($_POST['id']??0);$v=[trim($_POST['title']),trim($_POST['city']),trim($_POST['img']),trim($_POST['cat']),(int)$_POST['sort'],isset($_POST['pub'])?1:0];
  if($id)$db->prepare('UPDATE projects SET title=?,city=?,img=?,cat=?,sort=?,pub=? WHERE id=?')->execute([...$v,$id]);else $db->prepare('INSERT INTO projects(title,city,img,cat,sort,pub) VALUES(?,?,?,?,?,?)')->execute($v);flash('تم حفظ العمل');go('?p=works');}
 if($a=='pdel'){$db->prepare('DELETE FROM projects WHERE id=?')->execute([(int)$_POST['id']]);flash('تم حذف العمل');go('?p=works');}
 if($a=='srow'){$id=(int)($_POST['id']??0);$v=[trim($_POST['title']),slugify($_POST['slug']?:$_POST['title']),trim($_POST['body']),trim($_POST['feat']),trim($_POST['kw']),trim($_POST['img']),(int)$_POST['sort']];
  if($id)$db->prepare('UPDATE services SET title=?,slug=?,body=?,feat=?,kw=?,img=?,sort=? WHERE id=?')->execute([...$v,$id]);else $db->prepare('INSERT INTO services(title,slug,body,feat,kw,img,sort) VALUES(?,?,?,?,?,?,?)')->execute($v);flash('تم حفظ الخدمة');go('?p=services');}
 if($a=='sdel'){$db->prepare('DELETE FROM services WHERE id=?')->execute([(int)$_POST['id']]);flash('تم حذف الخدمة');go('?p=services');}
 if($a=='lead'){$db->prepare('UPDATE leads SET status=?,note=? WHERE id=?')->execute([$_POST['status'],trim($_POST['note']),(int)$_POST['id']]);flash('تم التحديث');go('?p=leads'.(isset($_GET['f'])?'&f='.$_GET['f']:''));}
 if($a=='ldel'){$db->prepare('DELETE FROM leads WHERE id=?')->execute([(int)$_POST['id']]);flash('تم الحذف');go('?p=leads');}
 if($a=='page'){$id=(int)($_POST['id']??0);$bl=json_decode($_POST['blocks']??'[]',true);if(!is_array($bl))$bl=[];$slug=slugify($_POST['slug']?:$_POST['title']);
  $v=[trim($_POST['title']),$slug,json_encode($bl,JSON_UNESCAPED_UNICODE),isset($_POST['pub'])?1:0,isset($_POST['menu'])?1:0,json_encode(['t'=>trim($_POST['seo_t']??''),'d'=>trim($_POST['seo_d']??'')],JSON_UNESCAPED_UNICODE),time()];
  if($id){$db->prepare('UPDATE pages SET title=?,slug=?,blocks=?,pub=?,menu=?,seo=?,t=? WHERE id=?')->execute([...$v,$id]);}else{$db->prepare('INSERT INTO pages(title,slug,blocks,pub,menu,seo,t) VALUES(?,?,?,?,?,?,?)')->execute($v);$id=$db->lastInsertId();}
  flash('تم حفظ الصفحة');go('?p=pages&id='.$id);}
 if($a=='pgdel'){$db->prepare('DELETE FROM pages WHERE id=?')->execute([(int)$_POST['id']]);flash('تم حذف الصفحة');go('?p=pages');}
 if($a=='purge'){$d=(int)$_POST['days'];$t=time()-$d*86400;$db->prepare('DELETE FROM visits WHERE t<?')->execute([$t]);$db->prepare('DELETE FROM events WHERE t<?')->execute([$t]);flash('تم حذف بيانات الزيارات الأقدم من '.$d.' يوم');go('?p=security');}
 if($a=='geo'){geo_resolve(100);flash('تم تحديث المواقع الجغرافية');go($_POST['back']??'?p=visitors');}
}
if($P=='csv'){header('Content-Type:text/csv;charset=utf-8');header('Content-Disposition:attachment;filename=leads-'.date('Y-m-d').'.csv');echo "\xEF\xBB\xBF";$o=fopen('php://output','w');fputcsv($o,['التاريخ','الاسم','الجوال','الطلب','الحالة','ملاحظة']);foreach($db->query('SELECT * FROM leads ORDER BY id DESC') as $r)fputcsv($o,[$r['t'],$r['name'],$r['phone'],$r['msg'],$r['status'],$r['note']]);exit;}
if($P=='backup'&&isset($_GET['k'])&&hash_equals(csrf(),$_GET['k'])){$db->exec('PRAGMA wal_checkpoint(TRUNCATE)');header('Content-Type:application/octet-stream');header('Content-Disposition:attachment;filename=backup-'.date('Y-m-d').'.db');readfile(R.'/data/site.db');exit;}
if($P=='mj'){header('Content-Type:application/json');echo json_encode(qa('SELECT id,file,thumb,name FROM media ORDER BY id DESC LIMIT 300'));exit;}
$ICO=['dash'=>['chart','نظرة عامة'],'visitors'=>['users','الزوار'],'traffic'=>['globe','مصادر الزيارات'],'leads'=>['mail','الطلبات'],'works'=>['image','الأعمال'],'services'=>['grid','الخدمات'],'media'=>['layers','مكتبة الصور'],'pages'=>['file','الصفحات'],'settings'=>['sliders','الإعدادات'],'security'=>['lock','الأمان والنسخ']];
$newl=(int)q1("SELECT COUNT(*) FROM leads WHERE status='new' OR status IS NULL");
?><!doctype html><html lang=ar dir=rtl><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><meta name=robots content=noindex><title><?=e($ICO[$P][1]??'لوحة التحكم')?> — لوحة التحكم</title><link rel=icon href="<?=$base?>/assets/logo.svg"><link rel=stylesheet href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap"><link rel=stylesheet href="admin.css?v=<?=filemtime(__DIR__.'/admin.css')?>"><script src="vendor/chart.umd.js"></script><script src="vendor/Sortable.min.js"></script></head><body>
<aside id=sb><a class=brand href="<?=$base?>/" target=_blank><svg class=lgi style="color:#fff;--lt:#3fb2a9"><use href="<?=$base?>/assets/logo-sprite.svg#lg-horiz"/></svg></a><nav><?php foreach($ICO as $k=>$v)echo '<a'.($k==$P||($k=='visitors'&&$P=='visitor')?' class=on':'').' href="?p='.$k.'">'.ic($v[0]).'<span>'.$v[1].'</span>'.($k=='leads'&&$newl?'<b class=bd>'.$newl.'</b>':'').'</a>';?></nav><div class=sbf><a href="<?=$base?>/" target=_blank><?=ic('eye')?> عرض الموقع</a><a href="?out=1"><?=ic('logout')?> خروج</a></div></aside>
<main><header class=tb><button id=mb aria-label=القائمة><?=ic('list')?></button><h1><?=e($ICO[$P][1]??'')?></h1><span class=live title="زوار نشطون خلال آخر 5 دقائق"><i></i><?=(int)q1('SELECT COUNT(DISTINCT vid) FROM visits WHERE bot=0 AND t>?',[time()-300])?> متصل الآن</span></header><div class=ct>
<?php if(!empty($_SESSION['fl'])){echo '<div class="al '.($_SESSION['fl'][0]=='ok'?'okk':'bad').'">'.e($_SESSION['fl'][1]).'</div>';unset($_SESSION['fl']);}
$C=csrf();
function hid(){return '<input type=hidden name=_c value="'.csrf().'">';}
function imgf($name,$val,$label='الصورة'){return '<label>'.$label.'</label><div class=imf><img src="'.($val?'../'.e($val):'').'" '.($val?'':'hidden').'><input name="'.$name.'" value="'.e($val).'" placeholder="مسار الصورة"><button type=button class="btn sm" data-pick>اختيار</button></div>';}
include __DIR__.'/views.php';
?></div></main><div id=pk class=modal hidden><div class=mc><div class=mh><b>اختر صورة من المكتبة</b><button type=button data-x><?=ic('x')?></button></div><form class=upf method=post enctype=multipart/form-data><?=hid()?><input type=hidden name=a value=up><input type=hidden name=ajax value=1><label class="btn sm">رفع صورة جديدة<input type=file name=f[] accept="image/*" multiple hidden></label></form><div class=mg id=pg></div></div></div><script>const C="<?=$C?>";</script><script src="admin.js?v=<?=filemtime(__DIR__.'/admin.js')?>"></script></body></html>
