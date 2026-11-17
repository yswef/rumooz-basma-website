<?php
date_default_timezone_set('Asia/Riyadh');
define('R',__DIR__);
$db=new PDO('sqlite:'.R.'/data/site.db');$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->exec("PRAGMA journal_mode=WAL;PRAGMA busy_timeout=3000;
CREATE TABLE IF NOT EXISTS s(k PRIMARY KEY,v);
CREATE TABLE IF NOT EXISTS services(id INTEGER PRIMARY KEY,title,slug,body,kw,img);
CREATE TABLE IF NOT EXISTS projects(id INTEGER PRIMARY KEY,title,city,img);
CREATE TABLE IF NOT EXISTS leads(id INTEGER PRIMARY KEY,name,phone,msg,t DEFAULT CURRENT_TIMESTAMP);");
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function S($k){global $db;static $c;if(!$c){$c=['name'=>'رموز بسمة للمقاولات','phone'=>'967784609423','wa'=>'السلام عليكم، أرغب بالاستفسار عن خدماتكم في الترميم والدهانات والديكور.','h1'=>'نحوّل مساحتك إلى مكان تحب العودة إليه','sub'=>'ترميم ودهانات وديكورات بإتقان وخبرة 7 سنوات في الدمام والحسين والزبير وأغلب مناطق المملكة.','about'=>'مؤسسة رموز بسمة للمقاولات متخصصة في الترميم والدهانات والديكورات بخبرة 7 سنوات. نخدم الدمام والحسين والزبير وأغلب مناطق المملكة، ونتواصل معك مباشرة عبر الواتساب من أول معاينة حتى التسليم.','cities'=>'الدمام،الحسين،الزبير','gsc'=>'','ga'=>''];foreach($db->query('SELECT k,v FROM s') as $r)$c[$r['k']]=$r['v'];}return $c[$k]??'';}
function n($s){return strtr(preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u','',mb_strtolower($s)),['أ'=>'ا','إ'=>'ا','آ'=>'ا','ة'=>'ه','ى'=>'ي']);}
if(!$db->query('SELECT 1 FROM services')->fetch()){
$st=$db->prepare('INSERT INTO services(title,slug,body,kw,img) VALUES(?,?,?,?,?)');
foreach([
['دهانات وأصباغ','paint','دهانات داخلية وخارجية بأجود الأصباغ، مع تجهيز دقيق للأسطح والمعجون وتشطيب ناعم يدوم سنوات.','دهان صبغ اصباغ بوية طلاء معجون جوتن ورق جدران'],
['ترميم المباني','renovation','ترميم الشقق والفلل والبيوت القديمة: معالجة التشققات والرطوبة وتجديد كامل للمساحات.','ترميم صيانة تجديد شقق فلل بيوت تشققات رطوبة'],
['ديكورات داخلية','decor','أفكار ديكور عصرية للمجالس وغرف النوم والواجهات الداخلية بتنفيذ متقن.','ديكور تصميم داخلي مجالس غرف نوم ستائر'],
['أسقف جبس وإضاءة','gypsum','تنفيذ أسقف الجبس بورد والإضاءة المخفية والسبوت بتصاميم أنيقة.','جبس جبس بورد اسقف معلقه اضاءه سبوت اسقف مخفيه'],
['عزل مائي وحراري','insulation','عزل الأسطح والخزانات والحمامات ومعالجة التسربات لحماية مبناك.','عزل مائي حراري تسرب تسريب رطوبه اسطح خزانات فوم'],
['أرضيات وبلاط','floors','تركيب البلاط والسيراميك والرخام والباركيه بدقة في المقاسات والتسوية.','بلاط سيراميك رخام باركيه ارضيات بورسلين تركيب'],
['مطابخ وحمامات','kitchens','تجديد المطابخ والحمامات بتشطيبات حديثة وتنفيذ نظيف.','مطبخ مطابخ حمام حمامات تجديد كاونتر'],
['صيانة عامة','maintenance','أعمال الصيانة المنزلية من كهرباء وسباكة ونجارة وأبواب ونوافذ.','صيانه كهرباء سباكه نجاره ابواب شبابيك شقق']] as $i=>$r)$st->execute([$r[0],$r[1],$r[2],$r[3],'ph/'.($i+1).'.svg']);
$st=$db->prepare('INSERT INTO projects(title,city,img) VALUES(?,?,?)');
foreach(['دهان شقة سكنية','ترميم فيلا','ديكور مجلس','سقف جبس بورد','عزل سطح','تجديد مطبخ'] as $i=>$t)$st->execute([$t.' (نموذج تجريبي)','الدمام','ph/'.($i+1).'.svg']);}
