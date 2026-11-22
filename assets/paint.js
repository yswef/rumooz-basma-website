/* دلو الطلاء: يتبع المؤشر ويرش البقع. يتعطل مع تقليل الحركة */
(()=>{if(matchMedia('(prefers-reduced-motion:reduce)').matches)return;
const fine=matchMedia('(pointer:fine)').matches,COL=['#1F7A74','#E9B44C','#e5584d','#3b8fe0','#8e5bd6','#2fb36f','#f28c28'];let ci=0;
const cv=document.createElement('canvas');cv.id='pc';document.body.appendChild(cv);const x=cv.getContext('2d');let W,H;
const rs=()=>{const d=Math.min(devicePixelRatio||1,2);W=innerWidth;H=innerHeight;cv.width=W*d;cv.height=H*d;x.setTransform(d,0,0,d,0,0)};rs();addEventListener('resize',rs);
const sp=[],dr=[],rnd=(a,b)=>a+Math.random()*(b-a);
function splat(px,py,r,c){const pts=[],n=11;for(let i=0;i<n;i++)pts.push(rnd(.75,1.25));const sat=[];for(let i=0;i<3+r/8;i++){const a=rnd(0,6.28),d=r*rnd(1.1,2);sat.push([Math.cos(a)*d,Math.sin(a)*d,r*rnd(.1,.22)])}
sp.push({x:px,y:py,r,c,t:performance.now(),pts,sat,rot:rnd(0,6.28)});if(sp.length>70)sp.shift()}
function blob(s,a){x.globalAlpha=a;x.fillStyle=s.c;x.beginPath();const n=s.pts.length;for(let i=0;i<=n;i++){const k=i%n,an=s.rot+k/n*6.2832,r=s.r*s.pts[k],X=s.x+Math.cos(an)*r,Y=s.y+Math.sin(an)*r;i?x.quadraticCurveTo(s.x+Math.cos(an-.28)*r*1.18,s.y+Math.sin(an-.28)*r*1.18,X,Y):x.moveTo(X,Y)}x.fill();for(const q of s.sat){x.beginPath();x.arc(s.x+q[0],s.y+q[1],q[2],0,6.2832);x.fill()}}
function burst(px,py,n,c){for(let i=0;i<n;i++){const a=rnd(-Math.PI,0),v=rnd(2,7);dr.push({x:px,y:py,vx:Math.cos(a)*v,vy:Math.sin(a)*v-1.5,r:rnd(1.2,3),c,g:.45,land:rnd(40,200)+py})}}
let mx=-99,my=-99,bx=-99,by=-99,px=0,sc=0,bk=null,svg='';
if(fine){bk=document.createElement('div');bk.id='bk';bk.innerHTML=`<svg viewBox="0 0 80 80"><g id="bkg" style="transform-origin:40px 44px;transform-box:fill-box"><path d="M18 30Q40 -6 62 30" fill="none" stroke="#8b949e" stroke-width="3" stroke-linecap="round"/><path d="M12 30h56l-7 44a4 4 0 0 1-4 3H23a4 4 0 0 1-4-3z" fill="#cfd6dc"/><path d="M12 30h56l-2 12H14z" fill="#aab3bb"/><ellipse cx="40" cy="30" rx="28" ry="7" fill="#e8edf0"/><ellipse class="bkp" id="bkc" cx="40" cy="31" rx="23" ry="5" fill="${COL[0]}"/><path class="bkp bkd" d="M17 31q-3 14 1 20q4-3 2-20z" fill="${COL[0]}"/><rect x="22" y="48" width="36" height="14" rx="4" fill="#fff" opacity=".85"/><circle cx="40" cy="55" r="4" fill="${COL[0]}" class="bkp bkd"/></g></svg>`;document.body.appendChild(bk);}
const setCol=()=>{const c=COL[ci];if(bk)bk.querySelectorAll('.bkp').forEach(e=>e.setAttribute('fill',c))};
addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;if(bk){bk.classList.add('on');const t=e.target;bk.classList.toggle('hide',!!(t.closest&&t.closest('input,textarea,select,#lb')))}},{passive:true});
document.addEventListener('mouseleave',()=>bk&&bk.classList.remove('on'));
addEventListener('pointerdown',e=>{if(e.target.closest&&e.target.closest('input,textarea,select'))return;const c=COL[ci];splat(e.clientX,e.clientY,rnd(10,17),c);burst(e.clientX,e.clientY,9,c);ci=(ci+1)%COL.length;setCol();if(bk){bk.classList.add('pop');setTimeout(()=>bk.classList.remove('pop'),140)}});
let last=0;function loop(ts){requestAnimationFrame(loop);if(document.hidden)return;
if(bk&&mx>-50){bx+=(mx-bx)*.16;by+=(my-by)*.16;const vx=bx-px;px=bx;const tilt=Math.max(-38,Math.min(38,vx*3));sc+=(tilt-sc)*.2;bk.style.transform=`translate(${bx+8}px,${by+6}px)`;bk.firstChild.firstChild.style.transform=`rotate(${sc}deg)`;
if(Math.abs(vx)>3&&Math.random()<.28){const lip=sc>0?38:12;dr.push({x:bx+6+lip*.6,y:by+4+18,vx:vx*.1+rnd(-.5,.5),vy:rnd(0,.8),r:rnd(1,2.4),c:COL[ci],g:.38,land:by+rnd(40,130)})}}
x.clearRect(0,0,W,H);
for(let i=dr.length-1;i>=0;i--){const d=dr[i];d.vy+=d.g;d.x+=d.vx;d.y+=d.vy;x.globalAlpha=.5;x.fillStyle=d.c;x.beginPath();x.arc(d.x,d.y,d.r,0,6.2832);x.fill();if(d.y>=d.land||d.y>H){splat(d.x,Math.min(d.y,H-2),d.r*rnd(1.3,2),d.c);dr.splice(i,1)}}
const now=performance.now();for(let i=sp.length-1;i>=0;i--){const s=sp[i],a=now-s.t,L=3600;if(a>L){sp.splice(i,1);continue}blob(s,a<1600?.42:.42*(1-(a-1600)/(L-1600)))}x.globalAlpha=1}
requestAnimationFrame(loop);
/* بقع الخلفية تتحرك مع التمرير */
const st=[...document.querySelectorAll('.stain')];if(st.length){st.forEach((e,i)=>e._f=(i%2?-1:1)*(.05+(i%4)*.025));let tk=0;addEventListener('scroll',()=>{if(tk)return;tk=requestAnimationFrame(()=>{tk=0;st.forEach(e=>e.style.setProperty('--py',(scrollY*e._f)+'px'))})},{passive:true})}
})();
