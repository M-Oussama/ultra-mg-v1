import{bd as x,k as _,Z as C,q as l,o,b as n,w as e,p as t,m as k,x as m,D as b,c as p,a as d,E as v,F as f,C as V,y as T,bf as y,a1 as w}from"./main.80073fb3.js";import{V as B,a as D}from"./VTabs.c380e8cb.js";import{a as h,V as I}from"./VRow.e327326a.js";import{V as L,a as N}from"./VWindowItem.cc08cc0a.js";import{V as z,c as R}from"./VCard.bed48467.js";import{V as E}from"./VAvatar.755a82ce.js";import{V as F,a as H,b as U}from"./VList.dace6270.js";import{V as W}from"./VBtn.bc513066.js";import"./router.9f57d7dd.js";import"./easing.36b781ab.js";import"./VSlideGroup.43a5391b.js";import"./index.4672e601.js";import"./lazy.172a4f89.js";import"./VImg.19a712b4.js";import"./ssrBoot.2abef11c.js";import"./position.a8816655.js";import"./VDivider.c8bf408f.js";const q={class:"text-base mb-3"},A=k("span",null,"Back to help center",-1),S={__name:"index",setup(Z){const g=x(),r=_(),s=_("");return C(s,()=>w.get("/pages/help-center/subcategory",{params:{category:g.params.category,subcategory:g.params.subcategory}}).then(u=>{r.value=u.data.data,s.value=u.data.activeTab}),{immediate:!0}),(u,i)=>l(r)?(o(),n(I,{key:0},{default:e(()=>[t(h,{cols:"12",md:"4"},{default:e(()=>[k("h6",q,m(l(r).title),1),t(B,{modelValue:l(s),"onUpdate:modelValue":i[0]||(i[0]=a=>b(s)?s.value=a:null),direction:"vertical",class:"v-tabs-pill"},{default:e(()=>[(o(!0),p(f,null,d(l(r).subCategories,a=>(o(),n(D,{key:a.slug,value:a.slug,to:{name:"pages-help-center-category-subcategory",params:{category:l(r).slug,subcategory:a.slug}}},{default:e(()=>[v(m(a.title),1)]),_:2},1032,["value","to"]))),128))]),_:1},8,["modelValue"])]),_:1}),t(h,{cols:"12",md:"8"},{default:e(()=>[t(L,{modelValue:l(s),"onUpdate:modelValue":i[1]||(i[1]=a=>b(s)?s.value=a:null),class:"disable-tab-transition"},{default:e(()=>[(o(!0),p(f,null,d(l(r).subCategories,a=>(o(),n(N,{key:a.slug,value:a.slug},{default:e(()=>[t(z,{title:a.title},{prepend:e(()=>[t(E,{color:"primary",variant:"tonal",rounded:"",size:"42"},{default:e(()=>[t(V,{icon:a.icon,size:"26"},null,8,["icon"])]),_:2},1024)]),default:e(()=>[t(R,null,{default:e(()=>[t(F,{class:"card-list mb-6"},{default:e(()=>[(o(!0),p(f,null,d(a.articles,c=>(o(),n(H,{key:c.slug,to:{name:"pages-help-center-category-subcategory-article",params:{category:l(r).slug,subcategory:a.slug,article:c.slug}}},{prepend:e(()=>[t(V,{icon:"tabler-chevron-right",size:"18",class:"me-2"})]),default:e(()=>[t(U,{class:"text-primary"},{default:e(()=>[v(m(c.title),1)]),_:2},1024)]),_:2},1032,["to"]))),128))]),_:2},1024),t(W,{variant:"tonal",to:{name:"pages-help-center"}},{default:e(()=>[t(V,{icon:"tabler-chevron-left",class:"flip-in-rtl"}),A]),_:1})]),_:2},1024)]),_:2},1032,["title"])]),_:2},1032,["value"]))),128))]),_:1},8,["modelValue"])]),_:1})]),_:1})):T("",!0)}};typeof y=="function"&&y(S);export{S as default};