import{k as s,bd as ie,$ as I,Z as ue,A as ne,o as m,c as g,p as l,w as o,q as r,m as i,b as h,y as $,D as d,E as k,F as R,a as L,C as me,x as U,bf as Q}from"./main.ccc6aae0.js";import{_ as de}from"./AppDateTimePicker.be59b953.js";import{_ as pe,a as ce}from"./EditEmployeeDrawer.15033b08.js";import{_ as fe}from"./ConfirmationDialog.d7e75799.js";import{s as W}from"./successMiddleware.53303d8d.js";import{e as C}from"./errorsMiddleware.ca8f0215.js";import{u as ve}from"./useAttendanceStore.ca57727c.js";import{a as Ve}from"./VOverlay.0ad0cca7.js";import{a as Y,V as q}from"./VBtn.7efa8e0f.js";import{a as v,V as N}from"./VRow.a17ab33e.js";import{V as z,c as T}from"./VCard.36c7adfd.js";import{V as Z}from"./VDivider.09896c6c.js";import{V as ye}from"./VAutocomplete.5bdf49b5.js";import{V as _e,a as be}from"./VTabs.d4bdf2de.js";import{V as Ae,a as ge}from"./VWindowItem.38e58b77.js";import{V as he}from"./VTable.484ee967.js";import{V as De}from"./VSwitch.45f4ff8e.js";import{V as F}from"./VCheckbox.f87b0a3c.js";import{V as xe}from"./VPagination.ced5ec2a.js";import"./VField.8b77e1e2.js";import"./index.720d3b34.js";import"./VInput.1586f7cc.js";import"./router.25b57325.js";import"./VImg.e193e870.js";import"./position.ea536dab.js";import"./easing.36b781ab.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VSpacer.fc3a499d.js";import"./VAvatar.cb7dc9a5.js";import"./VForm.8ba2a0de.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.8db2e7e8.js";/* empty css                   */import"./VCounter.d2fed1b1.js";import"./VNavigationDrawer.df836fef.js";import"./ssrBoot.81f110ad.js";import"./DialogCloseBtn.eb9ea561.js";import"./VDialog.11663ea2.js";import"./scopeId.1409e92e.js";import"./dialog-transition.e2b69798.js";import"./lazy.ab6dcefa.js";import"./VSelect.b041d3dc.js";import"./VList.54d6dfac.js";import"./VMenu.01138ff3.js";import"./VCheckboxBtn.cfb39e43.js";import"./VSelectionControl.e824dbee.js";import"./VChip.768b9a43.js";import"./filter.7282d4f9.js";import"./VSlideGroup.b3deddf0.js";const we={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},Ee={class:"pa-6"},ke={class:"ma-7"},Ue={class:"pt-5"},Ce=i("th",{class:"text-left"}," Quit Employee ",-1),Ne={class:"text-left"},Te=i("th",{class:"text-left"}," Remove Employee ",-1),Pe={class:"text-left"},Se=i("tr",null,[i("th",{class:"text-left"}," date "),i("th",{class:"text-left"}," Presence ")],-1),Oe=i("th",{class:"text-left"}," ALL DAYS ",-1),Be={class:"text-left"},Ie={class:"text-left"},$e={class:"text-left"},Re={class:"text-sm text-disabled"},Le={__name:"[id]",setup(qe){const _=ve(),G=s(""),D=ie(),H=s(!1),u=s({isActive:!0}),V=s(0);s([]),s(!1),s(),s(),s();const j=s(10),p=s(1),b=s(1),J=s(0);s({employees:[],dates:[]});let c=s({}),M=s({}),x=s([]),A=s({}),w=s({});const y=()=>{u.value.isActive=!0,_.getAttendanceByID(Number(D.params.id)).then(e=>{c.value=e.data.employeeAttendance,x.value=e.data.employees,console.log("employeeAttendance"),console.log(c),u.value.isActive=!1}).catch(e=>{console.error(e),u.value.isActive=!1})};I(y),I(()=>{p.value>b.value&&(p.value=b.value)}),ue(G,()=>{y()});const P=s(!1),S=s(!1),E=s(!1);let f=s();I(()=>{p.value>b.value&&(p.value=b.value)});const K=ne(()=>{const e=M.value.length?(p.value-1)*j.value+1:0,t=M.value.length+(p.value-1)*j.value;return`Showing ${e} to ${t} of ${J.value} entries`}),X=()=>{u.value.isActive=!0,_.updateAttendance(c,Number(D.params.id)).then(e=>{W(e.data.message),u.value.isActive=!1,y()}).catch(e=>{C(e.data.message),u.value.isActive=!1})},ee=e=>{u.value.isActive=!0,_.deleteAttendance(e).then(t=>{u.value.isActive=!1,W(t.data.message)}).catch(t=>{C(t.data.message),u.value.isActive=!1}),y()},O=s(!0),B=e=>(e.toString(),""),le=e=>e.name===void 0?"Select An Employee":e.name+" "+e.surname,te=()=>{u.value.isActive=!0,_.AddEmployeeToAttendance(A.value,Number(D.params.id)).then(e=>{u.value.isActive=!1,x.value=e.data.employees,c[e.data.key]=e.data.newAttendance,console.log(c),y()}).catch(e=>{C(e.data.message),u.value.isActive=!1})},ae=e=>{e.allDays=!e.allDays;for(let t=0;t<e.result.length;t++)e.result[t].present=e.allDays},oe=e=>{u.value.isActive=!0,console.log(e.result),_.RemoveEmployeeFromAttendance(e.result,Number(D.params.id)).then(t=>{u.value.isActive=!1,x=t.data.employees,c.value.hasOwnProperty(e.id)&&delete c.value[e.id],y()}).catch(t=>{C(t.data.message),u.value.isActive=!1})};return(e,t)=>{const se=de;return m(),g("section",null,[l(N,null,{default:o(()=>[l(Ve,{"model-value":r(u).isActive,class:"align-center justify-center"},{default:o(()=>[l(Y,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),l(v,{cols:"9"},{default:o(()=>[l(z,{title:"New Attendance"},{default:o(()=>[l(Z),l(T,{class:"d-flex flex-wrap py-4 gap-4"},{default:o(()=>[i("div",we,[i("div",null,[r(H)?(m(),h(Y,{key:0,indeterminate:"",color:"primary"})):$("",!0)])])]),_:1}),i("div",Ee,[l(N,null,{default:o(()=>[l(v,{cols:"8"},{default:o(()=>[l(ye,{modelValue:r(A),"onUpdate:modelValue":t[0]||(t[0]=a=>d(A)?A.value=a:A=a),items:r(x),label:"Employee","item-value":"id","item-title":le},null,8,["modelValue","items"])]),_:1}),l(v,{cols:"3"},{default:o(()=>[l(q,{block:"",color:"default",variant:"tonal",onClick:te},{default:o(()=>[k(" Add ")]),_:1})]),_:1})]),_:1})]),i("div",ke,[l(N,null,{default:o(()=>[l(v,{cols:"5",sm:"4"},{default:o(()=>[l(_e,{modelValue:r(V),"onUpdate:modelValue":t[1]||(t[1]=a=>d(V)?V.value=a:null),direction:"vertical",class:"v-tabs-pill"},{default:o(()=>[(m(!0),g(R,null,L(r(c),a=>(m(),h(be,null,{default:o(()=>[l(me,{start:"",icon:"tabler-user"}),k(" "+U(a.result[0].employee.name)+" "+U(a.result[0].employee.surname),1)]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1}),l(v,{cols:"7",sm:"8"},{default:o(()=>[l(z,null,{default:o(()=>[l(T,null,{default:o(()=>[l(Ae,{modelValue:r(V),"onUpdate:modelValue":t[3]||(t[3]=a=>d(V)?V.value=a:null)},{default:o(()=>[(m(!0),g(R,null,L(r(c),a=>(m(),h(ge,null,{default:o(()=>[l(he,null,{default:o(()=>[i("thead",null,[i("tr",Ue,[Ce,i("th",Ne,[l(N,null,{default:o(()=>[l(v,{cols:"4"},{default:o(()=>[l(De,{modelValue:a.result[0].employee.active,"onUpdate:modelValue":n=>a.result[0].employee.active=n,inset:""},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024),a.result[0].employee.active?(m(),h(v,{key:0,cols:"8"},{default:o(()=>[l(se,{modelValue:a.result[0].employee.quitDate,"onUpdate:modelValue":n=>a.result[0].employee.quitDate=n,label:"End Date"},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024)):$("",!0)]),_:2},1024)])]),i("tr",null,[Te,i("th",Pe,[l(F,{modelValue:r(w),"onUpdate:modelValue":t[2]||(t[2]=n=>d(w)?w.value=n:w=n),label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error",onClick:n=>oe(a)},null,8,["modelValue","label","onClick"])])]),Se]),i("tbody",null,[i("tr",null,[Oe,i("th",Be,[l(F,{modelValue:a.allDays,"onUpdate:modelValue":n=>a.allDays=n,label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error",onClick:n=>ae(a)},null,8,["modelValue","onUpdate:modelValue","label","onClick"])])]),(m(!0),g(R,null,L(a.result,n=>(m(),g("tr",null,[i("th",Ie,U(n.date),1),i("th",$e,[l(F,{modelValue:n.present,"onUpdate:modelValue":re=>n.present=re,label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error"},null,8,["modelValue","onUpdate:modelValue","label"])])]))),256))])]),_:2},1024)]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1})]),_:1})]),_:1})]),_:1})]),l(Z),l(T,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:o(()=>[i("span",Re,U(r(K)),1),l(xe,{modelValue:r(p),"onUpdate:modelValue":t[4]||(t[4]=a=>d(p)?p.value=a:null),size:"small","total-visible":5,length:r(b)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1}),l(v,{cols:"12",md:"3"},{default:o(()=>[l(z,{class:"mb-8"},{default:o(()=>[l(T,null,{default:o(()=>[l(q,{block:"","prepend-icon":"tabler-send",class:"mb-2"},{default:o(()=>[k(" Send Invoice ")]),_:1}),l(q,{block:"",color:"default",variant:"tonal",onClick:X},{default:o(()=>[k(" Update ")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1}),l(pe,{isDrawerOpen:r(P),"onUpdate:isDrawerOpen":t[5]||(t[5]=a=>d(P)?P.value=a:null),onEmployeeData:e.addNewEmployee},null,8,["isDrawerOpen","onEmployeeData"]),l(ce,{isDrawerOpen:r(S),"onUpdate:isDrawerOpen":t[6]||(t[6]=a=>d(S)?S.value=a:null),employee:r(f),"onUpdate:employee":t[7]||(t[7]=a=>d(f)?f.value=a:f=a),onEmployeeData:e.updateEmployee},null,8,["isDrawerOpen","employee","onEmployeeData"]),r(E)?(m(),h(fe,{key:0,isDialogVisible:r(E),"onUpdate:isDialogVisible":t[8]||(t[8]=a=>d(E)?E.value=a:null),employee:r(f),"onUpdate:employee":t[9]||(t[9]=a=>d(f)?f.value=a:f=a),onEmployeeData:ee},null,8,["isDialogVisible","employee"])):$("",!0)])}}};typeof Q=="function"&&Q(Le);export{Le as default};