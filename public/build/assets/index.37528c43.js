import{k as i,o as p,b as w,w as l,m as s,p as e,C as b,q as o,P as W,D as v,be as X,E as C,ay as q,$ as O,Z as ee,A as te,r as ae,c as N,F as le,a as oe,x as z,y as F,H as se,I as re}from"./main.cf9f31a9.js";import{r as M}from"./validators.330a354f.js";import{u as ie}from"./useSaleStore.04f1c1ac.js";import{V as j}from"./VSpacer.6ec948ae.js";import{V as f,a as B}from"./VBtn.d73656ae.js";import{V as Y,c as $}from"./VCard.a1247ba2.js";import{V as ne}from"./VForm.f1d9c3ac.js";import{V as H,a as P}from"./VRow.a394feb3.js";import{V as E}from"./VAutocomplete.dab6d47c.js";import{V as de}from"./VNavigationDrawer.2d863e08.js";import"./AppDateTimePicker.b70b0339.js";import{_ as ue}from"./ConfirmationDialog.5db50079.js";import{s as R}from"./successMiddleware.8a77ff6b.js";import{e as L}from"./errorsMiddleware.ca71b8e1.js";import{u as me}from"./useAttendanceStore.7e1b3906.js";import{a as ce}from"./VOverlay.9d9650d6.js";import{V as I}from"./VDivider.01acf602.js";import{V as pe}from"./VSelect.32c4b2dd.js";import{V as fe}from"./VTextField.9102871e.js";import{V as ve}from"./VTable.60781dfc.js";import{V as Ve}from"./VAvatar.9270a597.js";import{V as _e}from"./VImg.134af49b.js";import{V as ye}from"./VPagination.7fcfaf73.js";import"./index.b522f886.js";import"./router.1658937d.js";import"./position.b360a9d1.js";import"./VInput.d6b4ca43.js";import"./index.d1f0132a.js";import"./forwardRefs.c003b6b8.js";import"./filter.73bf6f77.js";import"./VList.a34172e8.js";import"./VMenu.3df9bfa4.js";import"./scopeId.08611610.js";import"./dialog-transition.b0009893.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.b3570faf.js";import"./VSelectionControl.5a5e7888.js";import"./VChip.6c2e2c44.js";import"./ssrBoot.e94d367d.js";import"./VField.6a2ec846.js";import"./DialogCloseBtn.1c4165b0.js";import"./VDialog.ac89ffbe.js";import"./lazy.f959659a.js";/* empty css                   */import"./VCounter.4f92d6c5.js";const he={class:"d-flex align-center pa-6 pb-1"},xe=s("h6",{class:"text-h6"}," Add Attendance ",-1),ge={__name:"AddNewAttendanceDrawer",props:{isDrawerOpen:{type:null,required:!0},attendances:{type:null,required:!0},months:{type:null,required:!0},years:{type:null,required:!0}},emits:["update:isDrawerOpen","attendanceData"],setup(T,{emit:y}){const V=T,A=i(!1),n=i();ie();const g=i({month:"",year:""});i(),i({id:-1});const _=()=>{y("update:isDrawerOpen",!1),q(()=>{var c,r;(c=n.value)==null||c.reset(),(r=n.value)==null||r.resetValidation()})},m=()=>{var c;(c=n.value)==null||c.validate().then(({valid:r})=>{r&&(y("attendanceData",{attendance:g}),y("update:isDrawerOpen",!1),q(()=>{var d,x;(d=n.value)==null||d.reset(),(x=n.value)==null||x.resetValidation()}))})},h=c=>{y("update:isDrawerOpen",c)};return(c,r)=>(p(),w(de,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":V.isDrawerOpen,"onUpdate:modelValue":h},{default:l(()=>[s("div",he,[xe,e(j),e(f,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:_},{default:l(()=>[e(b,{size:"18",icon:"tabler-x"})]),_:1})]),e(o(W),{options:{wheelPropagation:!1}},{default:l(()=>[e(Y,{flat:""},{default:l(()=>[e($,null,{default:l(()=>[e(ne,{ref_key:"refForm",ref:n,modelValue:o(A),"onUpdate:modelValue":r[2]||(r[2]=d=>v(A)?A.value=d:null),onSubmit:X(m,["prevent"])},{default:l(()=>[e(H,null,{default:l(()=>[e(P,{cols:"12"},{default:l(()=>[e(E,{modelValue:o(g).month,"onUpdate:modelValue":r[0]||(r[0]=d=>o(g).month=d),items:V.months,rules:[o(M)],"item-value":"id","item-title":"name",label:"Month"},null,8,["modelValue","items","rules"])]),_:1}),e(P,{cols:"12"},{default:l(()=>[e(E,{modelValue:o(g).year,"onUpdate:modelValue":r[1]||(r[1]=d=>o(g).year=d),items:V.years,rules:[o(M)],label:"Year"},null,8,["modelValue","items","rules"])]),_:1}),e(P,{cols:"12"},{default:l(()=>[e(f,{type:"submit",class:"me-3"},{default:l(()=>[C(" Submit ")]),_:1}),e(f,{type:"reset",variant:"tonal",color:"secondary",onClick:_},{default:l(()=>[C(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"]))}};const we={class:"me-3",style:{width:"100px"}},be={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},Ae={style:{width:"6rem"}},De=s("thead",null,[s("tr",null,[s("th",{scope:"col"}," ID "),s("th",{scope:"col"}," Month "),s("th",{scope:"col"}," Year "),s("th",{scope:"col"}," ACTIONS ")])],-1),ke={class:"d-flex align-center"},ze={key:1},Ce={class:"d-flex flex-column"},Se={class:"text-base"},Ue={class:"text-capitalize text-base font-weight-semibold"},Ne={class:"text-center",style:{width:"5rem"}},Pe=s("tr",null,[s("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Oe=[Pe],Fe={class:"text-sm text-disabled"},zt={__name:"index",setup(T){const y=me(),V=i(""),A=i(!1),n=i({isActive:!0}),g=i(!0);i(),i(),i();const _=i(10),m=i(1),h=i(1),c=i(0);let r=i([]),d=[],x=[];const S=()=>{n.value.isActive=!0,y.fetchAttendances({searchValue:V.value,perPage:_.value,currentPage:m.value}).then(u=>{r.value=u.data.attendances.data,d=u.data.months,x=u.data.years,h.value=u.data.totalPage,c.value=u.data.totalAttendances,A.value=!1,n.value.isActive=!1}).catch(u=>{console.error(u),n.value.isActive=!1})};O(S),O(()=>{m.value>h.value&&(m.value=h.value)}),ee(V,()=>{S()});const U=i(!1);i(!1);const D=i(!1);let k=i();O(()=>{m.value>h.value&&(m.value=h.value)});const Q=te(()=>{const u=r.value.length?(m.value-1)*_.value+1:0,a=r.value.length+(m.value-1)*_.value;return`Showing ${u} to ${a} of ${c.value} entries`}),Z=u=>{n.value.isActive=!0,y.addAttendance(u).then(a=>{R(a.data.message),n.value.isActive=!1,S()}).catch(a=>{n.value.isActive=!1,L(a.response.data.message)})},G=u=>{n.value.isActive=!0,y.deleteAttendance(u).then(a=>{n.value.isActive=!1,R(a.data.message)}).catch(a=>{L(a.data.message),n.value.isActive=!1}),S()},J=u=>{D.value=!0,k=u};return(u,a)=>{const K=ae("RouterLink");return p(),N("section",null,[e(H,null,{default:l(()=>[e(ce,{"model-value":o(n).isActive,class:"align-center justify-center"},{default:l(()=>[e(B,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),e(P,{cols:"12"},{default:l(()=>[e(Y,{title:"Attendance"},{default:l(()=>[e(I),e($,{class:"d-flex flex-wrap py-4 gap-4"},{default:l(()=>[s("div",we,[e(pe,{modelValue:o(_),"onUpdate:modelValue":a[0]||(a[0]=t=>v(_)?_.value=t:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),e(j),s("div",be,[s("div",Ae,[o(A)?(p(),w(B,{key:0,indeterminate:"",color:"primary"})):(p(),w(fe,{key:1,onInput:a[1]||(a[1]=t=>g.value=!0),ref:"searchField",modelValue:o(V),"onUpdate:modelValue":a[2]||(a[2]=t=>v(V)?V.value=t:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),e(f,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:l(()=>[C(" Export ")]),_:1}),e(f,{"prepend-icon":"tabler-plus",onClick:a[3]||(a[3]=t=>U.value=!0)},{default:l(()=>[C(" New Attendance ")]),_:1})])]),_:1}),e(I),e(ve,{class:"text-no-wrap"},{default:l(()=>[De,s("tbody",null,[(p(!0),N(le,null,oe(o(r),t=>(p(),N("tr",{key:t.id,style:{height:"3.75rem"}},[s("td",null,z(t.id),1),s("td",null,[s("div",ke,[e(Ve,{variant:"tonal",class:"me-3",size:"38"},{default:l(()=>[t.avatar?(p(),w(_e,{key:0,src:t.avatar},null,8,["src"])):(p(),N("span",ze,z(o(d)[t.month-1].name.toUpperCase().charAt(0)),1))]),_:2},1024),s("div",Ce,[s("h6",Se,[e(K,{to:{name:"apps-user-view-id",params:{id:t.id}},class:"font-weight-medium user-list-name"},{default:l(()=>[C(z(o(d)[t.month-1].name),1)]),_:2},1032,["to"])])])])]),s("td",null,[s("span",Ue,z(t.year),1)]),s("td",Ne,[t.active?F("",!0):(p(),w(f,{key:0,icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-add-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-edit"})]),_:2},1032,["to"])),t.active?(p(),w(f,{key:1,icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-edit-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-edit"})]),_:2},1032,["to"])):F("",!0),e(f,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-preview-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-viewfinder"})]),_:2},1032,["to"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text",onClick:Ie=>J(t)},{default:l(()=>[e(b,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-employees-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-eye"})]),_:2},1032,["to"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text"},{default:l(()=>[e(b,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),se(s("tfoot",null,Oe,512),[[re,!o(r).length]])]),_:1}),e(I),e($,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:l(()=>[s("span",Fe,z(o(Q)),1),e(ye,{modelValue:o(m),"onUpdate:modelValue":a[4]||(a[4]=t=>v(m)?m.value=t:null),size:"small","total-visible":5,length:o(h)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),e(ge,{isDrawerOpen:o(U),"onUpdate:isDrawerOpen":a[5]||(a[5]=t=>v(U)?U.value=t:null),attendances:o(r),"onUpdate:attendances":a[6]||(a[6]=t=>v(r)?r.value=t:r=t),months:o(d),"onUpdate:months":a[7]||(a[7]=t=>v(d)?d.value=t:d=t),years:o(x),"onUpdate:years":a[8]||(a[8]=t=>v(x)?x.value=t:x=t),onAttendanceData:Z},null,8,["isDrawerOpen","attendances","months","years"]),o(D)?(p(),w(ue,{key:0,isDialogVisible:o(D),"onUpdate:isDialogVisible":a[9]||(a[9]=t=>v(D)?D.value=t:null),attendance:o(k),"onUpdate:attendance":a[10]||(a[10]=t=>v(k)?k.value=t:k=t),onEmployeeData:G},null,8,["isDialogVisible","attendance"])):F("",!0)])}}};export{zt as default};