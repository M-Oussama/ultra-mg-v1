import{k as t,bd as L,$ as w,Z as Q,A as Z,o as u,c as A,p as l,w as r,q as n,m as s,x as d,b,y as E,F as G,a as H,E as k,D as p,bf as B}from"./main.ccc6aae0.js";import{_ as K,a as W}from"./EditEmployeeDrawer.15033b08.js";import{_ as X}from"./ConfirmationDialog.d7e75799.js";import{s as C}from"./successMiddleware.53303d8d.js";import{e as N}from"./errorsMiddleware.ca8f0215.js";import{u as Y}from"./useAttendanceStore.ca57727c.js";import{a as ee}from"./VOverlay.0ad0cca7.js";import{a as O,V as P}from"./VBtn.7efa8e0f.js";import{a as I,V as te}from"./VRow.a17ab33e.js";import{V as S,c as U}from"./VCard.36c7adfd.js";import{V as ae}from"./VTable.484ee967.js";import"./AppDateTimePicker.be59b953.js";import"./VField.8b77e1e2.js";import"./index.720d3b34.js";import"./VInput.1586f7cc.js";import"./router.25b57325.js";import"./VImg.e193e870.js";import"./position.ea536dab.js";import"./easing.36b781ab.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VSpacer.fc3a499d.js";import"./VAvatar.cb7dc9a5.js";import"./VForm.8ba2a0de.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.8db2e7e8.js";/* empty css                   */import"./VCounter.d2fed1b1.js";import"./VAutocomplete.5bdf49b5.js";import"./VSelect.b041d3dc.js";import"./VList.54d6dfac.js";import"./VDivider.09896c6c.js";import"./dialog-transition.e2b69798.js";import"./VMenu.01138ff3.js";import"./scopeId.1409e92e.js";import"./VCheckboxBtn.cfb39e43.js";import"./VSelectionControl.e824dbee.js";import"./VChip.768b9a43.js";import"./filter.7282d4f9.js";import"./VNavigationDrawer.df836fef.js";import"./ssrBoot.81f110ad.js";import"./DialogCloseBtn.eb9ea561.js";import"./VDialog.11663ea2.js";import"./lazy.ab6dcefa.js";const se={class:"align-center flex-wrap gap-4"},oe=s("thead",null,[s("tr",null,[s("th",{class:"text-center"}," Employee "),s("th",{class:"text-center"}," Date de r\xE9crutement "),s("th",{class:"text-center"}," Jours "),s("th",{class:"text-center"}," Jours de travail "),s("th",{class:"text-center"}," Date de fin ")])],-1),le={class:"text-center"},re={class:"text-center"},ne={class:"ma-0"},ie=s("th",{class:"text-center"}," Jours(J) ",-1),ce={class:"text-center"},de={class:"text-center"},me={class:"ma-0"},ue={__name:"[id]",setup(pe){const v=Y(),$=t(""),J=L(),M=t(!1),i=t({isActive:!0});t(0),t([]),t(!0),t(),t(),t();const D=t(10),m=t(1),f=t(1),R=t(0);t();let g=t({month:"",year:""}),x=t([]),V=t({});w(()=>{i.value.isActive=!0,v.getAttendanceByID(Number(J.params.id)).then(e=>{x.value=e.data.employeeAttendance,g.value=e.data.attendance,console.log(e),i.value.isActive=!1}).catch(e=>{console.error(e),i.value.isActive=!1})}),w(()=>{m.value>f.value&&(m.value=f.value)}),Q($,()=>{fetchAttendances()});const h=t(!1),y=t(!1),_=t(!1);let c=t();w(()=>{m.value>f.value&&(m.value=f.value)}),Z(()=>{const e=V.value.length?(m.value-1)*D.value+1:0,a=V.value.length+(m.value-1)*D.value;return`Showing ${e} to ${a} of ${R.value} entries`});const T=e=>{i.value.isActive=!0,v.updateAttendance(e).then(a=>{C(a.data.message),i.value.isActive=!1,fetchAttendances()}).catch(a=>{N(a.data.message),i.value.isActive=!1})},j=e=>{i.value.isActive=!0,v.deleteAttendance(e).then(a=>{i.value.isActive=!1,C(a.data.message)}).catch(a=>{N(a.data.message),i.value.isActive=!1}),fetchAttendances()};t(!0);const F=()=>{window.print()},q=e=>e[""]?e[""][0].career.start_date:e[0][0].career.start_date,z=e=>e[""]?e[""][0].career.end_date==null?"":e[""][0].career.end_date:e[0][0].career.start_date==null?"":e[0][0].career.end_date;return(e,a)=>(u(),A("section",null,[l(te,null,{default:r(()=>[l(ee,{"model-value":n(i).isActive,class:"align-center justify-center"},{default:r(()=>[l(O,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),l(I,{cols:"12",md:"9"},{default:r(()=>[l(S,{class:"text-center"},{default:r(()=>[l(U,{class:"flex-wrap justify-space-between flex-column flex-sm-row print-row"},{default:r(()=>[s("h3",null,"Pointage Mois "+d(("0"+n(g).month).slice(-2))+"/ "+d(n(g).year),1),s("div",se,[s("div",null,[n(M)?(u(),b(O,{key:0,indeterminate:"",color:"primary"})):E("",!0)]),l(ae,{class:"mt-6"},{default:r(()=>[oe,s("tbody",null,[(u(!0),A(G,null,H(n(x),o=>(u(),A("tr",null,[s("th",le,d(o.employee.name)+" "+d(o.employee.surname),1),s("th",re,[s("p",ne,d(q(o.result)),1)]),ie,s("th",ce,d(o.present_count),1),s("th",de,[s("p",me,d(z(o.result)),1)])]))),256))])]),_:1})])]),_:1})]),_:1})]),_:1}),l(I,{cols:"12",md:"3",class:"d-print-none"},{default:r(()=>[l(S,{class:"mb-8 print-row"},{default:r(()=>[l(U,null,{default:r(()=>[l(P,{block:"","prepend-icon":"tabler-send",class:"mb-2"},{default:r(()=>[k(" Send Invoice ")]),_:1}),l(P,{block:"",color:"default",variant:"tonal",onClick:F},{default:r(()=>[k(" Print ")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1}),l(K,{isDrawerOpen:n(h),"onUpdate:isDrawerOpen":a[0]||(a[0]=o=>p(h)?h.value=o:null),onEmployeeData:e.addNewEmployee},null,8,["isDrawerOpen","onEmployeeData"]),l(W,{isDrawerOpen:n(y),"onUpdate:isDrawerOpen":a[1]||(a[1]=o=>p(y)?y.value=o:null),employee:n(c),"onUpdate:employee":a[2]||(a[2]=o=>p(c)?c.value=o:c=o),onEmployeeData:T},null,8,["isDrawerOpen","employee"]),n(_)?(u(),b(X,{key:0,isDialogVisible:n(_),"onUpdate:isDialogVisible":a[3]||(a[3]=o=>p(_)?_.value=o:null),employee:n(c),"onUpdate:employee":a[4]||(a[4]=o=>p(c)?c.value=o:c=o),onEmployeeData:j},null,8,["isDialogVisible","employee"])):E("",!0)]))}};typeof B=="function"&&B(ue);export{ue as default};