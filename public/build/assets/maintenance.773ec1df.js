import{bc as ae,a1 as S,k as l,Z as w,$ as j,A as k,q as n,o as g,b as L,w as s,p as t,m as e,E as A,D as b,c as D,F as E,a as z,x as u,C as N,H as R,I as U,y as B,z as se,ay as le,bf as P}from"./main.3e4fe580.js";import{a as q,b as F,c as H,V as O}from"./VCard.6ecf732f.js";import{V as Q}from"./VInput.c44d1482.js";import{V as Y}from"./VTextField.8a7f8c82.js";import{V as Z}from"./VSelect.47435795.js";import{V as I}from"./VDivider.24d37960.js";import{V as G}from"./VTable.102acbab.js";import{V as J}from"./VAvatar.6ab53fb2.js";import{V as K,a as ne}from"./VBtn.78fdee4b.js";import{V as W}from"./VMenu.a8ae5610.js";import{V as X,a as T}from"./VList.763f3acf.js";import{V as ee}from"./VPagination.63ff60cb.js";import{a as oe}from"./VOverlay.2382ad16.js";import{a as C,V as ie}from"./VRow.04916300.js";import"./router.304344cf.js";import"./position.ba96c1c3.js";import"./VImg.0f30bc9b.js";import"./index.b0f48be5.js";/* empty css                   */import"./VField.8b01d37c.js";import"./easing.36b781ab.js";import"./forwardRefs.c003b6b8.js";import"./VCounter.5e3bb5dc.js";import"./dialog-transition.2b895dc7.js";import"./VCheckboxBtn.f819973a.js";import"./VSelectionControl.1891ba56.js";import"./VChip.afddbe62.js";import"./scopeId.d4b76a30.js";import"./lazy.8911aa6c.js";const M=ae("DashboardStore",{actions:{getRecentMaintenances(f){return S.get("/api/dashboard/maintenances/recent",{params:f})},getIncomingMaintenances(f){return S.get("/api/dashboard/maintenances/incoming",{params:f})},getMaintenanceDashboard(){return S.get("/api/dashboard/maintenances")}}});const ce={class:"me-3",style:{width:"80px"}},re={class:"d-flex align-center gap-2",style:{width:"272px"}},de=e("thead",null,[e("tr",null,[e("th",{scope:"col",class:"text-center"},[e("div",{style:{width:"1rem"}}," ID ")]),e("th",{scope:"col",class:"font-weight-semibold"}," MACHINE "),e("th",{scope:"col",class:"font-weight-semibold"}," PIECE "),e("th",{scope:"col",class:"font-weight-semibold"}," REST "),e("th",{scope:"col",class:"font-weight-semibold"},[e("span",{class:"ms-2"},"ACTIONS")])])],-1),ue={style:{width:"1rem"}},me={class:"d-flex align-center gap-3"},pe={class:"text-sm-subtitle-2 text-medium-emphasis font-weight-semibold"},he={class:"text-disabled"},_e={class:"text-medium-emphasis"},fe={class:"text-center"},ve={class:"text-center",style:{width:"7.5rem"}},ge=e("tr",null,[e("td",{colspan:"8",class:"text-center text-body-1"}," No data available ")],-1),xe=[ge],ye={class:"text-sm text-disabled"},Ve={__name:"AnalyticsIncomingMaintenanceTable",props:{maintenancesList:{type:Array,required:!0}},setup(f){const x=f,p=l(""),m=l(1),y=l(0),v=l([]),V=l([]),d=l(!1);M();const c=l(5),r=l(1);l({isActive:!1});const _=l([]);w(()=>x.maintenancesList,o=>{o&&(_.value=o)}),j(()=>{r.value>m.value&&(r.value=m.value)});const $=k(()=>{console.log(_);const o=_.value.length?(r.value-1)*c.value+1:0,a=_.value.length+(r.value-1)*c.value;return`Showing ${o} to ${a} of ${y.value} entries`});w(V,()=>{V.value.length||(d.value=!1)},{deep:!0});const h=o=>{const a=new Date,te=new Date(o).getTime()-a.getTime();return Math.ceil(te/(1e3*3600*24))};return(o,a)=>n(v)?(g(),L(O,{key:0},{default:s(()=>[t(q,{class:"project-header d-flex flex-wrap justify-space-between py-4 gap-4"},{append:s(()=>[e("div",re,[t(Q,null,{default:s(()=>[A("Search:")]),_:1}),t(Y,{modelValue:n(p),"onUpdate:modelValue":a[1]||(a[1]=i=>b(p)?p.value=i:null),placeholder:"Search"},null,8,["modelValue"])])]),default:s(()=>[t(F,null,{default:s(()=>[A("Incoming Maintenances")]),_:1}),e("div",ce,[t(Z,{modelValue:n(c),"onUpdate:modelValue":a[0]||(a[0]=i=>b(c)?c.value=i:null),density:"compact",variant:"outlined",items:[5,10,20,30,50]},null,8,["modelValue"])])]),_:1}),t(I),t(G,{class:"text-no-wrap"},{default:s(()=>[de,e("tbody",null,[(g(!0),D(E,null,z(n(_),i=>(g(),D("tr",{key:i.id,style:{height:"3.5rem"}},[e("td",null,[e("div",ue,u(i.id),1)]),e("td",null,[e("div",me,[t(J,{variant:"tonal",color:"primary",size:"38"}),e("div",null,[e("h6",pe,u(i.component.name),1),e("span",he,u(i.component.asset.name),1)])])]),e("td",_e,u(i.next_maintenance_date),1),e("td",fe,u(h(i.next_maintenance_date))+" DAYS ",1),e("td",ve,[t(K,{icon:"",variant:"plain",color:"default",size:"x-small"},{default:s(()=>[t(N,{size:22,icon:"tabler-user-check"}),t(W,{activator:"parent"},{default:s(()=>[t(X,{density:"compact"},{default:s(()=>[t(T,{href:"#",title:"Details"}),t(T,{href:"#",title:"Archive"})]),_:1})]),_:1})]),_:1})])]))),128))]),R(e("tfoot",null,xe,512),[[U,!n(_).length]])]),_:1}),t(I),t(H,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3"},{default:s(()=>[e("span",ye,u(n($)),1),t(ee,{modelValue:n(r),"onUpdate:modelValue":a[2]||(a[2]=i=>b(r)?r.value=i:null),size:"small","total-visible":2,length:n(m)},null,8,["modelValue","length"])]),_:1})]),_:1})):B("",!0)}};const be={class:"me-3",style:{width:"80px"}},we={class:"d-flex align-center gap-2",style:{width:"272px"}},Ae=e("thead",null,[e("tr",null,[e("th",{scope:"col",class:"text-center"},[e("div",{style:{width:"1rem"}}," ID ")]),e("th",{scope:"col",class:"font-weight-semibold"}," MACHINE "),e("th",{scope:"col",class:"font-weight-semibold"}," PIECE "),e("th",{scope:"col",class:"font-weight-semibold"}," REST "),e("th",{scope:"col",class:"font-weight-semibold"},[e("span",{class:"ms-2"},"ACTIONS")])])],-1),De={style:{width:"1rem"}},Ie={class:"d-flex align-center gap-3"},Te={class:"text-sm-subtitle-2 text-medium-emphasis font-weight-semibold"},$e={class:"text-disabled"},Se={class:"text-medium-emphasis"},Le={class:"text-center"},Me={class:"text-center",style:{width:"7.5rem"}},Pe=e("tr",null,[e("td",{colspan:"8",class:"text-center text-body-1"}," No data available ")],-1),Ce=[Pe],je={class:"text-sm text-disabled"},ke={__name:"AnalyticsRecentMaintenanceTable",props:{maintenancesList:{type:Array,required:!0}},setup(f){const x=l(""),p=l(1),m=l(0),y=l([]),v=l([]),V=l(!1);M(),l({isActive:!1});const d=l(5),c=l(1),r=l([]);w(()=>r,h=>{h&&(r.value=h)}),j(()=>{c.value>p.value&&(c.value=p.value)});const _=k(()=>{const h=r.value.length?(c.value-1)*d.value+1:0,o=r.value.length+(c.value-1)*d.value;return`Showing ${h} to ${o} of ${m.value} entries`});w(v,()=>{v.value.length||(V.value=!1)},{deep:!0});const $=h=>{const o=new Date,i=new Date(h).getTime()-o.getTime();return Math.ceil(i/(1e3*3600*24))};return(h,o)=>n(y)?(g(),L(O,{key:0},{default:s(()=>[t(q,{class:"project-header d-flex flex-wrap justify-space-between py-4 gap-4"},{append:s(()=>[e("div",we,[t(Q,null,{default:s(()=>[A("Search:")]),_:1}),t(Y,{modelValue:n(x),"onUpdate:modelValue":o[1]||(o[1]=a=>b(x)?x.value=a:null),placeholder:"Search"},null,8,["modelValue"])])]),default:s(()=>[t(F,null,{default:s(()=>[A("Recent Maintenances")]),_:1}),e("div",be,[t(Z,{modelValue:n(d),"onUpdate:modelValue":o[0]||(o[0]=a=>b(d)?d.value=a:null),density:"compact",variant:"outlined",items:[5,10,20,30,50]},null,8,["modelValue"])])]),_:1}),t(I),t(G,{class:"text-no-wrap"},{default:s(()=>[Ae,e("tbody",null,[(g(!0),D(E,null,z(n(r),a=>(g(),D("tr",{key:a.id,style:{height:"3.5rem"}},[e("td",null,[e("div",De,u(a.id),1)]),e("td",null,[e("div",Ie,[t(J,{variant:"tonal",color:"primary",size:"38"}),e("div",null,[e("h6",Te,u(a.component.name),1),e("span",$e,u(a.component.asset.name),1)])])]),e("td",Se,u(a.next_maintenance_date),1),e("td",Le,u($(a.next_maintenance_date))+" DAYS ",1),e("td",Me,[t(K,{icon:"",variant:"plain",color:"default",size:"x-small"},{default:s(()=>[t(N,{size:22,icon:"tabler-user-check"}),t(W,{activator:"parent"},{default:s(()=>[t(X,{density:"compact"},{default:s(()=>[t(T,{href:"#",title:"Details"}),t(T,{href:"#",title:"Archive"})]),_:1})]),_:1})]),_:1})])]))),128))]),R(e("tfoot",null,Ce,512),[[U,!n(r).length]])]),_:1}),t(I),t(H,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3"},{default:s(()=>[e("span",je,u(n(_)),1),t(ee,{modelValue:n(c),"onUpdate:modelValue":o[2]||(o[2]=a=>b(c)?c.value=a:null),size:"small","total-visible":2,length:n(p)},null,8,["modelValue","length"])]),_:1})]),_:1})):B("",!0)}};const Ee={__name:"maintenance",setup(f){se().current.value.colors;const p=M(),m=l({isActive:!1}),y=l([]),v=l([]),V=()=>{m.value.isActive=!0,p.getMaintenanceDashboard().then(d=>{v.value=d.data.maintenances,y.value=d.data.incomingMaintenances,m.value.isActive=!1}).catch(d=>{m.value.isActive=!1})};return le(()=>{V()}),(d,c)=>(g(),L(ie,{class:"match-height"},{default:s(()=>[t(oe,{"model-value":n(m).isActive,class:"align-center justify-center"},{default:s(()=>[t(ne,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),t(C,{cols:"12",md:"6"},{default:s(()=>[t(Ve,{maintenancesList:n(y)},null,8,["maintenancesList"])]),_:1}),t(C,{cols:"12",md:"6"},{default:s(()=>[t(ke,{maintenancesList:n(v)},null,8,["maintenancesList"])]),_:1})]),_:1}))}};typeof P=="function"&&P(Ee);export{Ee as default};