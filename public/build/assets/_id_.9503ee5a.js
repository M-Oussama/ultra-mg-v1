import{k as s,bd as re,$ as B,Z as ne,A as ie,o as u,c as V,p as l,w as a,q as r,m as n,b as A,y as M,D as m,E as k,F as U,a as C,C as ue,x as h,bf as Q}from"./main.ccc6aae0.js";import{_ as de,a as me}from"./EditEmployeeDrawer.15033b08.js";import{_ as pe}from"./ConfirmationDialog.d7e75799.js";import{s as W}from"./successMiddleware.53303d8d.js";import{e as I}from"./errorsMiddleware.ca8f0215.js";import{u as ce}from"./useAttendanceStore.ca57727c.js";import{a as fe}from"./VOverlay.0ad0cca7.js";import{a as q,V as $}from"./VBtn.7efa8e0f.js";import{a as _,V as L}from"./VRow.a17ab33e.js";import{V as N,c as x}from"./VCard.36c7adfd.js";import{V as Y}from"./VDivider.09896c6c.js";import{V as ve}from"./VAutocomplete.5bdf49b5.js";import{V as Ve,a as _e}from"./VTabs.d4bdf2de.js";import{V as ye,a as be}from"./VWindowItem.38e58b77.js";import{V as ge}from"./VTable.484ee967.js";import{V as Z}from"./VCheckbox.f87b0a3c.js";import{V as Ae}from"./VPagination.ced5ec2a.js";import"./AppDateTimePicker.be59b953.js";import"./VField.8b77e1e2.js";import"./index.720d3b34.js";import"./VInput.1586f7cc.js";import"./router.25b57325.js";import"./VImg.e193e870.js";import"./position.ea536dab.js";import"./easing.36b781ab.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VSpacer.fc3a499d.js";import"./VAvatar.cb7dc9a5.js";import"./VForm.8ba2a0de.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.8db2e7e8.js";/* empty css                   */import"./VCounter.d2fed1b1.js";import"./VNavigationDrawer.df836fef.js";import"./ssrBoot.81f110ad.js";import"./DialogCloseBtn.eb9ea561.js";import"./VDialog.11663ea2.js";import"./scopeId.1409e92e.js";import"./dialog-transition.e2b69798.js";import"./lazy.ab6dcefa.js";import"./VSelect.b041d3dc.js";import"./VList.54d6dfac.js";import"./VMenu.01138ff3.js";import"./VCheckboxBtn.cfb39e43.js";import"./VSelectionControl.e824dbee.js";import"./VChip.768b9a43.js";import"./filter.7282d4f9.js";import"./VSlideGroup.b3deddf0.js";const he={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},xe={class:"pa-6"},De={class:"ma-7"},we={class:"pt-5"},Ee=n("th",{class:"text-left"}," Quit Employee ",-1),ke={class:"text-left"},Ue=n("tr",null,[n("th",{class:"text-left"}," date "),n("th",{class:"text-left"}," Presence ")],-1),Ce=n("th",{class:"text-left"}," ALL DAYS ",-1),Ne={class:"text-left"},Te={class:"text-left"},Se={class:"text-left"},Oe={class:"text-sm text-disabled"},Pe={__name:"[id]",setup(Be){const D=ce(),G=s(""),T=re(),H=s(!1),i=s({isActive:!0}),f=s(0);s([]),s(!1),s(),s(),s();const z=s(10),d=s(1),y=s(1),J=s(0);s({employees:[],dates:[]});let c=s({}),R=s({}),S=s([]),b=s({});s({});const g=()=>{i.value.isActive=!0,D.getAttendanceByID(Number(T.params.id)).then(e=>{c.value=e.data.employeeAttendance,S.value=e.data.employees,console.log("employeeAttendance"),console.log(c),i.value.isActive=!1}).catch(e=>{console.error(e),i.value.isActive=!1})};B(g),B(()=>{d.value>y.value&&(d.value=y.value)}),ne(G,()=>{g()});const O=s(!1),P=s(!1),w=s(!1);let p=s();B(()=>{d.value>y.value&&(d.value=y.value)});const K=ie(()=>{const e=R.value.length?(d.value-1)*z.value+1:0,t=R.value.length+(d.value-1)*z.value;return`Showing ${e} to ${t} of ${J.value} entries`}),X=()=>{i.value.isActive=!0,D.updateAttendance(c,Number(T.params.id)).then(e=>{W(e.data.message),i.value.isActive=!1,g()}).catch(e=>{I(e.data.message),i.value.isActive=!1})},ee=e=>{i.value.isActive=!0,D.deleteAttendance(e).then(t=>{i.value.isActive=!1,W(t.data.message)}).catch(t=>{I(t.data.message),i.value.isActive=!1}),g()},j=s(!0),F=e=>(e.toString(),""),le=e=>e.name===void 0?"Select An Employee":e.name+" "+e.surname,te=()=>{i.value.isActive=!0,D.AddEmployeeToAttendance(b.value,Number(T.params.id)).then(e=>{i.value.isActive=!1,S.value=e.data.employees,c[e.data.key]=e.data.newAttendance,console.log(c),g()}).catch(e=>{I(e.data.message),i.value.isActive=!1})},ae=e=>{console.log(e),e.allDays=!e.allDays;for(let t=0;t<e.length;t++)e[t].present=e.allDays};return(e,t)=>(u(),V("section",null,[l(L,null,{default:a(()=>[l(fe,{"model-value":r(i).isActive,class:"align-center justify-center"},{default:a(()=>[l(q,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),l(_,{cols:"9"},{default:a(()=>[l(N,{title:"New Attendance"},{default:a(()=>[l(Y),l(x,{class:"d-flex flex-wrap py-4 gap-4"},{default:a(()=>[n("div",he,[n("div",null,[r(H)?(u(),A(q,{key:0,indeterminate:"",color:"primary"})):M("",!0)])])]),_:1}),n("div",xe,[l(L,null,{default:a(()=>[l(_,{cols:"8"},{default:a(()=>[l(ve,{modelValue:r(b),"onUpdate:modelValue":t[0]||(t[0]=o=>m(b)?b.value=o:b=o),items:r(S),label:"Employee","item-value":"id","item-title":le},null,8,["modelValue","items"])]),_:1}),l(_,{cols:"3"},{default:a(()=>[l($,{block:"",color:"default",variant:"tonal",onClick:te},{default:a(()=>[k(" Add ")]),_:1})]),_:1})]),_:1})]),n("div",De,[l(L,null,{default:a(()=>[l(_,{cols:"5",sm:"4"},{default:a(()=>[l(Ve,{modelValue:r(f),"onUpdate:modelValue":t[1]||(t[1]=o=>m(f)?f.value=o:null),direction:"vertical",class:"v-tabs-pill"},{default:a(()=>[(u(!0),V(U,null,C(r(c),o=>(u(),A(_e,null,{default:a(()=>[l(ue,{start:"",icon:"tabler-user"}),k(" "+h(o.employee.name)+" "+h(o.employee.surname),1)]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1}),l(_,{cols:"7",sm:"8"},{default:a(()=>[l(N,null,{default:a(()=>[l(x,null,{default:a(()=>[l(ye,{modelValue:r(f),"onUpdate:modelValue":t[2]||(t[2]=o=>m(f)?f.value=o:null)},{default:a(()=>[(u(!0),V(U,null,C(r(c),o=>(u(),A(be,null,{default:a(()=>[(u(!0),V(U,null,C(o.result,(E,oe)=>(u(),A(N,null,{default:a(()=>[l(x,null,{default:a(()=>[l(ge,null,{default:a(()=>[n("thead",null,[n("tr",we,[Ee,n("th",ke,h(oe)+" ",1)]),Ue]),n("tbody",null,[n("tr",null,[Ce,n("th",Ne,[l(Z,{modelValue:E.allDays,"onUpdate:modelValue":v=>E.allDays=v,label:F(r(j)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error",onClick:v=>ae(E)},null,8,["modelValue","onUpdate:modelValue","label","onClick"])])]),(u(!0),V(U,null,C(E,v=>(u(),V("tr",null,[n("th",Te,h(v.date),1),n("th",Se,[l(Z,{modelValue:v.present,"onUpdate:modelValue":se=>v.present=se,label:F(r(j)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error"},null,8,["modelValue","onUpdate:modelValue","label"])])]))),256))])]),_:2},1024)]),_:2},1024)]),_:2},1024))),256))]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1})]),_:1})]),_:1})]),_:1})]),l(Y),l(x,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:a(()=>[n("span",Oe,h(r(K)),1),l(Ae,{modelValue:r(d),"onUpdate:modelValue":t[3]||(t[3]=o=>m(d)?d.value=o:null),size:"small","total-visible":5,length:r(y)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1}),l(_,{cols:"12",md:"3"},{default:a(()=>[l(N,{class:"mb-8"},{default:a(()=>[l(x,null,{default:a(()=>[l($,{block:"","prepend-icon":"tabler-send",class:"mb-2"},{default:a(()=>[k(" Send Invoice ")]),_:1}),l($,{block:"",color:"default",variant:"tonal",onClick:X},{default:a(()=>[k(" Update ")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1}),l(de,{isDrawerOpen:r(O),"onUpdate:isDrawerOpen":t[4]||(t[4]=o=>m(O)?O.value=o:null),onEmployeeData:e.addNewEmployee},null,8,["isDrawerOpen","onEmployeeData"]),l(me,{isDrawerOpen:r(P),"onUpdate:isDrawerOpen":t[5]||(t[5]=o=>m(P)?P.value=o:null),employee:r(p),"onUpdate:employee":t[6]||(t[6]=o=>m(p)?p.value=o:p=o),onEmployeeData:e.updateEmployee},null,8,["isDrawerOpen","employee","onEmployeeData"]),r(w)?(u(),A(pe,{key:0,isDialogVisible:r(w),"onUpdate:isDialogVisible":t[7]||(t[7]=o=>m(w)?w.value=o:null),employee:r(p),"onUpdate:employee":t[8]||(t[8]=o=>m(p)?p.value=o:p=o),onEmployeeData:ee},null,8,["isDialogVisible","employee"])):M("",!0)]))}};typeof Q=="function"&&Q(Pe);export{Pe as default};
