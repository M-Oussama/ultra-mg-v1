import{k as t,bd as z,$ as w,Z as L,A as Q,o as u,c as A,p as o,w as r,q as n,m as s,x as d,b,y as E,F as Z,a as G,E as k,D as p}from"./main.eb2abb0f.js";import{_ as H,a as K}from"./EditEmployeeDrawer.b9313ef2.js";import{_ as W}from"./ConfirmationDialog.aedfa57e.js";import{s as B}from"./successMiddleware.5e0a2589.js";import{e as C}from"./errorsMiddleware.9385531a.js";import{u as X}from"./useAttendanceStore.378d4a26.js";import{a as Y}from"./VOverlay.8b37277a.js";import{a as N,V as O}from"./VBtn.4b29655b.js";import{a as P,V as ee}from"./VRow.fc64865f.js";import{V as I,c as S}from"./VCard.d18cee11.js";import{V as te}from"./VTable.dc32512e.js";import"./AppDateTimePicker.444d287c.js";import"./VField.d31c7729.js";import"./index.1d60d957.js";import"./VInput.12678d62.js";import"./router.cee8299d.js";import"./VImg.c89b2431.js";import"./position.4305ca24.js";import"./easing.36b781ab.js";import"./validators.330a354f.js";import"./index.b522f886.js";import"./VSpacer.47fd3527.js";import"./VAvatar.ef806db0.js";import"./VForm.b7335b57.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.cfaf068e.js";/* empty css                   */import"./VCounter.28fd36d2.js";import"./VAutocomplete.984d747d.js";import"./VSelect.1a2459fe.js";import"./VList.abe45db6.js";import"./VDivider.d741eb3a.js";import"./dialog-transition.ae223f33.js";import"./VMenu.6af9ca11.js";import"./scopeId.deee8bf4.js";import"./VCheckboxBtn.c57877f7.js";import"./VSelectionControl.b7f754bc.js";import"./VChip.574f5191.js";import"./filter.44b73af0.js";import"./VNavigationDrawer.5a003ebe.js";import"./ssrBoot.b678eb07.js";import"./DialogCloseBtn.d07cf106.js";import"./VDialog.9c7be325.js";import"./lazy.4714770b.js";const ae={class:"align-center flex-wrap gap-4"},se=s("thead",null,[s("tr",null,[s("th",{class:"text-center"}," Employee "),s("th",{class:"text-center"}," Date de r\xE9crutement "),s("th",{class:"text-center"}," Jours "),s("th",{class:"text-center"}," Jours de travail "),s("th",{class:"text-center"}," Date de fin ")])],-1),le={class:"text-center"},oe={class:"text-center"},re={class:"ma-0"},ne=s("th",{class:"text-center"}," Jours(J) ",-1),ie={class:"text-center"},ce={class:"text-center"},de={class:"ma-0"},st={__name:"[id]",setup(me){const v=X(),U=t(""),$=z(),J=t(!1),i=t({isActive:!0});t(0),t([]),t(!0),t(),t(),t();const D=t(10),m=t(1),f=t(1),M=t(0);t();let g=t({month:"",year:""}),x=t([]),V=t({});w(()=>{i.value.isActive=!0,v.getAttendanceByID(Number($.params.id)).then(e=>{x.value=e.data.employeeAttendance,g.value=e.data.attendance,console.log(e),i.value.isActive=!1}).catch(e=>{console.error(e),i.value.isActive=!1})}),w(()=>{m.value>f.value&&(m.value=f.value)}),L(U,()=>{fetchAttendances()});const h=t(!1),y=t(!1),_=t(!1);let c=t();w(()=>{m.value>f.value&&(m.value=f.value)}),Q(()=>{const e=V.value.length?(m.value-1)*D.value+1:0,a=V.value.length+(m.value-1)*D.value;return`Showing ${e} to ${a} of ${M.value} entries`});const R=e=>{i.value.isActive=!0,v.updateAttendance(e).then(a=>{B(a.data.message),i.value.isActive=!1,fetchAttendances()}).catch(a=>{C(a.data.message),i.value.isActive=!1})},T=e=>{i.value.isActive=!0,v.deleteAttendance(e).then(a=>{i.value.isActive=!1,B(a.data.message)}).catch(a=>{C(a.data.message),i.value.isActive=!1}),fetchAttendances()};t(!0);const j=()=>{window.print()},F=e=>e[""]?e[""][0].career.start_date:e[0][0].career.start_date,q=e=>e[""]?e[""][0].career.end_date==null?"":e[""][0].career.end_date:e[0][0].career.start_date==null?"":e[0][0].career.end_date;return(e,a)=>(u(),A("section",null,[o(ee,null,{default:r(()=>[o(Y,{"model-value":n(i).isActive,class:"align-center justify-center"},{default:r(()=>[o(N,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),o(P,{cols:"12",md:"9"},{default:r(()=>[o(I,{class:"text-center"},{default:r(()=>[o(S,{class:"flex-wrap justify-space-between flex-column flex-sm-row print-row"},{default:r(()=>[s("h3",null,"Pointage Mois "+d(("0"+n(g).month).slice(-2))+"/ "+d(n(g).year),1),s("div",ae,[s("div",null,[n(J)?(u(),b(N,{key:0,indeterminate:"",color:"primary"})):E("",!0)]),o(te,{class:"mt-6"},{default:r(()=>[se,s("tbody",null,[(u(!0),A(Z,null,G(n(x),l=>(u(),A("tr",null,[s("th",le,d(l.employee.name)+" "+d(l.employee.surname),1),s("th",oe,[s("p",re,d(F(l.result)),1)]),ne,s("th",ie,d(l.present_count),1),s("th",ce,[s("p",de,d(q(l.result)),1)])]))),256))])]),_:1})])]),_:1})]),_:1})]),_:1}),o(P,{cols:"12",md:"3",class:"d-print-none"},{default:r(()=>[o(I,{class:"mb-8 print-row"},{default:r(()=>[o(S,null,{default:r(()=>[o(O,{block:"","prepend-icon":"tabler-send",class:"mb-2"},{default:r(()=>[k(" Send Invoice ")]),_:1}),o(O,{block:"",color:"default",variant:"tonal",onClick:j},{default:r(()=>[k(" Print ")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1}),o(H,{isDrawerOpen:n(h),"onUpdate:isDrawerOpen":a[0]||(a[0]=l=>p(h)?h.value=l:null),onEmployeeData:e.addNewEmployee},null,8,["isDrawerOpen","onEmployeeData"]),o(K,{isDrawerOpen:n(y),"onUpdate:isDrawerOpen":a[1]||(a[1]=l=>p(y)?y.value=l:null),employee:n(c),"onUpdate:employee":a[2]||(a[2]=l=>p(c)?c.value=l:c=l),onEmployeeData:R},null,8,["isDrawerOpen","employee"]),n(_)?(u(),b(W,{key:0,isDialogVisible:n(_),"onUpdate:isDialogVisible":a[3]||(a[3]=l=>p(_)?_.value=l:null),employee:n(c),"onUpdate:employee":a[4]||(a[4]=l=>p(c)?c.value=l:c=l),onEmployeeData:T},null,8,["isDialogVisible","employee"])):E("",!0)]))}};export{st as default};