import{k as i,o as p,b as w,w as l,m as s,p as e,C as b,q as o,P as X,D as v,be as ee,E as C,ay as q,$ as O,Z as te,A as ae,r as le,c as N,F as oe,a as se,x as z,y as F,H as re,I as ie,bf as M}from"./main.80073fb3.js";import{r as B}from"./validators.330a354f.js";import{u as ne}from"./useSaleStore.556239d8.js";import{V as Y}from"./VSpacer.f6e4a849.js";import{V as f,a as E}from"./VBtn.bc513066.js";import{V as H,c as $}from"./VCard.bed48467.js";import{V as de}from"./VForm.afb9be88.js";import{V as Q,a as P}from"./VRow.e327326a.js";import{V as R}from"./VAutocomplete.a415af86.js";import{V as ue}from"./VNavigationDrawer.7eedad6d.js";import"./AppDateTimePicker.291f7947.js";import{_ as me}from"./ConfirmationDialog.ed373573.js";import{s as L}from"./successMiddleware.e1481932.js";import{e as j}from"./errorsMiddleware.7ae287f7.js";import{u as ce}from"./useAttendanceStore.428182e0.js";import{a as pe}from"./VOverlay.ba4319b9.js";import{V as I}from"./VDivider.c8bf408f.js";import{V as fe}from"./VSelect.eb06e1d1.js";import{V as ve}from"./VTextField.f7f47561.js";import{V as Ve}from"./VTable.6035f281.js";import{V as _e}from"./VAvatar.755a82ce.js";import{V as ye}from"./VImg.19a712b4.js";import{V as he}from"./VPagination.0ced2f02.js";import"./index.b522f886.js";import"./router.9f57d7dd.js";import"./position.a8816655.js";import"./VInput.b30e6f3e.js";import"./index.4672e601.js";import"./forwardRefs.c003b6b8.js";import"./filter.7efe6ffd.js";import"./VList.dace6270.js";import"./VMenu.5e377b50.js";import"./scopeId.7979a6ee.js";import"./dialog-transition.4fc1b9b4.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.9ec2021d.js";import"./VSelectionControl.816c4425.js";import"./VChip.1cdee411.js";import"./ssrBoot.2abef11c.js";import"./VField.95c31d27.js";import"./DialogCloseBtn.0b4ca758.js";import"./VDialog.809ecb4b.js";import"./lazy.172a4f89.js";/* empty css                   */import"./VCounter.770fe3f3.js";const xe={class:"d-flex align-center pa-6 pb-1"},ge=s("h6",{class:"text-h6"}," Add Attendance ",-1),we={__name:"AddNewAttendanceDrawer",props:{isDrawerOpen:{type:null,required:!0},attendances:{type:null,required:!0},months:{type:null,required:!0},years:{type:null,required:!0}},emits:["update:isDrawerOpen","attendanceData"],setup(T,{emit:y}){const V=T,A=i(!1),n=i();ne();const g=i({month:"",year:""});i(),i({id:-1});const _=()=>{y("update:isDrawerOpen",!1),q(()=>{var c,r;(c=n.value)==null||c.reset(),(r=n.value)==null||r.resetValidation()})},m=()=>{var c;(c=n.value)==null||c.validate().then(({valid:r})=>{r&&(y("attendanceData",{attendance:g}),y("update:isDrawerOpen",!1),q(()=>{var d,x;(d=n.value)==null||d.reset(),(x=n.value)==null||x.resetValidation()}))})},h=c=>{y("update:isDrawerOpen",c)};return(c,r)=>(p(),w(ue,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":V.isDrawerOpen,"onUpdate:modelValue":h},{default:l(()=>[s("div",xe,[ge,e(Y),e(f,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:_},{default:l(()=>[e(b,{size:"18",icon:"tabler-x"})]),_:1})]),e(o(X),{options:{wheelPropagation:!1}},{default:l(()=>[e(H,{flat:""},{default:l(()=>[e($,null,{default:l(()=>[e(de,{ref_key:"refForm",ref:n,modelValue:o(A),"onUpdate:modelValue":r[2]||(r[2]=d=>v(A)?A.value=d:null),onSubmit:ee(m,["prevent"])},{default:l(()=>[e(Q,null,{default:l(()=>[e(P,{cols:"12"},{default:l(()=>[e(R,{modelValue:o(g).month,"onUpdate:modelValue":r[0]||(r[0]=d=>o(g).month=d),items:V.months,rules:[o(B)],"item-value":"id","item-title":"name",label:"Month"},null,8,["modelValue","items","rules"])]),_:1}),e(P,{cols:"12"},{default:l(()=>[e(R,{modelValue:o(g).year,"onUpdate:modelValue":r[1]||(r[1]=d=>o(g).year=d),items:V.years,rules:[o(B)],label:"Year"},null,8,["modelValue","items","rules"])]),_:1}),e(P,{cols:"12"},{default:l(()=>[e(f,{type:"submit",class:"me-3"},{default:l(()=>[C(" Submit ")]),_:1}),e(f,{type:"reset",variant:"tonal",color:"secondary",onClick:_},{default:l(()=>[C(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"]))}};const be={class:"me-3",style:{width:"100px"}},Ae={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},De={style:{width:"6rem"}},ke=s("thead",null,[s("tr",null,[s("th",{scope:"col"}," ID "),s("th",{scope:"col"}," Month "),s("th",{scope:"col"}," Year "),s("th",{scope:"col"}," ACTIONS ")])],-1),ze={class:"d-flex align-center"},Ce={key:1},Se={class:"d-flex flex-column"},Ue={class:"text-base"},Ne={class:"text-capitalize text-base font-weight-semibold"},Pe={class:"text-center",style:{width:"5rem"}},Oe=s("tr",null,[s("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Fe=[Oe],Ie={class:"text-sm text-disabled"},$e={__name:"index",setup(T){const y=ce(),V=i(""),A=i(!1),n=i({isActive:!0}),g=i(!0);i(),i(),i();const _=i(10),m=i(1),h=i(1),c=i(0);let r=i([]),d=[],x=[];const S=()=>{n.value.isActive=!0,y.fetchAttendances({searchValue:V.value,perPage:_.value,currentPage:m.value}).then(u=>{r.value=u.data.attendances.data,d=u.data.months,x=u.data.years,h.value=u.data.totalPage,c.value=u.data.totalAttendances,A.value=!1,n.value.isActive=!1}).catch(u=>{console.error(u),n.value.isActive=!1})};O(S),O(()=>{m.value>h.value&&(m.value=h.value)}),te(V,()=>{S()});const U=i(!1);i(!1);const D=i(!1);let k=i();O(()=>{m.value>h.value&&(m.value=h.value)});const Z=ae(()=>{const u=r.value.length?(m.value-1)*_.value+1:0,a=r.value.length+(m.value-1)*_.value;return`Showing ${u} to ${a} of ${c.value} entries`}),G=u=>{n.value.isActive=!0,y.addAttendance(u).then(a=>{L(a.data.message),n.value.isActive=!1,S()}).catch(a=>{n.value.isActive=!1,j(a.response.data.message)})},J=u=>{n.value.isActive=!0,y.deleteAttendance(u).then(a=>{n.value.isActive=!1,L(a.data.message)}).catch(a=>{j(a.data.message),n.value.isActive=!1}),S()},K=u=>{D.value=!0,k=u};return(u,a)=>{const W=le("RouterLink");return p(),N("section",null,[e(Q,null,{default:l(()=>[e(pe,{"model-value":o(n).isActive,class:"align-center justify-center"},{default:l(()=>[e(E,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),e(P,{cols:"12"},{default:l(()=>[e(H,{title:"Attendance"},{default:l(()=>[e(I),e($,{class:"d-flex flex-wrap py-4 gap-4"},{default:l(()=>[s("div",be,[e(fe,{modelValue:o(_),"onUpdate:modelValue":a[0]||(a[0]=t=>v(_)?_.value=t:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),e(Y),s("div",Ae,[s("div",De,[o(A)?(p(),w(E,{key:0,indeterminate:"",color:"primary"})):(p(),w(ve,{key:1,onInput:a[1]||(a[1]=t=>g.value=!0),ref:"searchField",modelValue:o(V),"onUpdate:modelValue":a[2]||(a[2]=t=>v(V)?V.value=t:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),e(f,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:l(()=>[C(" Export ")]),_:1}),e(f,{"prepend-icon":"tabler-plus",onClick:a[3]||(a[3]=t=>U.value=!0)},{default:l(()=>[C(" New Attendance ")]),_:1})])]),_:1}),e(I),e(Ve,{class:"text-no-wrap"},{default:l(()=>[ke,s("tbody",null,[(p(!0),N(oe,null,se(o(r),t=>(p(),N("tr",{key:t.id,style:{height:"3.75rem"}},[s("td",null,z(t.id),1),s("td",null,[s("div",ze,[e(_e,{variant:"tonal",class:"me-3",size:"38"},{default:l(()=>[t.avatar?(p(),w(ye,{key:0,src:t.avatar},null,8,["src"])):(p(),N("span",Ce,z(o(d)[t.month-1].name.toUpperCase().charAt(0)),1))]),_:2},1024),s("div",Se,[s("h6",Ue,[e(W,{to:{name:"apps-user-view-id",params:{id:t.id}},class:"font-weight-medium user-list-name"},{default:l(()=>[C(z(o(d)[t.month-1].name),1)]),_:2},1032,["to"])])])])]),s("td",null,[s("span",Ne,z(t.year),1)]),s("td",Pe,[t.active?F("",!0):(p(),w(f,{key:0,icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-add-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-edit"})]),_:2},1032,["to"])),t.active?(p(),w(f,{key:1,icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-edit-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-edit"})]),_:2},1032,["to"])):F("",!0),e(f,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-preview-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-viewfinder"})]),_:2},1032,["to"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text",onClick:Te=>K(t)},{default:l(()=>[e(b,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-attendance-employees-id",params:{id:t.id}}},{default:l(()=>[e(b,{size:"22",icon:"tabler-eye"})]),_:2},1032,["to"]),e(f,{icon:"",size:"x-small",color:"default",variant:"text"},{default:l(()=>[e(b,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),re(s("tfoot",null,Fe,512),[[ie,!o(r).length]])]),_:1}),e(I),e($,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:l(()=>[s("span",Ie,z(o(Z)),1),e(he,{modelValue:o(m),"onUpdate:modelValue":a[4]||(a[4]=t=>v(m)?m.value=t:null),size:"small","total-visible":5,length:o(h)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),e(we,{isDrawerOpen:o(U),"onUpdate:isDrawerOpen":a[5]||(a[5]=t=>v(U)?U.value=t:null),attendances:o(r),"onUpdate:attendances":a[6]||(a[6]=t=>v(r)?r.value=t:r=t),months:o(d),"onUpdate:months":a[7]||(a[7]=t=>v(d)?d.value=t:d=t),years:o(x),"onUpdate:years":a[8]||(a[8]=t=>v(x)?x.value=t:x=t),onAttendanceData:G},null,8,["isDrawerOpen","attendances","months","years"]),o(D)?(p(),w(me,{key:0,isDialogVisible:o(D),"onUpdate:isDialogVisible":a[9]||(a[9]=t=>v(D)?D.value=t:null),attendance:o(k),"onUpdate:attendance":a[10]||(a[10]=t=>v(k)?k.value=t:k=t),onEmployeeData:J},null,8,["isDialogVisible","attendance"])):F("",!0)])}}};typeof M=="function"&&M($e);export{$e as default};