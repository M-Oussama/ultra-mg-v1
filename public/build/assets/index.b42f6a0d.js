import{k as i,$ as R,Z as ot,A as st,r as nt,o as d,c as _,p as a,w as o,q as n,m as t,D as c,b as v,E as P,F as $,a as S,x as r,C as T,H as it,I as dt,y as rt}from"./main.cf9f31a9.js";import{_ as ut}from"./DialogCloseBtn.1c4165b0.js";import{u as ct,_ as pt,a as mt,b as _t,c as ft}from"./ClientMonthlyReport.766d2a8a.js";import"./jspdf.es.min.aecddb65.js";import"./jspdf.plugin.autotable.81fc07ed.js";import{a as vt}from"./VOverlay.9d9650d6.js";import{a as Q,V as x}from"./VBtn.d73656ae.js";import{a as W,V as Z}from"./VRow.a394feb3.js";import{V as G,c as E}from"./VCard.a1247ba2.js";import{V as N}from"./VDivider.01acf602.js";import{V as ht}from"./VSelect.32c4b2dd.js";import{V as xt}from"./VSpacer.6ec948ae.js";import{V as Vt}from"./VTextField.9102871e.js";import{V as F}from"./VTable.60781dfc.js";import{V as gt}from"./VAvatar.9270a597.js";import{V as wt}from"./VImg.134af49b.js";import{V as bt}from"./VPagination.7fcfaf73.js";import{V as yt,a as Ct}from"./VTabs.0ae1bb3a.js";import{V as kt,a as O}from"./VWindowItem.3f52cdcf.js";import{V as Dt}from"./VDialog.ac89ffbe.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VForm.f1d9c3ac.js";import"./VInput.d6b4ca43.js";import"./router.1658937d.js";import"./index.d1f0132a.js";import"./forwardRefs.c003b6b8.js";import"./VAutocomplete.dab6d47c.js";import"./filter.73bf6f77.js";import"./VList.a34172e8.js";import"./VMenu.3df9bfa4.js";import"./scopeId.08611610.js";import"./dialog-transition.b0009893.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.b3570faf.js";import"./VSelectionControl.5a5e7888.js";import"./VChip.6c2e2c44.js";import"./VNavigationDrawer.2d863e08.js";import"./ssrBoot.e94d367d.js";import"./vue3-apexcharts.common.64a065dd.js";import"./vue.runtime.esm-bundler.899117cb.js";import"./lazy.f959659a.js";import"./position.b360a9d1.js";/* empty css                   */import"./VField.6a2ec846.js";import"./VCounter.4f92d6c5.js";import"./VSlideGroup.ee136b32.js";const At={class:"me-3",style:{width:"80px"}},zt={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},Ut={style:{width:"9rem"}},Pt=t("thead",null,[t("tr",null,[t("th",{scope:"col"}," ID "),t("th",{scope:"col"}," Client "),t("th",{scope:"col"}," City "),t("th",{scope:"col"}," Phone "),t("th",{scope:"col"}," Balance "),t("th",{scope:"col"}," ACTIONS ")])],-1),$t={class:"d-flex align-center"},St={key:1},Tt={class:"d-flex flex-column"},Lt={class:"text-base"},It={class:"text-sm text-disabled"},Nt={class:"text-capitalize text-base font-weight-semibold"},Ot={class:"text-capitalize text-base font-weight-semibold"},Bt={class:"text-capitalize text-base font-weight-semibold"},Rt={class:"text-center",style:{width:"5rem"}},Et=t("tr",null,[t("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Ft=[Et],jt={class:"text-sm text-disabled"},qt=t("thead",null,[t("tr",null,[t("th",{class:"text-caption"}," Product "),t("th",{class:"text-caption"}," Date "),t("th",{class:"text-caption"}," Quantity "),t("th",{class:"text-caption"}," Price "),t("th",{class:"text-caption"}," Total ")])],-1),Ht={class:"text-caption"},Qt={class:"text-caption"},Wt={class:"text-caption"},Zt={class:"text-caption"},Gt={class:"text-caption"},Jt=t("td",null,null,-1),Kt=t("td",null,null,-1),Mt=t("td",null,null,-1),Xt=t("td",{class:"text-caption"}," Total ",-1),Yt={class:"text-caption"},te=t("td",null,null,-1),ee=t("td",null,null,-1),le=t("td",null,null,-1),ae=t("td",{class:"text-caption"}," Payment ",-1),oe={class:"text-caption"},se=t("td",null,null,-1),ne=t("td",null,null,-1),ie=t("td",null,null,-1),de=t("td",{class:"text-caption"}," Rest ",-1),re={class:"text-caption"},ue=t("thead",null,[t("tr",null,[t("th",null," Date "),t("th",null," Amount ")])],-1),sl={__name:"index",setup(ce){const D=ct(),A=i(""),j=i(!1),J=i(!0),V=i(!1);i(),i(),i();const b=i(10),p=i(1),y=i(1),q=i(0),g=i([]);let C=i([]),K=i([]),h=i("Appetizers"),w=i({isActive:!1});const f=[{id:1,name:"Home"},{id:2,name:"Record"},{id:3,name:"Payments"},{id:4,name:"State"}],z=()=>{w.value.isActive=!0,D.fetchClients({searchValue:A.value,perPage:b.value,currentPage:p.value}).then(s=>{w.value.isActive=!1,C.value=s.data.clients.data,console.log(s.data.clientsAll),K.value=s.data.clientsAll,console.log(C.value),y.value=s.data.totalPage,g.value=s.data.cities,q.value=s.data.totalClients,j.value=!1}).catch(s=>{console.error(s),w.value.isActive=!1})};R(z),R(()=>{p.value>y.value&&(p.value=y.value)}),ot(A,()=>{z()});const L=i(!1),I=i(!1),U=i(!1);let u=i();R(()=>{p.value>y.value&&(p.value=y.value)});const M=st(()=>{const s=C.value.length?(p.value-1)*b.value+1:0,l=C.value.length+(p.value-1)*b.value;return`Showing ${s} to ${l} of ${q.value} entries`}),X=s=>{D.addClient(s).then(l=>{z()}).catch(l=>{console.log(l)})},Y=s=>{D.updateClient(s).then(l=>{z()}).catch(l=>{console.log(l)})},tt=s=>{D.deleteClient(s).then(l=>{z()}).catch(l=>{console.log(l)})},et=s=>{I.value=!0,u=s},lt=s=>{U.value=!0,u=s},at=s=>{V.value=!0,u=s},H=s=>{w.value.isActive=!0,D.getLogList(s).then(l=>{w.value.isActive=!1;const B=window.URL.createObjectURL(new Blob([l.data])),k=document.createElement("a");k.href=B,k.setAttribute("download","client_log.pdf"),document.body.appendChild(k),k.click()}).catch(l=>{console.error(l),w.value.isActive=!1})};return(s,l)=>{const B=nt("RouterLink"),k=ut;return d(),_("section",null,[a(Z,null,{default:o(()=>[a(vt,{"model-value":n(w).isActive,class:"align-center justify-center"},{default:o(()=>[a(Q,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),a(W,{cols:"12"},{default:o(()=>[a(G,{title:"Clients"},{default:o(()=>[a(N),a(E,{class:"d-flex flex-wrap py-4 gap-4"},{default:o(()=>[t("div",At,[a(ht,{modelValue:n(b),"onUpdate:modelValue":l[0]||(l[0]=e=>c(b)?b.value=e:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),a(xt),t("div",zt,[t("div",Ut,[n(j)?(d(),v(Q,{key:0,indeterminate:"",color:"primary"})):(d(),v(Vt,{key:1,onInput:l[1]||(l[1]=e=>J.value=!0),ref:"searchField",modelValue:n(A),"onUpdate:modelValue":l[2]||(l[2]=e=>c(A)?A.value=e:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),a(x,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share",onClick:H},{default:o(()=>[P(" Export ")]),_:1}),a(x,{"prepend-icon":"tabler-plus",onClick:l[3]||(l[3]=e=>L.value=!0)},{default:o(()=>[P(" Add New Client ")]),_:1})])]),_:1}),a(N),a(F,{class:"text-no-wrap"},{default:o(()=>[Pt,t("tbody",null,[(d(!0),_($,null,S(n(C),e=>(d(),_("tr",{key:e.id,style:{height:"3.75rem"}},[t("td",null,r(e.id),1),t("td",null,[t("div",$t,[a(gt,{variant:"tonal",class:"me-3",size:"38"},{default:o(()=>[e.avatar?(d(),v(wt,{key:0,src:e.avatar},null,8,["src"])):(d(),_("span",St,r(e.name.toUpperCase().charAt(0)),1))]),_:2},1024),t("div",Tt,[t("h6",Lt,[a(B,{to:{name:"apps-user-view-id",params:{id:e.id}},class:"font-weight-medium user-list-name"},{default:o(()=>[P(r(e.name),1)]),_:2},1032,["to"])]),t("span",It,"@"+r(e.email),1)])])]),t("td",null,[t("span",Nt,r(e.city.name),1)]),t("td",null,[t("span",Ot,r(e.phone),1)]),t("td",null,[t("span",Bt,r(parseFloat(e.balance.balance).toFixed(2))+" DZD",1)]),t("td",Rt,[a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:m=>H(e.id)},{default:o(()=>[a(T,{size:"22",icon:"tabler-currency-dollar"})]),_:2},1032,["onClick"]),a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:m=>at(e)},{default:o(()=>[a(T,{size:"22",icon:"tabler-shopping-cart"})]),_:2},1032,["onClick"]),a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:m=>et(e)},{default:o(()=>[a(T,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"]),a(x,{icon:"",size:"x-small",color:"default",variant:"text",onClick:m=>lt(e)},{default:o(()=>[a(T,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),a(x,{icon:"",size:"x-small",color:"default",variant:"text"},{default:o(()=>[a(T,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),it(t("tfoot",null,Ft,512),[[dt,!n(C).length]])]),_:1}),a(N),a(E,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:o(()=>[t("span",jt,r(n(M)),1),a(bt,{modelValue:n(p),"onUpdate:modelValue":l[4]||(l[4]=e=>c(p)?p.value=e:null),size:"small","total-visible":5,length:n(y)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),a(Dt,{modelValue:n(V),"onUpdate:modelValue":l[9]||(l[9]=e=>c(V)?V.value=e:null),scrollable:"",class:"v-dialog-sm","max-width":"700",width:"auto","content-class":"scrollable-dialog"},{default:o(()=>[a(k,{onClick:l[5]||(l[5]=e=>V.value=!n(V))}),a(G,{title:" "},{default:o(()=>[a(E,{class:"d-flex justify-end gap-3 flex-wrap"},{default:o(()=>[a(yt,{modelValue:n(h),"onUpdate:modelValue":l[6]||(l[6]=e=>c(h)?h.value=e:h=e),grow:""},{default:o(()=>[(d(),_($,null,S(f,e=>a(Ct,{key:e.id,value:e.id},{default:o(()=>[P(r(e.name),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),a(N),a(kt,{modelValue:n(h),"onUpdate:modelValue":l[7]||(l[7]=e=>c(h)?h.value=e:h=e),class:"mt-6 pl-3 pa-1"},{default:o(()=>[(d(),v(O,{key:f[0].id,value:f[0].id,width:"150%"},{default:o(()=>[a(Z,null,{default:o(()=>[a(W,{cols:"6",md:"6"},{default:o(()=>[a(pt)]),_:1})]),_:1})]),_:1},8,["value"])),(d(),v(O,{key:f[1].id,value:f[1].id,class:"mt-0",style:{"margin-top":"0 !important"}},{default:o(()=>[(d(!0),_($,null,S(n(u).sales,e=>(d(),v(F,{class:"text-no-wrap mt-0 mb-10"},{default:o(()=>[qt,t("tbody",null,[(d(!0),_($,null,S(e.sale_items,m=>(d(),_("tr",{key:m.id},[t("td",Ht,r(m.product.name),1),t("td",Qt,r(m.sale_date),1),t("td",Wt,r(m.quantity),1),t("td",Zt,r(m.price),1),t("td",Gt,r(m.total_price),1)]))),128)),t("tr",null,[Jt,Kt,Mt,Xt,t("td",Yt,r(e.total_amount),1)]),t("tr",null,[te,ee,le,ae,t("td",oe,r(e.regulation),1)]),t("tr",null,[se,ne,ie,de,t("td",re,r(e.balance),1)])])]),_:2},1024))),256))]),_:1},8,["value"])),(d(),v(O,{key:f[2].id,value:f[2].id,class:"mt-0",style:{width:"-webkit-fill-available"},width:"100%"},{default:o(()=>[a(F,{class:"text-no-wrap mt-0",style:{width:"-webkit-fill-available"}},{default:o(()=>[ue,t("tbody",null,[(d(!0),_($,null,S(n(u).payments,e=>(d(),_("tr",{key:e.id},[t("td",null,r(e.payment_date),1),t("td",null,r(e.amount_paid),1)]))),128))])]),_:1})]),_:1},8,["value"])),(d(),v(O,{key:f[3].id,value:f[3].id},null,8,["value"]))]),_:1},8,["modelValue"]),a(x,{onClick:l[8]||(l[8]=e=>V.value=!1)},{default:o(()=>[P(" Close ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),a(mt,{isDrawerOpen:n(L),"onUpdate:isDrawerOpen":l[10]||(l[10]=e=>c(L)?L.value=e:null),cities:n(g),"onUpdate:cities":l[11]||(l[11]=e=>c(g)?g.value=e:null),onClientData:X},null,8,["isDrawerOpen","cities"]),a(_t,{isDrawerOpen:n(I),"onUpdate:isDrawerOpen":l[12]||(l[12]=e=>c(I)?I.value=e:null),client:n(u),"onUpdate:client":l[13]||(l[13]=e=>c(u)?u.value=e:u=e),cities:n(g),"onUpdate:cities":l[14]||(l[14]=e=>c(g)?g.value=e:null),onClientData:Y},null,8,["isDrawerOpen","client","cities"]),n(U)?(d(),v(ft,{key:0,isDialogVisible:n(U),"onUpdate:isDialogVisible":l[15]||(l[15]=e=>c(U)?U.value=e:null),client:n(u),"onUpdate:client":l[16]||(l[16]=e=>c(u)?u.value=e:u=e),onClientData:tt},null,8,["isDialogVisible","client"])):rt("",!0)])}}};export{sl as default};