import{a6 as v,az as h,am as w,aE as p,k as y,Z as d,an as B,ae as f,p as a,F as D,O as x,ax as F}from"./main.8e3d834c.js";import{V as I}from"./router.96bf7663.js";import{V as C}from"./VBtn.d45a6048.js";import{V as S}from"./position.c80f08b0.js";import{V as T,a as k}from"./VWindowItem.011c0acb.js";import{V as A}from"./VImg.5f2c6d35.js";const M=v({name:"VCarousel",props:{color:String,cycle:Boolean,delimiterIcon:{type:h,default:"$delimiter"},height:{type:[Number,String],default:500},hideDelimiters:Boolean,hideDelimiterBackground:Boolean,interval:{type:[Number,String],default:6e3,validator:e=>e>0},modelValue:null,progress:[Boolean,String],showArrows:{type:[Boolean,String],default:!0,validator:e=>typeof e=="boolean"||e==="hover"},verticalDelimiters:[Boolean,String]},emits:{"update:modelValue":e=>!0},setup(e,o){let{slots:t}=o;const s=w(e,"modelValue"),{t:b}=p(),n=y();let u=-1;d(s,c),d(()=>e.interval,c),d(()=>e.cycle,i=>{i?c():window.clearTimeout(u)}),B(m);function m(){!e.cycle||!n.value||(u=window.setTimeout(n.value.group.next,+e.interval>0?+e.interval:6e3))}function c(){window.clearTimeout(u),window.requestAnimationFrame(m)}return f(()=>a(T,{ref:n,modelValue:s.value,"onUpdate:modelValue":i=>s.value=i,class:["v-carousel",{"v-carousel--hide-delimiter-background":e.hideDelimiterBackground,"v-carousel--vertical-delimiters":e.verticalDelimiters}],style:{height:F(e.height)},continuous:!0,mandatory:"force",showArrows:e.showArrows},{default:t.default,additional:i=>{let{group:l}=i;return a(D,null,[!e.hideDelimiters&&a("div",{class:"v-carousel__controls",style:{left:e.verticalDelimiters==="left"&&e.verticalDelimiters?0:"auto",right:e.verticalDelimiters==="right"?0:"auto"}},[l.items.value.length>0&&a(I,{defaults:{VBtn:{color:e.color,icon:e.delimiterIcon,size:"x-small",variant:"text"}},scoped:!0},{default:()=>[l.items.value.map((r,V)=>{const g={"aria-label":b("$vuetify.carousel.ariaLabel.delimiter",V+1,l.items.value.length),class:[l.isSelected(r.id)&&"v-btn--active"],onClick:()=>l.select(r.id,!0)};return t.item?t.item({props:g,item:r}):a(C,x(r,g),null)})]})]),e.progress&&a(S,{class:"v-carousel__progress",color:typeof e.progress=="string"?e.progress:void 0,modelValue:(l.getItemIndex(s.value)+1)/l.items.value.length*100},null)])},prev:t.prev,next:t.next})),{}}}),R=v({name:"VCarouselItem",inheritAttrs:!1,props:{value:null},setup(e,o){let{slots:t,attrs:s}=o;f(()=>a(k,{class:"v-carousel-item",value:e.value},{default:()=>[a(A,s,t)]}))}}),U="/build/assets/au.5380d9be.png",W="/build/assets/br.36ca37c4.png",q="/build/assets/cn.afd5c222.png",E="/build/assets/fr.90409c73.png",O="/build/assets/in.08eaaccd.png",Z="/build/assets/us.bfe9c650.png",j="/build/assets/sidebar-pic-1.24754ce8.png",G="/build/assets/sidebar-pic-2.a64f09c4.png",H="/build/assets/sidebar-pic-3.c8e8b9bf.png";export{M as V,U as a,W as b,q as c,R as d,G as e,E as f,H as g,O as i,j as s,Z as u};