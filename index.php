<?php
require __DIR__.'/inc.php';
$base=rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\');
$path=trim(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen($base)),'/');
$host=((!empty($_SERVER['HTTPS'])||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')=='https')?'https':'http').'://'.$_SERVER['HTTP_HOST'].$base;
function wa($m=null){return 'https://wa.me/'.S('phone').'?text='.rawurlencode($m??S('wa'));}
function logo(){return "<svg viewBox='0 0 64 64' fill=none stroke-width=6 stroke-linecap=round stroke-linejoin=round><path d='M6 30 32 8l26 22' stroke='#1F7A74'/><path d='M16 36v18h32V36' stroke='#222'/><path d='M23 43q9 10 18 0' stroke='#E9B44C' stroke-width=5 /></svg>";}
if(preg_match('~^ph/(\d+)\.svg$~',$path,$m)){header('Content-Type:image/svg+xml');header('Cache-Control:max-age=31536000');$c=['#1F7A74','#E9B44C','#d9e6e4','#222','#7fb3ad','#f3dca0'];$w=$c[$m[1]%6];$f=$m[1]%2?'#222':'#fff';
echo "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600'><rect width='800' height='600' fill='$w'/><rect y='430' width='800' height='170' fill='#0003'/><rect x='520' y='90' width='190' height='230' rx='8' fill='#fff8'/><path d='M520 205h190M615 90v230' stroke='$w' stroke-width='8'/><rect x='90' y='320' width='330' height='130' rx='24' fill='$f'/><rect x='70' y='350' width='60' height='100' rx='20' fill='$f'/><rect x='380' y='350' width='60' height='100' rx='20' fill='$f'/><path d='M600 430V330' stroke='#222' stroke-width='6'/><circle cx='600' cy='300' r='34' fill='#E9B44C'/></svg>";exit;}
if($path=='lead'&&$_SERVER['REQUEST_METHOD']=='POST'){header('Content-Type:application/json');if(!empty($_POST['web']))exit;
$a=mb_substr(trim($_POST['name']??''),0,60);$p=mb_substr(trim($_POST['phone']??''),0,20);$m=mb_substr(trim($_POST['msg']??''),0,500);
if($a&&$p)$db->prepare('INSERT INTO leads(name,phone,msg) VALUES(?,?,?)')->execute([$a,$p,$m]);
echo json_encode(['wa'=>wa("السلام عليكم، أنا $a. $m (جوالي: $p)")]);exit;}
if($path=='sitemap.xml'){header('Content-Type:application/xml');echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.$host.'/</loc></url>';foreach($db->query('SELECT slug FROM services') as $r)echo '<url><loc>'.$host.'/s/'.e($r['slug']).'</loc></url>';echo '</urlset>';exit;}
if($path=='robots.txt'){header('Content-Type:text/plain');echo "User-agent: *\nAllow: /\nDisallow: /admin.php\nSitemap: $host/sitemap.xml";exit;}
function top($t,$d,$cn=''){global $base,$host;
$ld=json_encode(['@context'=>'https://schema.org','@type'=>'GeneralContractor','name'=>S('name'),'url'=>$host.'/','telephone'=>'+'.S('phone'),'description'=>S('sub'),'areaServed'=>array_map(fn($c)=>['@type'=>'City','name'=>trim($c)],preg_split('/[،,]/u',S('cities')))],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>
<!doctype html><html lang=ar dir=rtl><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><title><?=e($t)?></title><meta name=description content="<?=e($d)?>"><link rel=canonical href="<?=e("$host/$cn")?>"><meta property=og:title content="<?=e($t)?>"><meta property=og:description content="<?=e($d)?>"><meta property=og:locale content=ar_SA><meta name=theme-color content="#1F7A74"><link rel=icon href="<?=$base?>/assets/logo.svg"><?php if(S('gsc'))echo '<meta name=google-site-verification content="'.e(S('gsc')).'">';?><link rel=stylesheet href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap"><link rel=stylesheet href="<?=$base?>/assets/s.css"><script type=application/ld+json><?=$ld?></script><?php if(S('ga'))echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.e(S('ga')).'"></script><script>window.dataLayer=[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config","'.e(S('ga')).'")</script>';?></head><body>
<header><div class=w><a class=lg href="<?=$base?>/"><?=logo()?><span><?=e(S('name'))?><small>ترميم • دهانات • ديكورات</small></span></a>
<nav><a href="<?=$base?>/#services">الخدمات</a><a href="<?=$base?>/#work">أعمالنا</a><a href="<?=$base?>/#about">من نحن</a><a href="<?=$base?>/#contact">تواصل</a></nav>
<form class=sr action="<?=$base?>/search"><input name=q placeholder="ابحث: دهان، ترميم، ديكور…" aria-label=بحث></form></div></header>
<?php }
function foot(){global $base;?>
<footer><div class=w><p><?=e(S('name'))?> © <?=date('Y')?></p><p>واتساب: <a href="<?=wa()?>" dir=ltr>+<?=e(S('phone'))?></a></p></div></footer>
<a class=fw href="<?=wa()?>" target=_blank rel=noopener aria-label=واتساب><svg viewBox="0 0 24 24" width=30 fill=#fff><path fill=#fff d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.2-4.600-4-4.700-4.200-.1-.2-1.100-1.500-1.100-2.800s.7-2 .9-2.300c.2-.3.5-.3.700-.3h.5c.2 0 .4 0 .6.500l.8 2c.1.2.1.4 0 .5l-.4.600c-.1.100-.3.300-.1.600.7 1.100 1.500 1.900 2.600 2.400.3.100.5.100.6-.1l.8-.9c.2-.2.4-.2.600-.1l1.900.9c.2.100.4.200.4.300.1.200.1.800-.2 1.400z"/></svg></a>
<div class=fb><b>حياك الله 👋</b><br>كيف نقدر نخدمك؟ اكتب لنا على واتساب.</div>
<script>const B="<?=$base?>";document.body.classList.add('j');
const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){const t=x.target;t.classList.add('on');io.unobserve(t);if(t.dataset.n){let v=0,m=+t.dataset.n;const k=setInterval(()=>{v+=Math.ceil(m/30);if(v>=m){v=m;clearInterval(k)}t.textContent=v},40)}}}),{threshold:.2});
document.querySelectorAll('.rv,[data-n]').forEach(e=>io.observe(e));
setTimeout(()=>document.querySelector('.fb').classList.add('on'),4000);
const f=document.getElementById('lf');f&&f.addEventListener('submit',async e=>{e.preventDefault();const r=await fetch(B+'/lead',{method:'POST',body:new FormData(f)});location.href=(await r.json()).wa});</script></body></html>
<?php }
function img($i){global $base;return $base.'/'.e($i);}
$cf=R.'/data/c_'.md5($path.'?'.($_SERVER['QUERY_STRING']??'')).'.html';
if($_SERVER['REQUEST_METHOD']=='GET'&&$path!='search'&&is_file($cf)&&time()-filemtime($cf)<300){readfile($cf);exit;}
ob_start();
if($path==''){
$sv=$db->query('SELECT * FROM services')->fetchAll();$pr=$db->query('SELECT * FROM projects')->fetchAll();$cn=array_filter(array_map('trim',preg_split('/[،,]/u',S('cities'))));
top(S('name').' | ترميم ودهانات وديكورات في الدمام والشرقية',S('sub'));?>
<section class=hero><i style="width:320px;height:320px;background:#1F7A74;top:-80px;right:-60px"></i><i style="width:240px;height:240px;background:#E9B44C;bottom:-90px;left:8%;animation-delay:-3s"></i>
<div class=w><h1><?=e(S('h1'))?></h1><p><?=e(S('sub'))?></p><a class=btn href="<?=wa()?>" target=_blank>تواصل عبر واتساب</a> <a class="btn g" href="#services">استعرض خدماتنا</a>
<div class=stats><div><b data-n=7>0</b>+<span>سنوات خبرة</span></div><div><b data-n=<?=count($sv)?>>0</b><span>خدمة متكاملة</span></div><div><b data-n=<?=count($cn)?>>0</b><span>مناطق خدمة رئيسية</span></div></div></div></section>
<section id=services class=w><h2 class=rv>خدماتنا</h2><div class=grid><?php foreach($sv as $s){?><a class="card rv" href="<?=$base?>/s/<?=e($s['slug'])?>"><img loading=lazy src="<?=img($s['img'])?>" alt="<?=e($s['title'])?>"><h3><?=e($s['title'])?></h3><p><?=e($s['body'])?></p></a><?php }?></div></section>
<section id=work class=w><h2 class=rv>أعمالنا</h2><div class=grid><?php foreach($pr as $s){?><div class="card rv"><img loading=lazy src="<?=img($s['img'])?>" alt="<?=e($s['title'])?>"><h3><?=e($s['title'])?></h3><p><?=e($s['city'])?></p></div><?php }?></div></section>
<section id=about class="w ab rv"><h2>من نحن</h2><p><?=nl2br(e(S('about')))?></p><div class=chips><?php foreach($cn as $c)echo '<span>'.e($c).'</span>';?></div></section>
<section id=contact class="w rv"><h2>اطلب معاينة</h2><form id=lf class=ct><input name=name placeholder="الاسم" required><input name=phone type=tel placeholder="رقم الجوال" required><textarea name=msg placeholder="اكتب طلبك باختصار"></textarea><input name=web class=hp tabindex=-1 autocomplete=off><button class=btn>أرسل عبر واتساب</button></form></section>
<?php foot();
}elseif(preg_match('~^s/([a-z0-9-]+)$~',$path,$m)&&($s=$db->query("SELECT * FROM services WHERE slug=".$db->quote($m[1]))->fetch())){
top($s['title'].' في الدمام والشرقية | '.S('name'),$s['body'],$path);?>
<main class="w sp"><h1><?=e($s['title'])?></h1><img src="<?=img($s['img'])?>" alt="<?=e($s['title'])?>"><p><?=e($s['body'])?></p><p>نخدمك في: <?=e(S('cities'))?> وأغلب مناطق المملكة.</p><a class=btn href="<?=wa("السلام عليكم، أرغب بالاستفسار عن: ".$s['title'])?>" target=_blank>اطلب هذه الخدمة عبر واتساب</a></main><?php foot();
}elseif($path=='search'){
$q=trim($_GET['q']??'');$ws=array_filter(array_map(fn($w)=>preg_replace('/^ال/u','',n($w)),preg_split('/\s+/u',$q)),fn($w)=>mb_strlen($w)>1);$res=[];
foreach($db->query('SELECT * FROM services') as $r){$h=n($r['title'].' '.$r['body'].' '.$r['kw']);$sc=0;foreach($ws as $w)if(mb_strpos($h,$w)!==false)$sc++;if($sc)$res[]=[$sc,$r];}
usort($res,fn($a,$b)=>$b[0]<=>$a[0]);
if(count($res)==1){header("Location: $base/s/".$res[0][1]['slug']);exit;}
top('نتائج البحث: '.$q,'نتائج البحث');?><main class="w sp"><h1>نتائج: <?=e($q)?></h1><?php foreach($res as [$sc,$r])echo '<a class=card href="'.$base.'/s/'.e($r['slug']).'"><h3>'.e($r['title']).'</h3><p>'.e($r['body']).'</p></a>';
if(!$res)echo '<p>ما لقينا نتيجة مطابقة، اكتب لنا ونخدمك مباشرة:</p><a class=btn href="'.wa("أبحث عن: $q").'">واتساب</a>';?></main><?php foot();
}else{http_response_code(404);top('الصفحة غير موجودة','');echo '<main class="w sp"><h1>404</h1><a class=btn href="'.$base.'/">الرئيسية</a></main>';foot();}
if(http_response_code()==200&&$_SERVER['REQUEST_METHOD']=='GET'&&$path!='search')file_put_contents($cf,ob_get_contents());
ob_end_flush();
