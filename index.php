<?php
require __DIR__.'/inc.php';
$base=rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\');
$path=trim(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen($base)),'/');
$path=preg_replace('~^index\.php/?~','',$path);
$host=((!empty($_SERVER['HTTPS'])||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')=='https')?'https':'http').'://'.$_SERVER['HTTP_HOST'].$base;
const GH='https://github.com/yswef';
function wa($m=null){return 'https://wa.me/'.S('phone').'?text='.rawurlencode($m??S('wa'));}
function logo(){return "<svg viewBox='0 0 64 64' fill=none stroke-width=6 stroke-linecap=round stroke-linejoin=round><path d='M6 30 32 8l26 22' stroke='#E9B44C'/><path d='M16 36v18h32V36' stroke='currentColor'/><path d='M23 43q9 10 18 0' stroke='#3fb2a9' stroke-width=5 /></svg>";}
$CM=['دهانات'=>'paint','ترميم'=>'renovation','ديكور'=>'decor','جبس'=>'gypsum','عزل'=>'insulation','مطابخ'=>'kitchens','أرضيات'=>'floors','صيانة'=>'maintenance'];

/* صور بديلة أنيقة تُستخدم حتى ترفع الصور الحقيقية */
if(preg_match('~^ph/([a-z]+)(?:-(\d+))?\.svg$~',$path,$m)){
header('Content-Type:image/svg+xml');header('Cache-Control:max-age=31536000');
$P=[['#1F7A74','#0b3f3b'],['#E9B44C','#a8741a'],['#24403e','#0c1a19'],['#3a9188','#175550'],['#c28a2e','#6e4a12'],['#2b6f69','#10302d']];
$k=$m[1].($m[2]??'');$c=$P[crc32($k)%6];$d=ICONS[$m[1]]??array_values(ICONS)[crc32($k)%8];$o=(int)($m[2]??0);
echo "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='{$c[0]}'/><stop offset='1' stop-color='{$c[1]}'/></linearGradient><pattern id='p' width='48' height='48' patternUnits='userSpaceOnUse'><path d='M48 0H0V48' fill='none' stroke='#fff' stroke-opacity='.07'/></pattern></defs><rect width='800' height='600' fill='url(#g)'/><rect width='800' height='600' fill='url(#p)'/><circle cx='".(620+$o*20)."' cy='90' r='230' fill='#fff' fill-opacity='.07'/><circle cx='".(120+$o*30)."' cy='560' r='190' fill='#000' fill-opacity='.12'/><rect x='80' y='430' width='640' height='6' rx='3' fill='#fff' fill-opacity='.18'/><g transform='translate(400 280) scale(9) translate(-12 -12)' fill='none' stroke='#fff' stroke-width='1.1' stroke-linecap='round' stroke-linejoin='round'><path d='$d'/></g></svg>";exit;}

if($path=='lead'&&$_SERVER['REQUEST_METHOD']=='POST'){header('Content-Type:application/json');if(!empty($_POST['web']))exit;
$a=mb_substr(trim($_POST['name']??''),0,60);$p=mb_substr(trim($_POST['phone']??''),0,20);$sv=mb_substr(trim($_POST['svc']??''),0,60);$m=mb_substr(trim($_POST['msg']??''),0,500);
if($a&&$p)$db->prepare('INSERT INTO leads(name,phone,msg) VALUES(?,?,?)')->execute([$a,$p,trim("$sv $m")]);
echo json_encode(['wa'=>wa("السلام عليكم، أنا $a. ".($sv?"أبحث عن: $sv. ":'')."$m (جوالي: $p)")]);exit;}
if($path=='sitemap.xml'){header('Content-Type:application/xml');echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach(['','services','works','about','contact'] as $u)echo '<url><loc>'.$host.'/'.$u.'</loc></url>';
foreach($db->query('SELECT slug FROM services') as $r)echo '<url><loc>'.$host.'/s/'.e($r['slug']).'</loc></url>';echo '</urlset>';exit;}
if($path=='robots.txt'){header('Content-Type:text/plain');echo "User-agent: *\nAllow: /\nDisallow: /admin.php\nSitemap: $host/sitemap.xml";exit;}

/* ===== القالب ===== */
function top($t,$d,$cn='',$act='',$og=''){global $base,$host;
$ld=json_encode(['@context'=>'https://schema.org','@type'=>'GeneralContractor','name'=>S('name'),'url'=>$host.'/','telephone'=>'+'.S('phone'),'description'=>S('sub'),'areaServed'=>array_map(fn($c)=>['@type'=>'City','name'=>trim($c)],preg_split('/[،,]/u',S('cities')))],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
$nav=['home'=>['/','الرئيسية'],'services'=>['/services','خدماتنا'],'works'=>['/works','أعمالنا'],'about'=>['/about','من نحن'],'contact'=>['/contact','تواصل معنا']];?>
<!doctype html><html lang=ar dir=rtl><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><title><?=e($t)?></title><meta name=description content="<?=e($d)?>"><link rel=canonical href="<?=e("$host/$cn")?>"><meta property=og:title content="<?=e($t)?>"><meta property=og:description content="<?=e($d)?>"><meta property=og:locale content=ar_SA><meta property=og:type content=website><?php if($og)echo '<meta property=og:image content="'.e($og).'">';?><meta name=theme-color content="#0f2f2c"><link rel=icon href="<?=$base?>/assets/logo.svg"><?php if(S('gsc'))echo '<meta name=google-site-verification content="'.e(S('gsc')).'">';?><link rel=preconnect href="https://fonts.googleapis.com"><link rel=preconnect href="https://fonts.gstatic.com" crossorigin><link rel=stylesheet href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap"><link rel=stylesheet href="<?=$base?>/assets/s.css?v=2"><script type=application/ld+json><?=$ld?></script><?php if(S('ga'))echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.e(S('ga')).'"></script><script>window.dataLayer=[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config","'.e(S('ga')).'")</script>';?></head><body>
<header id=hd><div class=w><a class=lg href="<?=$base?>/"><?=logo()?><span><?=e(S('name'))?><small>ترميم • دهانات • ديكورات</small></span></a>
<nav id=nv><?php foreach($nav as $k=>$v)echo '<a'.($k==$act?' class=on':'').' href="'.$base.$v[0].'">'.$v[1].'</a>';?><a class=sq href="<?=$base?>/search" aria-label=بحث><?=ic('search')?></a></nav>
<a class="btn sm hb" href="<?=wa()?>" target=_blank rel=noopener>واتساب</a><button id=bg aria-label="القائمة" aria-expanded=false><i></i><i></i><i></i></button></div></header>
<?php }
function pghead($t,$s,$crumb){global $base;?>
<section class=ph><i style="width:360px;height:360px;background:#1F7A74;top:-120px;right:-80px"></i><i style="width:240px;height:240px;background:#E9B44C;bottom:-100px;left:6%;animation-delay:-4s"></i>
<div class=w><p class=cr><a href="<?=$base?>/">الرئيسية</a> / <?=e($crumb)?></p><h1><?=e($t)?></h1><?php if($s)echo '<p class=lead>'.e($s).'</p>';?></div></section>
<?php }
function foot(){global $base;$cn=array_filter(array_map('trim',preg_split('/[،,]/u',S('cities'))));?>
<footer><div class="w fg"><div><a class=lg href="<?=$base?>/"><?=logo()?><span><?=e(S('name'))?></span></a><p>نحوّل بيتك القديم إلى مساحة تفخر بها. ترميم ودهانات وديكورات بإتقان وخبرة 7 سنوات.</p></div>
<div><h4>روابط سريعة</h4><a href="<?=$base?>/services">خدماتنا</a><a href="<?=$base?>/works">أعمالنا</a><a href="<?=$base?>/about">من نحن</a><a href="<?=$base?>/contact">تواصل معنا</a></div>
<div><h4>مناطق الخدمة</h4><?php foreach($cn as $c)echo '<span>'.e($c).'</span>';?><span>وأغلب مناطق المملكة</span></div>
<div><h4>تواصل</h4><a href="<?=wa()?>" target=_blank rel=noopener dir=ltr>+<?=e(S('phone'))?></a><a class="btn sm" href="<?=wa()?>" target=_blank rel=noopener>اطلب معاينة</a></div></div>
<div class="w fl"><p><?=e(S('name'))?> © <?=date('Y')?> — جميع الحقوق محفوظة</p><p class=credit>صُنع بحب <?=ic('heart','ic hr')?> من قبل <a href="<?=GH?>" target=_blank rel=noopener>يوسف الحمزي</a></p></div></footer>
<a class=fw href="<?=wa()?>" target=_blank rel=noopener aria-label=واتساب><svg viewBox="0 0 24 24" width=30 fill=#fff><path fill=#fff d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.2-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8s.7-2 .9-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.6c-.1.1-.3.3-.1.6.7 1.1 1.5 1.9 2.6 2.4.3.1.5.1.6-.1l.8-.9c.2-.2.4-.2.6-.1l1.9.9c.2.1.4.2.4.3.1.2.1.8-.2 1.4z"/></svg></a>
<div class=fb><b>حياك الله 👋</b><br>جدّد بيتك اليوم، اكتب لنا على واتساب.</div>
<script>const B="<?=$base?>";document.body.classList.add('j');
const $=s=>[...document.querySelectorAll(s)],hd=document.getElementById('hd'),bg=document.getElementById('bg');
const sc=()=>hd.classList.toggle('sc',scrollY>40);sc();addEventListener('scroll',sc,{passive:true});
bg.onclick=()=>{const o=document.body.classList.toggle('mo');bg.setAttribute('aria-expanded',o)};
$('#nv a').forEach(a=>a.onclick=()=>document.body.classList.remove('mo'));
const cnt=t=>{const m=+t.dataset.n;let s=null;const f=ts=>{s=s||ts;const p=Math.min((ts-s)/1400,1);t.textContent=Math.round(m*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(f)};requestAnimationFrame(f)};
const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){const t=x.target;t.classList.add('on');io.unobserve(t);if(t.dataset.n)cnt(t)}}),{threshold:.15});
$('.rv,[data-n]').forEach(e=>io.observe(e));
if(matchMedia('(hover:hover)').matches)$('[data-tilt]').forEach(c=>{c.addEventListener('mousemove',v=>{const r=c.getBoundingClientRect(),x=(v.clientX-r.left)/r.width-.5,y=(v.clientY-r.top)/r.height-.5;c.style.setProperty('--ry',(x*8)+'deg');c.style.setProperty('--rx',(-y*8)+'deg')});c.addEventListener('mouseleave',()=>{c.style.setProperty('--ry','0deg');c.style.setProperty('--rx','0deg')})});
setTimeout(()=>document.querySelector('.fb').classList.add('on'),5000);setTimeout(()=>document.querySelector('.fb').classList.remove('on'),13000);
$('.fc button').forEach(b=>b.onclick=()=>{$('.fc button').forEach(x=>x.classList.remove('on'));b.classList.add('on');$('.pj').forEach(p=>{const h=b.dataset.c!='all'&&p.dataset.cat!=b.dataset.c;p.classList.toggle('hid',h)})});
const lb=document.getElementById('lb');if(lb){const im=lb.querySelector('img'),cp=lb.querySelector('p');let L=[],i=0;const sh=n=>{i=(n+L.length)%L.length;im.src=L[i].href;cp.textContent=L[i].dataset.cap;lb.classList.add('on')};
$('a.pj[data-lb]').forEach(a=>a.onclick=e=>{e.preventDefault();L=$('a.pj[data-lb]').filter(x=>!x.classList.contains('hid'));sh(L.indexOf(a))});
lb.querySelector('.x').onclick=()=>lb.classList.remove('on');lb.querySelector('.nx').onclick=()=>sh(i+1);lb.querySelector('.pv').onclick=()=>sh(i-1);
lb.onclick=e=>{if(e.target==lb)lb.classList.remove('on')};addEventListener('keydown',e=>{if(!lb.classList.contains('on'))return;if(e.key=='Escape')lb.classList.remove('on');if(e.key=='ArrowLeft')sh(i+1);if(e.key=='ArrowRight')sh(i-1)})}
$('form.lf').forEach(f=>f.addEventListener('submit',async e=>{e.preventDefault();const b=f.querySelector('button');b.disabled=true;b.textContent='جارٍ التحويل إلى واتساب…';
try{const r=await fetch(B+'/lead',{method:'POST',body:new FormData(f)});location.href=(await r.json()).wa}catch(x){location.href=<?=json_encode(wa())?>}}));</script></body></html>
<?php }
function cta($t='هل بيتك يستحق شكلاً أجمل؟',$s='تواصل معنا اليوم، نعاين، نقترح، ونحوّل فكرتك إلى واقع تفتخر به.'){?>
<section class="cta w-full"><div class="w ci"><div class=rv><h2><?=e($t)?></h2><p><?=e($s)?></p></div><div class="bt rv" style="--d:.15s"><a class="btn g lg2" href="<?=wa()?>" target=_blank rel=noopener>ابدأ التجديد على واتساب</a><a class="btn o" href="<?=$GLOBALS['base']?>/contact">اطلب معاينة</a></div></div></section>
<?php }
function scard($s,$i){global $base;?>
<a class="card rv" data-tilt style="--d:<?=($i%4)*.08?>s" href="<?=$base?>/s/<?=e($s['slug'])?>"><div class=im><img loading=lazy src="<?=im($s,$s['slug'],kd($s['slug']))?>" alt="<?=e($s['title'])?>"><span class=ib><?=ic($s['slug'])?></span></div><div class=cb><h3><?=e($s['title'])?></h3><p><?=e($s['body'])?></p><span class=mr>اكتشف المزيد <?=ic('arrow')?></span></div></a>
<?php }
function pcard($p,$i,$lb=true){global $base,$CM;$k=($CM[$p['cat']??'']??'renovation').'-'.$p['id'];$u=im($p,'work-'.$p['id'],$k);?>
<a class="pj rv" style="--d:<?=($i%4)*.07?>s" data-cat="<?=e($p['cat'])?>" href="<?=$lb?$u:$base.'/works'?>" <?=$lb?'data-lb data-cap="'.e($p['title'].' — '.$p['city']).'"':''?>><img loading=lazy src="<?=$u?>" alt="<?=e($p['title'])?>"><div class=ov><span><?=e($p['cat'])?></span><h3><?=e($p['title'])?></h3><p><?=ic('pin')?> <?=e($p['city'])?></p></div></a>
<?php }
function form($sv){?>
<form class="lf ct" ><input name=name placeholder="الاسم" required autocomplete=name><input name=phone type=tel placeholder="رقم الجوال" required autocomplete=tel><select name=svc><option value="">نوع الخدمة المطلوبة</option><?php foreach($sv as $s)echo '<option>'.e($s['title']).'</option>';?></select><textarea name=msg rows=3 placeholder="اكتب طلبك باختصار (المساحة، المدينة…)"></textarea><input name=web class=hp tabindex=-1 autocomplete=off><button class="btn g">أرسل طلبك عبر واتساب</button></form>
<?php }

$sv=$db->query('SELECT * FROM services ORDER BY id')->fetchAll();
$cn=array_filter(array_map('trim',preg_split('/[،,]/u',S('cities'))));
$N=S('name');

if($path==''){
$pr=$db->query('SELECT * FROM projects ORDER BY id DESC LIMIT 6')->fetchAll();$hb=bg('hero');
top($N.' | ترميم ودهانات وديكورات في الدمام والشرقية',S('sub'),'','home',$hb);?>
<section class=hero<?=$hb?' style="--hb:url('.e($hb).')"':''?>><i style="width:420px;height:420px;background:#1F7A74;top:-120px;right:-100px"></i><i style="width:300px;height:300px;background:#E9B44C;bottom:-120px;left:4%;animation-delay:-3s"></i>
<div class="w hg"><div class=ht><span class=kick>خبرة 7 سنوات في الدمام والشرقية</span><h1><?=e(S('h1'))?><span><?=e(S('h1b'))?></span></h1><p><?=e(S('sub'))?></p>
<div class=bt><a class="btn g lg2" href="<?=wa()?>" target=_blank rel=noopener>احصل على معاينة الآن</a><a class="btn o" href="<?=$base?>/works">شاهد أعمالنا</a></div>
<ul class=tr><li><?=ic('check')?> تنفيذ متقن ونظيف</li><li><?=ic('check')?> متابعة مباشرة عبر واتساب</li><li><?=ic('check')?> أسعار واضحة</li></ul></div>
<div class=col><img class=c1 src="<?=im($sv[0]??[],'paint','paint')?>" alt="دهانات"><img class=c2 src="<?=im($sv[1]??[],'renovation','renovation')?>" alt="ترميم"><img class=c3 src="<?=im($sv[2]??[],'decor','decor')?>" alt="ديكور">
<div class=badge><b data-n=7>0</b><em>+</em><span>سنوات من الإتقان</span></div></div></div></section>
<section class="w sb"><div class=rv><b data-n=7>0</b>+<span>سنوات خبرة</span></div><div class=rv style="--d:.1s"><b data-n=<?=count($sv)?>>0</b><span>خدمة متكاملة</span></div><div class=rv style="--d:.2s"><b data-n=<?=max(1,count($cn))?>>0</b>+<span>مدن نخدمها</span></div><div class=rv style="--d:.3s"><b>100%</b><span>تواصل مباشر معك</span></div></section>
<div class=mq aria-hidden=true><div><?php for($k=0;$k<2;$k++)foreach($sv as $s)echo '<span>'.e($s['title']).'</span>';?></div></div>
<section class="sec w"><div class="sh rv"><span class=tag>خدماتنا</span><h2>كل ما يحتاجه بيتك، في مكان واحد</h2><p>من الدهان إلى الترميم الكامل، فريق واحد ينفّذ بإتقان ويسلّمك بيتاً يليق بك.</p></div><div class=grid><?php foreach($sv as $i=>$s)scard($s,$i);?></div></section>
<section class="sec alt"><div class="w sp2"><div class="im2 rv l"><img loading=lazy src="<?=bg('about')?:$base.'/ph/renovation.svg'?>" alt="فريق رموز بسمة"><div class=fl2><?=ic('shield')?><div><b>إتقان</b><span>في كل تفصيلة</span></div></div></div>
<div><div class="sh rv"><span class=tag>لماذا رموز بسمة؟</span><h2>لأن بيتك ليس مجرد جدران</h2><p>نتعامل مع كل مشروع كأنه بيتنا. نسمع فكرتك، ننفذها بدقة، ونسلّمك النتيجة التي حلمت بها.</p></div>
<div class=fts><?php foreach([['star','خبرة 7 سنوات','سنوات من المشاريع علّمتنا كيف ننجز بدقة وسرعة.'],['eye','معاينة وعرض سعر واضح','نعاين موقعك ونقدّم لك تفاصيل واضحة قبل البدء.'],['bolt','تواصل مباشر وسريع','نرد عليك مباشرة عبر واتساب من أول رسالة حتى التسليم.'],['shield','تنفيذ متقن ونظيف','نهتم بأدق التفاصيل ونترك المكان نظيفاً.']] as $i=>$f)echo '<div class="ft rv" style="--d:'.($i*.08).'s">'.ic($f[0]).'<div><h3>'.$f[1].'</h3><p>'.$f[2].'</p></div></div>';?></div></div></div></section>
<?php cta('مللت من شكل منزلك القديم؟ قرارك اليوم يغيّره غداً','لا تؤجّل جمال بيتك. أرسل لنا صورة من موقعك وسنقترح عليك الأنسب.');?>
<section class="sec w"><div class="sh rv"><span class=tag>أعمالنا</span><h2>نتائج تتحدث عن نفسها</h2><p>لمحة من مشاريع نفّذناها بحب وإتقان في المنطقة الشرقية.</p></div><div class="pg pg3"><?php foreach($pr as $i=>$p)pcard($p,$i,false);?></div><div class="mid rv"><a class="btn" href="<?=$base?>/works">شاهد كل الأعمال</a></div></section>
<section class="sec alt"><div class=w><div class="sh c rv"><span class=tag>كيف نعمل؟</span><h2>أربع خطوات بسيطة لبيت جديد</h2></div><div class=steps><?php foreach([['phone','تواصل معنا','راسلنا على واتساب وأخبرنا بفكرتك.'],['eye','المعاينة والتسعير','نزور الموقع ونقدّم عرضاً واضحاً.'],['renovation','التنفيذ بإتقان','فريقنا ينفّذ بدقة ويطلعك أولاً بأول.'],['key','التسليم','تستلم بيتك بحلّته الجديدة.']] as $i=>$s)echo '<div class="stp rv" style="--d:'.($i*.12).'s"><span class=sn>'.($i+1).'</span>'.ic($s[0]).'<h3>'.$s[1].'</h3><p>'.$s[2].'</p></div>';?></div></div></section>
<section class="sec w fq"><div class="sh rv"><span class=tag>أسئلة شائعة</span><h2>عندك سؤال؟ هذه أبرز الإجابات</h2></div><div class=fa><?php foreach([['كيف أبدأ مشروعي معكم؟','راسلنا على واتساب أو عبّئ نموذج الطلب، وسنتواصل معك لترتيب المعاينة.'],['هل تخدمون خارج الدمام؟','نخدم الدمام والحسين والزبير وأغلب مناطق المملكة، تواصل معنا للتأكد من منطقتك.'],['هل تنفذون أكثر من عمل في مشروع واحد؟','نعم، نقدّم ترميماً ودهاناً وجبساً وعزلاً وأرضيات ومطابخ وصيانة بفريق واحد ومتابعة واحدة.'],['كيف يتحدد السعر؟','بعد المعاينة نقدّم عرضاً واضحاً حسب المساحة وتفاصيل العمل والخامات المطلوبة.']] as $i=>$q)echo '<details class="rv" style="--d:'.($i*.07).'s"><summary>'.$q[0].'</summary><p>'.$q[1].'</p></details>';?></div></section>
<section class="sec w ctc"><div class="sh rv"><span class=tag>اطلب معاينة</span><h2>خلّنا نبدأ بتجديد بيتك</h2><p>اترك بياناتك وسنتواصل معك عبر واتساب.</p></div><div class=rv><?php form($sv);?></div></section>
<?php foot();

}elseif($path=='services'){
top('خدماتنا | '.$N,'ترميم، دهانات، ديكورات، جبس، عزل، أرضيات، مطابخ وصيانة عامة في الدمام والشرقية.','services','services');
pghead('خدماتنا','كل ما يحتاجه بيتك من الترميم إلى آخر لمسة ديكور، بإتقان وخبرة 7 سنوات.','خدماتنا');?>
<section class="sec w"><div class=grid><?php foreach($sv as $i=>$s)scard($s,$i);?></div></section><?php cta();foot();

}elseif($path=='works'){
$pr=$db->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();$cats=array_values(array_unique(array_filter(array_column($pr,'cat'))));
top('أعمالنا | '.$N,'معرض أعمال رموز بسمة للمقاولات: دهانات وترميم وديكورات وأسقف جبس في الدمام والشرقية.','works','works');
pghead('معرض أعمالنا','مشاريع نفّذناها بحب وإتقان. كل صورة قصة بيت تجدّد.','أعمالنا');?>
<section class="sec w"><div class="fc rv"><button class=on data-c=all>الكل</button><?php foreach($cats as $c)echo '<button data-c="'.e($c).'">'.e($c).'</button>';?></div><div class=pg><?php foreach($pr as $i=>$p)pcard($p,$i);?></div>
<p class="credit big rv">صُنع بحب <?=ic('heart','ic hr')?> من قبل <a href="<?=GH?>" target=_blank rel=noopener>يوسف الحمزي</a></p></section>
<div id=lb><button class=x aria-label=إغلاق>×</button><button class=pv aria-label=السابق>›</button><figure><img alt=""><p></p></figure><button class=nx aria-label=التالي>‹</button></div>
<?php cta('عمل بيتك القادم قد يكون هنا','أرسل لنا فكرتك وسنحوّلها إلى مشروع تفتخر به.');foot();

}elseif($path=='about'){
top('من نحن | '.$N,S('about'),'about','about');pghead('من نحن','قصة مؤسسة آمنت بأن البيت الجميل يغيّر يوم صاحبه.','من نحن');?>
<section class="sec w sp2 ab"><div class="im2 rv l"><img loading=lazy src="<?=bg('about')?:$base.'/ph/decor.svg'?>" alt="عن رموز بسمة"></div><div class=rv><span class=tag>قصتنا</span><h2><?=e($N)?></h2><p class=lead2><?=nl2br(e(S('about')))?></p><div class=chips><?php foreach($cn as $c)echo '<span>'.ic('pin').e($c).'</span>';?></div></div></section>
<section class="sec alt"><div class=w><div class="sh c rv"><span class=tag>قيمنا</span><h2>ما نلتزم به في كل مشروع</h2></div><div class=fts4><?php foreach([['star','الإتقان','لا نسلّم عملاً لا نرضى عنه.'],['heart','الأمانة','وضوح في التفاصيل والتواصل.'],['bolt','السرعة','نحترم وقتك ونلتزم بالمتابعة.'],['shield','الجودة','خامات مختارة وتنفيذ يدوم.']] as $i=>$f)echo '<div class="vl rv" data-tilt style="--d:'.($i*.08).'s">'.ic($f[0]).'<h3>'.$f[1].'</h3><p>'.$f[2].'</p></div>';?></div></div></section>
<section class="w sb"><div class=rv><b data-n=7>0</b>+<span>سنوات خبرة</span></div><div class=rv style="--d:.1s"><b data-n=<?=count($sv)?>>0</b><span>خدمة متكاملة</span></div><div class=rv style="--d:.2s"><b data-n=<?=max(1,count($cn))?>>0</b>+<span>مدن نخدمها</span></div></section>
<?php cta();foot();

}elseif($path=='contact'){
top('تواصل معنا | '.$N,'اطلب معاينة مجانية لمشروعك عبر واتساب أو نموذج الطلب.','contact','contact');pghead('تواصل معنا','نحب أن نسمع فكرتك. راسلنا وسنرد عليك بأسرع وقت.','تواصل معنا');?>
<section class="sec w cg"><div class=rv><span class=tag>اطلب معاينة</span><h2>أخبرنا عن مشروعك</h2><?php form($sv);?></div>
<div class="ci2 rv" style="--d:.15s"><a class=ic3 href="<?=wa()?>" target=_blank rel=noopener><?=ic('phone')?><div><b>واتساب</b><span dir=ltr>+<?=e(S('phone'))?></span></div></a><div class=ic3><?=ic('pin')?><div><b>مناطق الخدمة</b><span><?=e(implode('، ',$cn))?> وأغلب مناطق المملكة</span></div></div><div class=ic3><?=ic('clock')?><div><b>سرعة الرد</b><span>نرد عليك مباشرة عبر واتساب</span></div></div></div></section><?php foot();

}elseif(preg_match('~^s/([a-z0-9-]+)$~',$path,$m)&&($s=$db->query("SELECT * FROM services WHERE slug=".$db->quote($m[1]))->fetch())){
$ft=array_filter(array_map('trim',explode("\n",(string)$s['feat'])));
top($s['title'].' في الدمام والشرقية | '.$N,$s['body'],$path,'services',im($s,$s['slug'],kd($s['slug'])));
pghead($s['title'],$s['body'],$s['title']);?>
<section class="sec w sd"><div class=rv><img class=hi src="<?=im($s,$s['slug'],kd($s['slug']))?>" alt="<?=e($s['title'])?>"><h2>ماذا نقدّم في <?=e($s['title'])?></h2><p class=lead2><?=e($s['body'])?></p><?php if($ft){echo '<ul class=ck>';foreach($ft as $f)echo '<li>'.ic('check').e($f).'</li>';echo '</ul>';}?><p>نخدمك في: <?=e(S('cities'))?> وأغلب مناطق المملكة.</p></div>
<aside class="side rv" style="--d:.15s"><div class=box><h3>اطلب هذه الخدمة</h3><p>أرسل لنا طلبك وسنتواصل معك لترتيب المعاينة.</p><a class="btn g" href="<?=wa("السلام عليكم، أرغب بالاستفسار عن: ".$s['title'])?>" target=_blank rel=noopener>تواصل عبر واتساب</a></div>
<div class=box><h3>خدمات أخرى</h3><?php foreach($sv as $o)if($o['id']!=$s['id'])echo '<a class=ol href="'.$base.'/s/'.e($o['slug']).'">'.ic($o['slug']).e($o['title']).'</a>';?></div></aside></section><?php cta();foot();

}elseif($path=='search'){
$q=trim($_GET['q']??'');$ws=array_filter(array_map(fn($w)=>preg_replace('/^ال/u','',n($w)),preg_split('/\s+/u',$q)),fn($w)=>mb_strlen($w)>1);$res=[];
foreach($sv as $r){$h=n($r['title'].' '.$r['body'].' '.$r['kw']);$sc=0;foreach($ws as $w)if(mb_strpos($h,$w)!==false)$sc++;if($sc)$res[]=[$sc,$r];}
usort($res,fn($a,$b)=>$b[0]<=>$a[0]);
if(count($res)==1){header("Location: $base/s/".$res[0][1]['slug']);exit;}
top('البحث | '.$N,'ابحث في خدماتنا','search');pghead($q?'نتائج: '.$q:'ابحث في خدماتنا','','بحث');?>
<section class="sec w"><form class=sf action="<?=$base?>/search"><input name=q value="<?=e($q)?>" placeholder="ابحث: دهان، ترميم، ديكور، جبس…" aria-label=بحث><button class=btn>بحث</button></form><div class=grid><?php foreach($res as $i=>[$sc,$r])scard($r,$i);?></div>
<?php if($q&&!$res)echo '<p class=mid>ما لقينا نتيجة مطابقة، اكتب لنا ونخدمك مباشرة:</p><p class=mid><a class="btn" href="'.wa("أبحث عن: $q").'">واتساب</a></p>';?></section><?php foot();

}else{http_response_code(404);top('الصفحة غير موجودة | '.$N,'','','');pghead('404','هذه الصفحة غير موجودة، لكن بيتك الجديد موجود!','غير موجودة');echo '<section class="sec w mid"><a class=btn href="'.$base.'/">الرئيسية</a></section>';foot();}
