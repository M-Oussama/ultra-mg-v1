import{k as s,Z as L,o as S,b as $,w as a,m as r,p as e,C as P,q as n,P as Q,D as y,be as W,E as h,ay as G,bd as J,r as re,$ as q,A as de,c as B,F as ie,a as ue,x as F,y as pe,H as ce,I as me}from"./main.e3ae6f5b.js";import{_ as fe}from"./DialogCloseBtn.91cc9156.js";import{s as M,e as N}from"./errorsMiddleware.cd79b49a.js";import{u as R}from"./useAttendanceStore.8b7f1f54.js";import{_ as ve}from"./AppDateTimePicker.fa89fced.js";import{V as j}from"./VSpacer.50e7ecc1.js";import{V,a as H}from"./VBtn.78b9f864.js";import{V as z,c as U}from"./VCard.9574192c.js";import{V as K}from"./VForm.724a7186.js";import{V as Y,a as O}from"./VRow.307db058.js";import{V as X}from"./VNavigationDrawer.2a3e31b0.js";import{a as _e}from"./VOverlay.bef3a4a8.js";import{V as I}from"./VDivider.04b9ef9a.js";import{V as ge}from"./VSelect.e8db8923.js";import{V as ye}from"./VTextField.c4710a67.js";import{V as Ve}from"./VTable.24eac033.js";import{V as De}from"./VPagination.29fbda3c.js";import{V as we}from"./VDialog.6cfe2dfa.js";import"./VField.93385836.js";import"./index.ba8dc909.js";import"./VInput.ebff45cb.js";import"./router.92e08cce.js";import"./VImg.a8f9a1c8.js";import"./position.4c99774d.js";import"./easing.36b781ab.js";import"./VAvatar.a66dff35.js";import"./forwardRefs.c003b6b8.js";import"./ssrBoot.170b97eb.js";import"./lazy.f3535b64.js";import"./VList.d6dc74e3.js";import"./dialog-transition.cc00eae2.js";import"./VMenu.d6d893ca.js";import"./scopeId.55b8accf.js";import"./VCheckboxBtn.41b91637.js";import"./VSelectionControl.90a7537b.js";import"./VChip.dd80b8e5.js";/* empty css                   */import"./VCounter.a137105a.js";const be={class:"d-flex align-center pa-6 pb-1"},xe=r("h6",{class:"text-h6"}," Update End Date ",-1),he={__name:"EditEmployeeAttendanceDrawer",props:{isDrawerOpen:{type:null,required:!0},loading:{type:null,required:!0},employee:{type:Object,required:!0},disableEndDate:{type:Boolean,required:!0},disableStartDate:{type:Boolean,required:!0}},setup(E){const d=E,A=R(),m=s({start_date:"",end_date:""});L(d.isDrawerOpen,()=>{d.isDrawerOpen.open&&(console.log(d.employee),m.value.id=d.employee.id||"",m.value.start_date=d.employee.start_date||"",m.value.end_date=d.employee.end_date||"")});const o=s(!1),b=s();s(),s({id:-1});const C=()=>{d.isDrawerOpen.open=!1,G(()=>{var u,f;(u=b.value)==null||u.reset(),(f=b.value)==null||f.resetValidation()})},w=()=>{d.loading.isActive=!0,console.log(d.loading),A.updateEndDate(m.value.end_date,m.value.start_date,m.value.id).then(u=>{d.isDrawerOpen.open=!1,d.loading.isActive=!1,d.employee.end_date=u.data.endDate,M(u.data.msg)}).catch(u=>{d.loading.isActive=!1,N("Error Occured",u.response.data.message)})},_=u=>{d.isDrawerOpen.value.open=u};return(u,f)=>{const v=ve;return S(),$(X,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":d.isDrawerOpen.open,"onUpdate:modelValue":_},{default:a(()=>[r("div",be,[xe,e(j),e(V,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:C},{default:a(()=>[e(P,{size:"18",icon:"tabler-x"})]),_:1})]),e(n(Q),{options:{wheelPropagation:!1}},{default:a(()=>[e(z,{flat:""},{default:a(()=>[e(U,null,{default:a(()=>[e(K,{ref_key:"refForm",ref:b,modelValue:n(o),"onUpdate:modelValue":f[2]||(f[2]=g=>y(o)?o.value=g:null),onSubmit:W(w,["prevent"])},{default:a(()=>[e(Y,null,{default:a(()=>[e(O,{cols:"12"},{default:a(()=>[e(v,{modelValue:n(m).start_date,"onUpdate:modelValue":f[0]||(f[0]=g=>n(m).start_date=g),label:"Start Date",disabled:E.disableStartDate},null,8,["modelValue","disabled"])]),_:1}),e(O,{cols:"12"},{default:a(()=>[e(v,{modelValue:n(m).end_date,"onUpdate:modelValue":f[1]||(f[1]=g=>n(m).end_date=g),label:"End Date",config:{minDate:`

                  ${n(m).start_date}`},disabled:E.disableEndDate},null,8,["modelValue","config","disabled"])]),_:1}),e(O,{cols:"12"},{default:a(()=>[e(V,{type:"submit",class:"me-3"},{default:a(()=>[h(" Submit ")]),_:1}),e(V,{type:"reset",variant:"tonal",color:"secondary",onClick:C},{default:a(()=>[h(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"])}}};const Ae={class:"d-flex align-center pa-6 pb-1"},Ce=r("h6",{class:"text-h6"}," Add New Record ",-1),Se={__name:"AddEmployeeAttendanceDrawer",props:{isDrawerOpen:{type:null,required:!0},loading:{type:null,required:!0},employees:{type:Object,required:!0}},setup(E){const d=E,A=R(),m=J(),o=s({start_date:new Date().getFullYear()+"-"+(new Date().getMonth()+1)+"-"+new Date().getDate(),end_date:"",end_date_restrict:new Date().getFullYear()+"-"+(new Date().getMonth()+1)+"-"+(new Date().getDate()+1)}),b=s(!1),C=s();s(),s({id:-1});const w=()=>{d.isDrawerOpen.open=!1,G(()=>{var i,p;(i=C.value)==null||i.reset(),(p=C.value)==null||p.resetValidation()})},_=()=>{o.value.start_date===""?N("Warning","start date must be selected"):(d.loading.isActive=!0,console.log(d.loading),A.addEmployeeAttendanceRecord(o.value.end_date,o.value.start_date,Number(m.params.id)).then(i=>{d.isDrawerOpen.open=!1,d.loading.isActive=!1,M(i.data.msg),location.reload()}).catch(i=>{d.loading.isActive=!1,N("Error Occured",i.response.data.message)}))},u=i=>{d.isDrawerOpen.value.open=i};function f(i){return parseInt(i)<10?"0"+i:i}const v=i=>{const p=i.getDate(),x=i.getMonth()+1;return`${i.getFullYear()}-${f(x)}-${f(p)}`},g=()=>{(o.value.start_date===""||o.value.start_date>=o.value.end_date&&o.value.end_date!=="")&&(o.value.end_date=""),o.value.end_date_restrict=new Date(new Date(o.value.start_date).getTime()+24*60*60*2e3).toISOString().split("T")[0]};return(i,p)=>{const x=re("VueDatePicker");return S(),$(X,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":d.isDrawerOpen.open,"onUpdate:modelValue":u},{default:a(()=>[r("div",Ae,[Ce,e(j),e(V,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:w},{default:a(()=>[e(P,{size:"18",icon:"tabler-x"})]),_:1})]),e(n(Q),{options:{wheelPropagation:!1}},{default:a(()=>[e(z,{flat:""},{default:a(()=>[e(U,null,{default:a(()=>[e(K,{ref_key:"refForm",ref:C,modelValue:n(b),"onUpdate:modelValue":p[2]||(p[2]=D=>y(b)?b.value=D:null),onSubmit:W(_,["prevent"])},{default:a(()=>[e(Y,null,{default:a(()=>[e(O,{cols:"12"},{default:a(()=>[e(x,{modelValue:n(o).start_date,"onUpdate:modelValue":[p[0]||(p[0]=D=>n(o).start_date=D),g],"auto-apply":"",format:v},null,8,["modelValue"])]),_:1}),e(O,{cols:"12"},{default:a(()=>[e(x,{modelValue:n(o).end_date,"onUpdate:modelValue":p[1]||(p[1]=D=>n(o).end_date=D),"auto-apply":"",format:v,"min-date":n(o).end_date_restrict},null,8,["modelValue","min-date"])]),_:1}),e(O,{cols:"12"},{default:a(()=>[e(V,{type:"submit",class:"me-3"},{default:a(()=>[h(" Submit ")]),_:1}),e(V,{type:"reset",variant:"tonal",color:"secondary",onClick:w},{default:a(()=>[h(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"])}}};const ke={class:"me-3",style:{width:"80px"}},Oe={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},Ee={style:{width:"9rem"}},Ue=r("thead",null,[r("tr",null,[r("th",{scope:"col"}," ID "),r("th",{scope:"col"}," Start Date "),r("th",{scope:"col"}," End Date "),r("th",{scope:"col"}," ACTIONS ")])],-1),$e={class:"d-flex align-center"},Fe={class:"d-flex flex-column"},Pe={class:"text-base"},Ne={class:"d-flex align-center"},ze={class:"d-flex flex-column"},Te={class:"text-base"},qe={class:"text-center",style:{width:"5rem"}},Be=r("tr",null,[r("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Ie=[Be],Me={class:"text-sm text-disabled"},Ct={__name:"[id]",setup(E){const d=R(),A=s(""),m=s(!1),o=s({isActive:!0}),b=J(),C=s(!0);s(),s(),s();const w=s(10),_=s(1),u=s(1),f=s(0);let v=s([]),g=s({open:!1}),i=s({open:!1}),p=s({open:!1}),x=s(!1),D=s(!0);const T=()=>{o.value.isActive=!0,d.fetchEmployeesByAttendance(Number(b.params.id)).then(c=>{v.value=c.data.employees.data,u.value=c.data.totalPage,f.value=c.data.totalEmployees,m.value=!1,o.value.isActive=!1,console.log(c)}).catch(c=>{console.error(c),o.value.isActive=!1})};q(T),q(()=>{_.value>u.value&&(_.value=u.value)}),L(A,()=>{T()}),s(!1);let k=s(null);q(()=>{_.value>u.value&&(_.value=u.value)});const ee=de(()=>{const c=v.value.length?(_.value-1)*w.value+1:0,l=v.value.length+(_.value-1)*w.value;return`Showing ${c} to ${l} of ${f.value} entries`}),te=c=>{k.value=c,g.value.open=!0},ae=c=>{k.value=c,i.value.open=!0},le=()=>{p.value.open=!0},oe=()=>{o.value.isActive=!0,i.value.open=!1,d.deleteCareer(k.value.id).then(c=>{o.value.isActive=!1,M(c.data.message),T()}).catch(c=>{N(c.data.message),o.value.isActive=!1})},Z=()=>{i.value.open=!1};return(c,l)=>{const ne=fe;return S(),B("section",null,[e(Y,null,{default:a(()=>[e(_e,{"model-value":n(o).isActive,class:"align-center justify-center"},{default:a(()=>[e(H,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),e(O,{cols:"12"},{default:a(()=>[e(z,{title:"Employees"},{default:a(()=>[e(I),e(U,{class:"d-flex flex-wrap py-4 gap-4"},{default:a(()=>[r("div",ke,[e(ge,{modelValue:n(w),"onUpdate:modelValue":l[0]||(l[0]=t=>y(w)?w.value=t:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),e(j),r("div",Oe,[r("div",Ee,[n(m)?(S(),$(H,{key:0,indeterminate:"",color:"primary"})):(S(),$(ye,{key:1,onInput:l[1]||(l[1]=t=>C.value=!0),ref:"searchField",modelValue:n(A),"onUpdate:modelValue":l[2]||(l[2]=t=>y(A)?A.value=t:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),e(V,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:a(()=>[h(" Export ")]),_:1}),e(V,{color:"primary","prepend-icon":"tabler-plus",onClick:l[3]||(l[3]=t=>le())},{default:a(()=>[h(" New Record ")]),_:1})])]),_:1}),e(I),e(Ve,{class:"text-no-wrap"},{default:a(()=>[Ue,r("tbody",null,[(S(!0),B(ie,null,ue(n(v),t=>(S(),B("tr",{key:t.id,style:{height:"3.75rem"}},[r("td",null,F(t.id),1),r("td",null,[r("div",$e,[r("div",Fe,[r("h6",Pe,F(t.start_date),1)])])]),r("td",null,[r("div",Ne,[r("div",ze,[r("h6",Te,F(t.end_date),1)])])]),r("td",qe,[t.last?(S(),$(V,{key:0,icon:"",size:"x-small",color:"default",variant:"text",onClick:se=>te(t)},{default:a(()=>[e(P,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"])):pe("",!0),e(V,{icon:"",size:"x-small",color:"default",variant:"text"},{default:a(()=>[e(P,{size:"22",icon:"tabler-trash",onClick:se=>ae(t)},null,8,["onClick"])]),_:2},1024)])]))),128))]),ce(r("tfoot",null,Ie,512),[[me,!n(v).length]])]),_:1}),e(I),e(U,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:a(()=>[r("span",Me,F(n(ee)),1),e(De,{modelValue:n(_),"onUpdate:modelValue":l[4]||(l[4]=t=>y(_)?_.value=t:null),size:"small","total-visible":5,length:n(u)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),e(he,{isDrawerOpen:n(g),"onUpdate:isDrawerOpen":l[5]||(l[5]=t=>y(g)?g.value=t:g=t),employee:n(k),"onUpdate:employee":l[6]||(l[6]=t=>y(k)?k.value=t:k=t),"disable-end-date":n(x),"onUpdate:disable-end-date":l[7]||(l[7]=t=>y(x)?x.value=t:x=t),loading:n(o),"onUpdate:loading":l[8]||(l[8]=t=>y(o)?o.value=t:null),"disable-start-date":n(D),"onUpdate:disable-start-date":l[9]||(l[9]=t=>y(D)?D.value=t:D=t)},null,8,["isDrawerOpen","employee","disable-end-date","loading","disable-start-date"]),e(Se,{isDrawerOpen:n(p),"onUpdate:isDrawerOpen":l[10]||(l[10]=t=>y(p)?p.value=t:p=t),employees:n(v),"onUpdate:employees":l[11]||(l[11]=t=>y(v)?v.value=t:v=t),loading:n(o),"onUpdate:loading":l[12]||(l[12]=t=>y(o)?o.value=t:null)},null,8,["isDrawerOpen","employees","loading"]),e(we,{modelValue:n(i).open,"onUpdate:modelValue":l[15]||(l[15]=t=>n(i).open=t),persistent:"",class:"v-dialog-sm"},{default:a(()=>[e(ne,{onClick:l[13]||(l[13]=t=>Z())}),e(z,{title:"Confirmation"},{default:a(()=>[e(U,null,{default:a(()=>[h(" Are you sure you want to delete this record ? ")]),_:1}),e(U,{class:"d-flex justify-end gap-3 flex-wrap"},{default:a(()=>[e(V,{color:"secondary",variant:"tonal",onClick:l[14]||(l[14]=t=>Z())},{default:a(()=>[h(" Disagree ")]),_:1}),e(V,{onClick:oe},{default:a(()=>[h(" Agree ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])])}}};export{Ct as default};