const $=(s,r=document)=>r.querySelector(s),$$=(s,r=document)=>[...r.querySelectorAll(s)];
$('#mb')&&($('#mb').onclick=()=>document.body.classList.toggle('so'));
/* الرسوم البيانية */
if(window.D&&typeof Chart!=='undefined'){Chart.defaults.font.family='Tajawal';Chart.defaults.color='#6b7b79';const D=window.D;
const c1=$('#cv1');if(c1){const g=c1.getContext('2d').createLinearGradient(0,0,0,260);g.addColorStop(0,'#1F7A7455');g.addColorStop(1,'#1F7A7400');
new Chart(c1,{type:'line',data:{labels:D.days.map(d=>d.slice(5)),datasets:[{label:'زوار',data:D.v,borderColor:'#1F7A74',backgroundColor:g,fill:true,tension:.4,pointRadius:2,borderWidth:2.5},{label:'مشاهدات',data:D.p,borderColor:'#E9B44C',tension:.4,pointRadius:0,borderWidth:2,borderDash:[5,4]}]},options:{maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef3f3'}},x:{grid:{display:false},ticks:{maxRotation:0,autoSkip:true,maxTicksLimit:8}}}}})}
const c2=$('#cv2');if(c2)new Chart(c2,{type:'doughnut',data:{labels:D.dev,datasets:[{data:D.devn,backgroundColor:['#1F7A74','#E9B44C','#3b8fe0','#e5584d'],borderWidth:0}]},options:{maintainAspectRatio:false,cutout:'66%',plugins:{legend:{position:'bottom'}}}});
const c3=$('#cv3');if(c3)new Chart(c3,{type:'bar',data:{labels:D.hrs.map((_,i)=>i),datasets:[{data:D.hrs,backgroundColor:'#3fb2a9',borderRadius:5}]},options:{maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef3f3'}},x:{grid:{display:false},ticks:{autoSkip:true,maxTicksLimit:12}}}}})}
/* عدّاد */
$$('.kpi b[data-n]').forEach(b=>{const m=+b.dataset.n;let s=null;const f=t=>{s=s||t;const p=Math.min((t-s)/900,1);b.textContent=Math.round(m*(1-Math.pow(1-p,3))).toLocaleString('en');if(p<1)requestAnimationFrame(f)};requestAnimationFrame(f)});
/* نسخ المسار */
$$('[data-copy]').forEach(b=>b.onclick=()=>{navigator.clipboard&&navigator.clipboard.writeText(b.dataset.copy);const t=b.textContent;b.textContent='تم النسخ ✓';setTimeout(()=>b.textContent=t,1200)});
/* رفع بالسحب والإفلات */
const upload=(files,done)=>{const f=new FormData();f.append('_c',C);f.append('a','up');f.append('ajax','1');[...files].forEach(x=>f.append('f[]',x));const x=new XMLHttpRequest(),pb=$('#pgb');pb&&(pb.hidden=false);
x.upload.onprogress=e=>{if(pb)pb.firstChild.style.width=(e.loaded/e.total*100)+'%'};x.onload=()=>done&&done();x.open('POST','index.php');x.send(f)};
const dz=$('#dz'),fi=$('#fi');if(dz){['dragover','dragenter'].forEach(e=>dz.addEventListener(e,ev=>{ev.preventDefault();dz.classList.add('ov')}));['dragleave','drop'].forEach(e=>dz.addEventListener(e,ev=>{ev.preventDefault();dz.classList.remove('ov')}));dz.addEventListener('drop',ev=>upload(ev.dataTransfer.files,()=>location.reload()));fi.onchange=()=>upload(fi.files,()=>location.reload())}
/* منتقي الصور */
const pk=$('#pk');let cb=null;
const loadM=async()=>{const r=await (await fetch('?p=mj')).json();$('#pg').innerHTML=r.length?'':'<p class=mt>لا توجد صور. ارفع صورة بالزر أعلاه.</p>';r.forEach(m=>{const d=document.createElement('div');d.className='mi';d.innerHTML=`<img src="../${m.thumb}" alt="">`;d.onclick=()=>{pk.hidden=true;cb&&cb(m.file)};$('#pg').appendChild(d)})};
const pick=c=>{cb=c;pk.hidden=false;loadM()};
if(pk){$('[data-x]',pk).onclick=()=>pk.hidden=true;$('.upf input',pk).onchange=e=>upload(e.target.files,loadM)}
$$('[data-pick]').forEach(b=>b.onclick=()=>{const w=b.closest('.imf'),inp=$('input',w),im=$('img',w);pick(v=>{inp.value=v;im.src='../'+v;im.hidden=false})});
$$('.imf input').forEach(i=>i.oninput=()=>{const im=$('img',i.parentNode);if(i.value){im.src='../'+i.value;im.hidden=false}else im.hidden=true});
/* منشئ الصفحات */
const bl=$('#bl');if(bl){
const SC={hero:{l:'غلاف رئيسي',f:[['title','العنوان'],['sub','السطر الملوّن'],['img','صورة الخلفية','img'],['btn','نص الزر'],['url','رابط الزر (اتركه فارغاً لواتساب)']]},
text:{l:'نص',f:[['h','العنوان'],['body','النص','ta']]},image:{l:'صورة كبيرة',f:[['img','الصورة','img'],['cap','وصف الصورة']]},
gallery:{l:'معرض صور',f:[['h','العنوان']],imgs:1},cards:{l:'بطاقات مزايا',f:[['h','العنوان']],items:[['icon','أيقونة: star, heart, shield, bolt, paint, renovation, decor, gypsum…'],['h','العنوان'],['p','الوصف']]},
faq:{l:'أسئلة شائعة',f:[['h','العنوان']],items:[['q','السؤال'],['a','الجواب','ta']]},cta:{l:'دعوة للتواصل',f:[['h','العنوان'],['p','النص']]}};
let data=[];try{data=JSON.parse(bl.dataset.blocks)||[]}catch(e){}
const inp=(o,k,l,t)=>{const w=document.createElement('div');w.innerHTML=`<label>${l.split(':')[0]}</label>`;let i;if(t=='ta'){i=document.createElement('textarea');i.rows=3}else{i=document.createElement('input')}i.value=o[k]||'';i.oninput=()=>o[k]=i.value;if(t=='img'){const r=document.createElement('div');r.className='imf';const b=document.createElement('button');b.type='button';b.className='btn sm';b.textContent='اختيار';b.onclick=()=>pick(v=>{i.value=v;o[k]=v});r.append(i,b);w.append(r)}else w.append(i);return w};
const render=()=>{bl.innerHTML='';data.forEach((b,ix)=>{const s=SC[b.t];if(!s)return;const el=document.createElement('div');el.className='blk';el.dataset.i=ix;
el.innerHTML=`<div class=bh><span class=hnd>⠿</span><b>${s.l}${b.title||b.h?' — '+(b.title||b.h):''}</b><button type=button class="btn sm d rm">حذف</button></div>`;const bb=document.createElement('div');bb.className='bb';
s.f.forEach(f=>bb.append(inp(b,f[0],f[1],f[2])));
if(s.imgs){b.imgs=b.imgs||[];const w=document.createElement('div');w.innerHTML='<label>الصور</label>';const g=document.createElement('div');g.className='mg';g.style.gridTemplateColumns='repeat(auto-fill,minmax(90px,1fr))';b.imgs.forEach((u,k)=>{const d=document.createElement('div');d.className='mi';d.innerHTML=`<img src="../${u}"><div class=mo><button type=button class="btn sm d">×</button></div>`;d.querySelector('button').onclick=()=>{b.imgs.splice(k,1);render()};g.append(d)});w.append(g);const ab=document.createElement('button');ab.type='button';ab.className='btn sm o';ab.textContent='+ إضافة صورة';ab.onclick=()=>pick(v=>{b.imgs.push(v);render()});w.append(ab);bb.append(w)}
if(s.items){b.items=b.items||[];const w=document.createElement('div');w.innerHTML='<label>العناصر</label>';b.items.forEach((it,k)=>{const d=document.createElement('div');d.className='itm';s.items.forEach(f=>d.append(inp(it,f[0],f[1],f[2])));const r=document.createElement('button');r.type='button';r.className='btn sm d rm';r.textContent='×';r.onclick=()=>{b.items.splice(k,1);render()};d.append(r);w.append(d)});const ab=document.createElement('button');ab.type='button';ab.className='btn sm o';ab.style.marginTop='8px';ab.textContent='+ إضافة عنصر';ab.onclick=()=>{b.items.push({});render()};w.append(ab);bb.append(w)}
el.append(bb);$('.bh',el).onclick=e=>{if(e.target.closest('.rm')){if(confirm('حذف هذا القسم؟')){data.splice(ix,1);render()}return}el.classList.toggle('cl')};bl.append(el)})};
render();new Sortable(bl,{handle:'.hnd',animation:150,onEnd:()=>{data=$$('.blk',bl).map(e=>data[+e.dataset.i]);render()}});
$$('[data-add]').forEach(b=>b.onclick=()=>{data.push({t:b.dataset.add});render();bl.lastChild.scrollIntoView({behavior:'smooth',block:'center'})});
$('#pf').onsubmit=()=>{$('#bj').value=JSON.stringify(data)}}

/* تبويبات الإعدادات */
const st=$$('.stabs button');if(st.length){const sh=t=>{st.forEach(b=>b.classList.toggle('on',b.dataset.t==t));$$('.pane').forEach(p=>p.hidden=p.id!=='p-'+t);try{history.replaceState(null,'','#'+t)}catch(e){}};st.forEach(b=>b.onclick=()=>sh(b.dataset.t));sh((location.hash||'#text').slice(1)||'text')}
