import{k as s,bd as re,$ as I,Z as ie,A as ue,o as m,c as g,p as l,w as o,q as r,m as i,b as h,y as $,D as d,E as k,F as R,a as L,C as ne,x as U}from"./main.60095230.js";import{_ as me}from"./AppDateTimePicker.378caec5.js";import{_ as de,a as pe}from"./EditEmployeeDrawer.316c7d67.js";import{_ as ce}from"./ConfirmationDialog.ae707c98.js";import{s as Q,e as C}from"./errorsMiddleware.1a224d60.js";import{u as fe}from"./useAttendanceStore.08c176cc.js";import{a as ve}from"./VOverlay.07c5d8cd.js";import{a as W,V as q}from"./VBtn.5ecb9490.js";import{a as v,V as N}from"./VRow.8955dee0.js";import{V as z,c as T}from"./VCard.b8af7deb.js";import{V as Y}from"./VDivider.f2016316.js";import{V as Ve}from"./VAutocomplete.ee5898e7.js";import{V as ye,a as _e}from"./VTabs.1e48a5b5.js";import{V as be,a as Ae}from"./VWindowItem.f2995b37.js";import{V as ge}from"./VTable.c64d737f.js";import{V as he}from"./VSwitch.6912ca31.js";import{V as F}from"./VCheckbox.846aa4cb.js";import{V as De}from"./VPagination.58915958.js";import"./VField.e7600162.js";import"./index.7d0b373a.js";import"./VInput.c62758ee.js";import"./router.717d3535.js";import"./VImg.cdaed533.js";import"./position.e6185ce0.js";import"./easing.36b781ab.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VSpacer.fd46eca3.js";import"./VAvatar.ad95b801.js";import"./VForm.ec185ea1.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.9b7b3347.js";/* empty css                   */import"./VCounter.5d1d4da1.js";import"./VNavigationDrawer.0f9444a5.js";import"./ssrBoot.2c77fa9a.js";import"./DialogCloseBtn.2ff0cdff.js";import"./VDialog.6e21af97.js";import"./scopeId.0ada01c1.js";import"./dialog-transition.4295e999.js";import"./lazy.e4a387c0.js";import"./VSelect.4cefa2aa.js";import"./VList.49cad3e0.js";import"./VMenu.5abb403b.js";import"./VCheckboxBtn.248e09d9.js";import"./VSelectionControl.5c498ebb.js";import"./VChip.64738807.js";import"./filter.faf1d54c.js";import"./VSlideGroup.e21457b7.js";const xe={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},we={class:"pa-6"},Ee={class:"ma-7"},ke={class:"pt-5"},Ue=i("th",{class:"text-left"}," Quit Employee ",-1),Ce={class:"text-left"},Ne=i("th",{class:"text-left"}," Remove Employee ",-1),Te={class:"text-left"},Pe=i("tr",null,[i("th",{class:"text-left"}," date "),i("th",{class:"text-left"}," Presence ")],-1),Se=i("th",{class:"text-left"}," ALL DAYS ",-1),Oe={class:"text-left"},Be={class:"text-left"},Ie={class:"text-left"},$e={class:"text-sm text-disabled"},Ol={__name:"[id]",setup(Re){const _=fe(),Z=s(""),D=re(),G=s(!1),u=s({isActive:!0}),V=s(0);s([]),s(!1),s(),s(),s();const j=s(10),p=s(1),b=s(1),H=s(0);s({employees:[],dates:[]});let c=s({}),M=s({}),x=s([]),A=s({}),w=s({});const y=()=>{u.value.isActive=!0,_.getAttendanceByID(Number(D.params.id)).then(e=>{c.value=e.data.employeeAttendance,x.value=e.data.employees,console.log("employeeAttendance"),console.log(c),u.value.isActive=!1}).catch(e=>{console.error(e),u.value.isActive=!1})};I(y),I(()=>{p.value>b.value&&(p.value=b.value)}),ie(Z,()=>{y()});const P=s(!1),S=s(!1),E=s(!1);let f=s();I(()=>{p.value>b.value&&(p.value=b.value)});const J=ue(()=>{const e=M.value.length?(p.value-1)*j.value+1:0,t=M.value.length+(p.value-1)*j.value;return`Showing ${e} to ${t} of ${H.value} entries`}),K=()=>{u.value.isActive=!0,_.updateAttendance(c,Number(D.params.id)).then(e=>{Q(e.data.message),u.value.isActive=!1,y()}).catch(e=>{C(e.data.message),u.value.isActive=!1})},X=e=>{u.value.isActive=!0,_.deleteAttendance(e).then(t=>{u.value.isActive=!1,Q(t.data.message)}).catch(t=>{C(t.data.message),u.value.isActive=!1}),y()},O=s(!0),B=e=>(e.toString(),""),ee=e=>e.name===void 0?"Select An Employee":e.name+" "+e.surname,le=()=>{u.value.isActive=!0,_.AddEmployeeToAttendance(A.value,Number(D.params.id)).then(e=>{u.value.isActive=!1,x.value=e.data.employees,c[e.data.key]=e.data.newAttendance,console.log(c),y()}).catch(e=>{C(e.data.message),u.value.isActive=!1})},te=e=>{e.allDays=!e.allDays;for(let t=0;t<e.result.length;t++)e.result[t].present=e.allDays},ae=e=>{u.value.isActive=!0,console.log(e.result),_.RemoveEmployeeFromAttendance(e.result,Number(D.params.id)).then(t=>{u.value.isActive=!1,x=t.data.employees,c.value.hasOwnProperty(e.id)&&delete c.value[e.id],y()}).catch(t=>{C(t.data.message),u.value.isActive=!1})};return(e,t)=>{const oe=me;return m(),g("section",null,[l(N,null,{default:o(()=>[l(ve,{"model-value":r(u).isActive,class:"align-center justify-center"},{default:o(()=>[l(W,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),l(v,{cols:"9"},{default:o(()=>[l(z,{title:"New Attendance"},{default:o(()=>[l(Y),l(T,{class:"d-flex flex-wrap py-4 gap-4"},{default:o(()=>[i("div",xe,[i("div",null,[r(G)?(m(),h(W,{key:0,indeterminate:"",color:"primary"})):$("",!0)])])]),_:1}),i("div",we,[l(N,null,{default:o(()=>[l(v,{cols:"8"},{default:o(()=>[l(Ve,{modelValue:r(A),"onUpdate:modelValue":t[0]||(t[0]=a=>d(A)?A.value=a:A=a),items:r(x),label:"Employee","item-value":"id","item-title":ee},null,8,["modelValue","items"])]),_:1}),l(v,{cols:"3"},{default:o(()=>[l(q,{block:"",color:"default",variant:"tonal",onClick:le},{default:o(()=>[k(" Add ")]),_:1})]),_:1})]),_:1})]),i("div",Ee,[l(N,null,{default:o(()=>[l(v,{cols:"5",sm:"4"},{default:o(()=>[l(ye,{modelValue:r(V),"onUpdate:modelValue":t[1]||(t[1]=a=>d(V)?V.value=a:null),direction:"vertical",class:"v-tabs-pill"},{default:o(()=>[(m(!0),g(R,null,L(r(c),a=>(m(),h(_e,null,{default:o(()=>[l(ne,{start:"",icon:"tabler-user"}),k(" "+U(a.result[0].employee.name)+" "+U(a.result[0].employee.surname),1)]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1}),l(v,{cols:"7",sm:"8"},{default:o(()=>[l(z,null,{default:o(()=>[l(T,null,{default:o(()=>[l(be,{modelValue:r(V),"onUpdate:modelValue":t[3]||(t[3]=a=>d(V)?V.value=a:null)},{default:o(()=>[(m(!0),g(R,null,L(r(c),a=>(m(),h(Ae,null,{default:o(()=>[l(ge,null,{default:o(()=>[i("thead",null,[i("tr",ke,[Ue,i("th",Ce,[l(N,null,{default:o(()=>[l(v,{cols:"4"},{default:o(()=>[l(he,{modelValue:a.result[0].employee.active,"onUpdate:modelValue":n=>a.result[0].employee.active=n,inset:""},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024),a.result[0].employee.active?(m(),h(v,{key:0,cols:"8"},{default:o(()=>[l(oe,{modelValue:a.result[0].employee.quitDate,"onUpdate:modelValue":n=>a.result[0].employee.quitDate=n,label:"End Date"},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024)):$("",!0)]),_:2},1024)])]),i("tr",null,[Ne,i("th",Te,[l(F,{modelValue:r(w),"onUpdate:modelValue":t[2]||(t[2]=n=>d(w)?w.value=n:w=n),label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error",onClick:n=>ae(a)},null,8,["modelValue","label","onClick"])])]),Pe]),i("tbody",null,[i("tr",null,[Se,i("th",Oe,[l(F,{modelValue:a.allDays,"onUpdate:modelValue":n=>a.allDays=n,label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error",onClick:n=>te(a)},null,8,["modelValue","onUpdate:modelValue","label","onClick"])])]),(m(!0),g(R,null,L(a.result,n=>(m(),g("tr",null,[i("th",Be,U(n.date),1),i("th",Ie,[l(F,{modelValue:n.present,"onUpdate:modelValue":se=>n.present=se,label:B(r(O)),"true-icon":"tabler-check","false-icon":"tabler-circle-x",color:"error"},null,8,["modelValue","onUpdate:modelValue","label"])])]))),256))])]),_:2},1024)]),_:2},1024))),256))]),_:1},8,["modelValue"])]),_:1})]),_:1})]),_:1})]),_:1})]),l(Y),l(T,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:o(()=>[i("span",$e,U(r(J)),1),l(De,{modelValue:r(p),"onUpdate:modelValue":t[4]||(t[4]=a=>d(p)?p.value=a:null),size:"small","total-visible":5,length:r(b)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1}),l(v,{cols:"12",md:"3"},{default:o(()=>[l(z,{class:"mb-8"},{default:o(()=>[l(T,null,{default:o(()=>[l(q,{block:"","prepend-icon":"tabler-send",class:"mb-2"},{default:o(()=>[k(" Send Invoice ")]),_:1}),l(q,{block:"",color:"default",variant:"tonal",onClick:K},{default:o(()=>[k(" Update ")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1}),l(de,{isDrawerOpen:r(P),"onUpdate:isDrawerOpen":t[5]||(t[5]=a=>d(P)?P.value=a:null),onEmployeeData:e.addNewEmployee},null,8,["isDrawerOpen","onEmployeeData"]),l(pe,{isDrawerOpen:r(S),"onUpdate:isDrawerOpen":t[6]||(t[6]=a=>d(S)?S.value=a:null),employee:r(f),"onUpdate:employee":t[7]||(t[7]=a=>d(f)?f.value=a:f=a),onEmployeeData:e.updateEmployee},null,8,["isDrawerOpen","employee","onEmployeeData"]),r(E)?(m(),h(ce,{key:0,isDialogVisible:r(E),"onUpdate:isDialogVisible":t[8]||(t[8]=a=>d(E)?E.value=a:null),employee:r(f),"onUpdate:employee":t[9]||(t[9]=a=>d(f)?f.value=a:f=a),onEmployeeData:X},null,8,["isDialogVisible","employee"])):$("",!0)])}}};export{Ol as default};