import{k as r,$ as F,Z as it,A as rt,r as dt,o as i,c as _,p as a,w as o,q as s,m as t,D as c,b as p,E as I,y as $,F as z,a as L,x as d,C as S,H as ut,I as ct,bf as W}from"./main.80073fb3.js";import{_ as pt}from"./DialogCloseBtn.0b4ca758.js";import{u as mt,_ as ft,a as _t,b as vt,c as ht}from"./ClientMonthlyReport.56f76e0d.js";import{P as D}from"./permissions.bafe5f34.js";import"./jspdf.es.min.09750773.js";import"./jspdf.plugin.autotable.993ce2f6.js";import{a as xt}from"./VOverlay.ba4319b9.js";import{a as Z,V as x}from"./VBtn.bc513066.js";import{a as M,V as G}from"./VRow.e327326a.js";import{V as K,c as j}from"./VCard.bed48467.js";import{V as B}from"./VDivider.c8bf408f.js";import{V as Vt}from"./VSelect.eb06e1d1.js";import{V as gt}from"./VSpacer.f6e4a849.js";import{V as Ct}from"./VTextField.f7f47561.js";import{V as J}from"./VTable.6035f281.js";import{V as wt}from"./VAvatar.755a82ce.js";import{V as yt}from"./VImg.19a712b4.js";import{V as bt}from"./VPagination.0ced2f02.js";import{V as kt,a as Dt}from"./VTabs.c380e8cb.js";import{V as Tt,a as O}from"./VWindowItem.cc08cc0a.js";import{V as At}from"./VDialog.809ecb4b.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VForm.afb9be88.js";import"./VInput.b30e6f3e.js";import"./router.9f57d7dd.js";import"./index.4672e601.js";import"./forwardRefs.c003b6b8.js";import"./VAutocomplete.a415af86.js";import"./filter.7efe6ffd.js";import"./VList.dace6270.js";import"./VMenu.5e377b50.js";import"./scopeId.7979a6ee.js";import"./dialog-transition.4fc1b9b4.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.9ec2021d.js";import"./VSelectionControl.816c4425.js";import"./VChip.1cdee411.js";import"./VNavigationDrawer.7eedad6d.js";import"./ssrBoot.2abef11c.js";import"./vue3-apexcharts.common.8bdf0a30.js";import"./vue.runtime.esm-bundler.d776eb28.js";import"./lazy.172a4f89.js";import"./position.a8816655.js";/* empty css                   */import"./VField.95c31d27.js";import"./VCounter.770fe3f3.js";import"./VSlideGroup.43a5391b.js";const Et={class:"me-3",style:{width:"80px"}},Ut={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},It={style:{width:"9rem"}},zt=t("thead",null,[t("tr",null,[t("th",{scope:"col"}," ID "),t("th",{scope:"col"}," Client "),t("th",{scope:"col"}," City "),t("th",{scope:"col"}," Phone "),t("th",{scope:"col"}," Balance "),t("th",{scope:"col"}," ACTIONS ")])],-1),Lt={class:"d-flex align-center"},St={key:1},Nt={class:"d-flex flex-column"},Pt={class:"text-base"},$t={class:"text-sm text-disabled"},Bt={class:"text-capitalize text-base font-weight-semibold"},Ot={class:"text-capitalize text-base font-weight-semibold"},Rt={class:"text-capitalize text-base font-weight-semibold"},Ft={class:"text-center",style:{width:"5rem"}},jt=t("tr",null,[t("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Jt=[jt],qt={class:"text-sm text-disabled"},Ht=t("thead",null,[t("tr",null,[t("th",{class:"text-caption"}," Product "),t("th",{class:"text-caption"}," Date "),t("th",{class:"text-caption"}," Quantity "),t("th",{class:"text-caption"}," Price "),t("th",{class:"text-caption"}," Total ")])],-1),Qt={class:"text-caption"},Wt={class:"text-caption"},Zt={class:"text-caption"},Mt={class:"text-caption"},Gt={class:"text-caption"},Kt=t("td",null,null,-1),Xt=t("td",null,null,-1),Yt=t("td",null,null,-1),te=t("td",{class:"text-caption"}," Total ",-1),ee={class:"text-caption"},le=t("td",null,null,-1),ae=t("td",null,null,-1),oe=t("td",null,null,-1),se=t("td",{class:"text-caption"}," Payment ",-1),ne={class:"text-caption"},ie=t("td",null,null,-1),re=t("td",null,null,-1),de=t("td",null,null,-1),ue=t("td",{class:"text-caption"}," Rest ",-1),ce={class:"text-caption"},pe=t("thead",null,[t("tr",null,[t("th",null," Date "),t("th",null," Amount ")])],-1),me={__name:"index",setup(fe){const T=mt(),A=r(""),q=r(!1),X=r(!0),V=r(!1);r(),r(),r();const w=r(10),m=r(1),y=r(1),H=r(0),g=r([]);let b=r([]),Y=r([]),h=r("Appetizers"),C=r({isActive:!1});const v=[{id:1,name:"Home"},{id:2,name:"Record"},{id:3,name:"Payments"},{id:4,name:"State"}],E=()=>{C.value.isActive=!0,T.fetchClients({searchValue:A.value,perPage:w.value,currentPage:m.value}).then(n=>{C.value.isActive=!1,b.value=n.data.clients.data,console.log(n.data.clientsAll),Y.value=n.data.clientsAll,console.log(b.value),y.value=n.data.totalPage,g.value=n.data.cities,H.value=n.data.totalClients,q.value=!1}).catch(n=>{console.error(n),C.value.isActive=!1})};F(E),F(()=>{m.value>y.value&&(m.value=y.value)}),it(A,()=>{E()});const N=r(!1),P=r(!1),U=r(!1);let u=r();F(()=>{m.value>y.value&&(m.value=y.value)});const tt=rt(()=>{const n=b.value.length?(m.value-1)*w.value+1:0,l=b.value.length+(m.value-1)*w.value;return`Showing ${n} to ${l} of ${H.value} entries`}),et=n=>{T.addClient(n).then(l=>{E()}).catch(l=>{console.log(l)})},lt=n=>{T.updateClient(n).then(l=>{E()}).catch(l=>{console.log(l)})},at=n=>{T.deleteClient(n).then(l=>{E()}).catch(l=>{console.log(l)})},ot=n=>{P.value=!0,u=n},st=n=>{U.value=!0,u=n},nt=n=>{V.value=!0,u=n},Q=n=>{C.value.isActive=!0,T.getLogList(n).then(l=>{C.value.isActive=!1;const R=window.URL.createObjectURL(new Blob([l.data])),k=document.createElement("a");k.href=R,k.setAttribute("download","client_log.pdf"),document.body.appendChild(k),k.click()}).catch(l=>{console.error(l),C.value.isActive=!1})};return(n,l)=>{const R=dt("RouterLink"),k=pt;return i(),_("section",null,[a(G,null,{default:o(()=>[a(xt,{"model-value":s(C).isActive,class:"align-center justify-center"},{default:o(()=>[a(Z,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),a(M,{cols:"12"},{default:o(()=>[a(K,{title:"Clients"},{default:o(()=>[a(B),a(j,{class:"d-flex flex-wrap py-4 gap-4"},{default:o(()=>[t("div",Et,[a(Vt,{modelValue:s(w),"onUpdate:modelValue":l[0]||(l[0]=e=>c(w)?w.value=e:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),a(gt),t("div",Ut,[t("div",It,[s(q)?(i(),p(Z,{key:0,indeterminate:"",color:"primary"})):(i(),p(Ct,{key:1,onInput:l[1]||(l[1]=e=>X.value=!0),ref:"searchField",modelValue:s(A),"onUpdate:modelValue":l[2]||(l[2]=e=>c(A)?A.value=e:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),a(x,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share",onClick:Q},{default:o(()=>[I(" Export ")]),_:1}),n.$can(s(D).CLIENT.ADD,s(D).CLIENT.SUBJECT)?(i(),p(x,{key:0,"prepend-icon":"tabler-plus",onClick:l[3]||(l[3]=e=>N.value=!0)},{default:o(()=>[I(" Add New Client ")]),_:1})):$("",!0)])]),_:1}),a(B),a(J,{class:"text-no-wrap"},{default:o(()=>[zt,t("tbody",null,[(i(!0),_(z,null,L(s(b),e=>(i(),_("tr",{key:e.id,style:{height:"3.75rem"}},[t("td",null,d(e.id),1),t("td",null,[t("div",Lt,[a(wt,{variant:"tonal",class:"me-3",size:"38"},{default:o(()=>[e.avatar?(i(),p(yt,{key:0,src:e.avatar},null,8,["src"])):(i(),_("span",St,d(e.name.toUpperCase().charAt(0)),1))]),_:2},1024),t("div",Nt,[t("h6",Pt,[a(R,{to:{name:"apps-user-view-id",params:{id:e.id}},class:"font-weight-medium user-list-name"},{default:o(()=>[I(d(e.name),1)]),_:2},1032,["to"])]),t("span",$t,"@"+d(e.email),1)])])]),t("td",null,[t("span",Bt,d(e.city.name),1)]),t("td",null,[t("span",Ot,d(e.phone),1)]),t("td",null,[t("span",Rt,d(parseFloat(e.balance.balance).toFixed(2))+" DZD",1)]),t("td",Ft,[a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:f=>Q(e.id)},{default:o(()=>[a(S,{size:"22",icon:"tabler-currency-dollar"})]),_:2},1032,["onClick"]),a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:f=>nt(e)},{default:o(()=>[a(S,{size:"22",icon:"tabler-shopping-cart"})]),_:2},1032,["onClick"]),n.$can(s(D).CLIENT.EDIT,s(D).CLIENT.SUBJECT)?(i(),p(x,{key:0,icon:"",size:"x-small",color:"default",variant:"text",onClick:f=>ot(e)},{default:o(()=>[a(S,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"])):$("",!0),n.$can(s(D).CLIENT.DELETE,s(D).CLIENT.SUBJECT)?(i(),p(x,{key:1,icon:"",size:"x-small",color:"default",variant:"text",onClick:f=>st(e)},{default:o(()=>[a(S,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"])):$("",!0),a(x,{icon:"",size:"x-small",color:"default",variant:"text"},{default:o(()=>[a(S,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),ut(t("tfoot",null,Jt,512),[[ct,!s(b).length]])]),_:1}),a(B),a(j,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:o(()=>[t("span",qt,d(s(tt)),1),a(bt,{modelValue:s(m),"onUpdate:modelValue":l[4]||(l[4]=e=>c(m)?m.value=e:null),size:"small","total-visible":5,length:s(y)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),a(At,{modelValue:s(V),"onUpdate:modelValue":l[9]||(l[9]=e=>c(V)?V.value=e:null),scrollable:"",class:"v-dialog-sm","max-width":"700",width:"auto","content-class":"scrollable-dialog"},{default:o(()=>[a(k,{onClick:l[5]||(l[5]=e=>V.value=!s(V))}),a(K,{title:" "},{default:o(()=>[a(j,{class:"d-flex justify-end gap-3 flex-wrap"},{default:o(()=>[a(kt,{modelValue:s(h),"onUpdate:modelValue":l[6]||(l[6]=e=>c(h)?h.value=e:h=e),grow:""},{default:o(()=>[(i(),_(z,null,L(v,e=>a(Dt,{key:e.id,value:e.id},{default:o(()=>[I(d(e.name),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),a(B),a(Tt,{modelValue:s(h),"onUpdate:modelValue":l[7]||(l[7]=e=>c(h)?h.value=e:h=e),class:"mt-6 pl-3 pa-1"},{default:o(()=>[(i(),p(O,{key:v[0].id,value:v[0].id,width:"150%"},{default:o(()=>[a(G,null,{default:o(()=>[a(M,{cols:"6",md:"6"},{default:o(()=>[a(ft)]),_:1})]),_:1})]),_:1},8,["value"])),(i(),p(O,{key:v[1].id,value:v[1].id,class:"mt-0",style:{"margin-top":"0 !important"}},{default:o(()=>[(i(!0),_(z,null,L(s(u).sales,e=>(i(),p(J,{class:"text-no-wrap mt-0 mb-10"},{default:o(()=>[Ht,t("tbody",null,[(i(!0),_(z,null,L(e.sale_items,f=>(i(),_("tr",{key:f.id},[t("td",Qt,d(f.product.name),1),t("td",Wt,d(f.sale_date),1),t("td",Zt,d(f.quantity),1),t("td",Mt,d(f.price),1),t("td",Gt,d(f.total_price),1)]))),128)),t("tr",null,[Kt,Xt,Yt,te,t("td",ee,d(e.total_amount),1)]),t("tr",null,[le,ae,oe,se,t("td",ne,d(e.regulation),1)]),t("tr",null,[ie,re,de,ue,t("td",ce,d(e.balance),1)])])]),_:2},1024))),256))]),_:1},8,["value"])),(i(),p(O,{key:v[2].id,value:v[2].id,class:"mt-0",style:{width:"-webkit-fill-available"},width:"100%"},{default:o(()=>[a(J,{class:"text-no-wrap mt-0",style:{width:"-webkit-fill-available"}},{default:o(()=>[pe,t("tbody",null,[(i(!0),_(z,null,L(s(u).payments,e=>(i(),_("tr",{key:e.id},[t("td",null,d(e.payment_date),1),t("td",null,d(e.amount_paid),1)]))),128))])]),_:1})]),_:1},8,["value"])),(i(),p(O,{key:v[3].id,value:v[3].id},null,8,["value"]))]),_:1},8,["modelValue"]),a(x,{onClick:l[8]||(l[8]=e=>V.value=!1)},{default:o(()=>[I(" Close ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),a(_t,{isDrawerOpen:s(N),"onUpdate:isDrawerOpen":l[10]||(l[10]=e=>c(N)?N.value=e:null),cities:s(g),"onUpdate:cities":l[11]||(l[11]=e=>c(g)?g.value=e:null),onClientData:et},null,8,["isDrawerOpen","cities"]),a(vt,{isDrawerOpen:s(P),"onUpdate:isDrawerOpen":l[12]||(l[12]=e=>c(P)?P.value=e:null),client:s(u),"onUpdate:client":l[13]||(l[13]=e=>c(u)?u.value=e:u=e),cities:s(g),"onUpdate:cities":l[14]||(l[14]=e=>c(g)?g.value=e:null),onClientData:lt},null,8,["isDrawerOpen","client","cities"]),s(U)?(i(),p(ht,{key:0,isDialogVisible:s(U),"onUpdate:isDialogVisible":l[15]||(l[15]=e=>c(U)?U.value=e:null),client:s(u),"onUpdate:client":l[16]||(l[16]=e=>c(u)?u.value=e:u=e),onClientData:at},null,8,["isDialogVisible","client"])):$("",!0)])}}};typeof W=="function"&&W(me);export{me as default};