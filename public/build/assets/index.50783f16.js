import{k as o,o as y,b as P,w as l,m as a,p as e,C as B,q as n,P as ae,D as m,be as oe,E as I,ay as Q,Z as ee,bc as _e,a1 as j,z as he,A as se,bt as H,c as $,F as E,a as M,x as N,$ as G,r as ye,H as xe,I as we,y as Ce}from"./main.60095230.js";import{_ as ne}from"./DialogCloseBtn.2ff0cdff.js";import{r as J,e as ie}from"./validators.330a354f.js";import{V as le}from"./VSpacer.fd46eca3.js";import{V as z,a as te}from"./VBtn.5ecb9490.js";import{V as q,c as L}from"./VCard.b8af7deb.js";import{V as re}from"./VForm.ec185ea1.js";import{V as Z,a as w}from"./VRow.8955dee0.js";import{V as A}from"./VTextField.9b7b3347.js";import{V as ue}from"./VAutocomplete.ee5898e7.js";import{V as de}from"./VNavigationDrawer.0f9444a5.js";import{V as pe}from"./VDialog.6e21af97.js";import{V as Se}from"./vue3-apexcharts.common.2994d79d.js";import{V as ke}from"./VMenu.5abb403b.js";import{V as De,a as Ne,b as Ae}from"./VList.49cad3e0.js";import{a as Oe}from"./VOverlay.07c5d8cd.js";import{V as Y}from"./VDivider.f2016316.js";import{V as Ue}from"./VSelect.4cefa2aa.js";import{V as K}from"./VTable.c64d737f.js";import{V as ze}from"./VAvatar.ad95b801.js";import{V as Pe}from"./VImg.cdaed533.js";import{V as Ie}from"./VPagination.58915958.js";import{V as $e,a as Fe}from"./VTabs.1e48a5b5.js";import{V as Te,a as X}from"./VWindowItem.f2995b37.js";import"./index.b522f886.js";import"./router.717d3535.js";import"./position.e6185ce0.js";import"./VInput.c62758ee.js";import"./index.7d0b373a.js";import"./forwardRefs.c003b6b8.js";/* empty css                   */import"./VField.e7600162.js";import"./easing.36b781ab.js";import"./VCounter.5d1d4da1.js";import"./filter.faf1d54c.js";import"./VCheckboxBtn.248e09d9.js";import"./VSelectionControl.5c498ebb.js";import"./VChip.64738807.js";import"./ssrBoot.2c77fa9a.js";import"./scopeId.0ada01c1.js";import"./dialog-transition.4295e999.js";import"./vue.runtime.esm-bundler.6231c809.js";import"./lazy.e4a387c0.js";import"./VSlideGroup.e21457b7.js";const Re={class:"d-flex align-center pa-6 pb-1"},Le=a("h6",{class:"text-h6"}," Add Client ",-1),We={__name:"AddNewClientDrawer",props:{isDrawerOpen:{type:Boolean,required:!0},cities:{type:Array,required:!0}},emits:["update:isDrawerOpen","clientData"],setup(O,{emit:V}){const g=O,x=o(!1),_=o(),v=o(""),c=o(""),s=o(""),f=o(""),h=o(""),b=o(""),d=o(""),C=o(""),k=o("");o(""),o(""),o("");const S=o(null);o(),o(),o(!1),o(!1),o(),o(),o();const U=()=>{V("update:isDrawerOpen",!1),Q(()=>{var p,t;(p=_.value)==null||p.reset(),(t=_.value)==null||t.resetValidation()})},F=()=>{var p;(p=_.value)==null||p.validate().then(({valid:t})=>{t&&(V("clientData",{name:v.value,surname:c.value,email:s.value,address:f.value,phone:h.value,NRC:b.value,NIS:C.value,NIF:d.value,NART:k.value,city_id:S.value}),V("update:isDrawerOpen",!1),Q(()=>{var r,W;(r=_.value)==null||r.reset(),(W=_.value)==null||W.resetValidation()}))})},R=p=>{V("update:z",p)};return(p,t)=>(y(),P(de,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":g.isDrawerOpen,"onUpdate:modelValue":R},{default:l(()=>[a("div",Re,[Le,e(le),e(z,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:U},{default:l(()=>[e(B,{size:"18",icon:"tabler-x"})]),_:1})]),e(n(ae),{options:{wheelPropagation:!1}},{default:l(()=>[e(q,{flat:""},{default:l(()=>[e(L,null,{default:l(()=>[e(re,{ref_key:"refForm",ref:_,modelValue:n(x),"onUpdate:modelValue":t[10]||(t[10]=r=>m(x)?x.value=r:null),onSubmit:oe(F,["prevent"])},{default:l(()=>[e(Z,null,{default:l(()=>[e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(v),"onUpdate:modelValue":t[0]||(t[0]=r=>m(v)?v.value=r:null),rules:[n(J)],label:"Name"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(c),"onUpdate:modelValue":t[1]||(t[1]=r=>m(c)?c.value=r:null),label:"Surname"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(s),"onUpdate:modelValue":t[2]||(t[2]=r=>m(s)?s.value=r:null),rules:[n(ie)],label:"Email"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(ue,{clearable:"",modelValue:n(S),"onUpdate:modelValue":t[3]||(t[3]=r=>m(S)?S.value=r:null),items:g.cities,"item-value":"id","item-title":"name",label:"City",rules:[n(J)]},null,8,["modelValue","items","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(f),"onUpdate:modelValue":t[4]||(t[4]=r=>m(f)?f.value=r:null),rules:[n(J)],label:"Address"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(h),"onUpdate:modelValue":t[5]||(t[5]=r=>m(h)?h.value=r:null),label:"Phone"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(b),"onUpdate:modelValue":t[6]||(t[6]=r=>m(b)?b.value=r:null),label:"Num\xE9ro de registre"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(d),"onUpdate:modelValue":t[7]||(t[7]=r=>m(d)?d.value=r:null),label:"Num\xE9ro de NIF"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(C),"onUpdate:modelValue":t[8]||(t[8]=r=>m(C)?C.value=r:null),label:"Num\xE9ro de NIS"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(k),"onUpdate:modelValue":t[9]||(t[9]=r=>m(k)?k.value=r:null),label:"Num\xE9ro de ARTICLE"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(z,{type:"submit",class:"me-3"},{default:l(()=>[I(" Submit ")]),_:1}),e(z,{type:"reset",variant:"tonal",color:"secondary",onClick:U},{default:l(()=>[I(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"]))}},Be={class:"d-flex align-center pa-6 pb-1"},Ee=a("h6",{class:"text-h6"}," Edit Client ",-1),Me={__name:"EditClientDrawer",props:{isDrawerOpen:{type:Boolean,required:!0},client:{type:Object,required:!0},cities:{type:Array,required:!0}},emits:["update:isDrawerOpen","clientData"],setup(O,{emit:V}){const g=O;ee(()=>g.client,p=>{console.log(p),p&&(v.value=p.id||0,c.value=p.name||"",s.value=p.surname||"",f.value=p.email||"",h.value=p.address||"",b.value=p.phone||"",d.value=p.NRC||"",C.value=p.NIF||"",S.value=p.NART||"",k.value=p.NIS||"",U.value=p.city_id||"")});const x=o(!1),_=o(),v=o(),c=o(""),s=o(""),f=o(""),h=o(""),b=o(""),d=o(""),C=o(""),k=o(""),S=o(""),U=o(null);o(""),o(""),o(""),o(),o(),o(!1),o(!1),o(!1),o(),o(),o();const F=()=>{V("update:isDrawerOpen",!1),Q(()=>{var p,t;(p=_.value)==null||p.reset(),(t=_.value)==null||t.resetValidation()})},R=()=>{var p;(p=_.value)==null||p.validate().then(({valid:t})=>{t&&(V("clientData",{id:v.value,name:c.value,surname:s.value,email:f.value,address:h.value,phone:b.value,NRC:d.value,NIS:k.value,NIF:C.value,NART:S.value}),V("update:isDrawerOpen",!1),Q(()=>{var r,W;(r=_.value)==null||r.reset(),(W=_.value)==null||W.resetValidation()}))})};return(p,t)=>(y(),P(de,{temporary:"",width:400,location:"end",class:"scrollable-content","model-value":g.isDrawerOpen},{default:l(()=>[a("div",Be,[Ee,e(le),e(z,{variant:"tonal",color:"default",icon:"",size:"32",class:"rounded",onClick:F},{default:l(()=>[e(B,{size:"18",icon:"tabler-x"})]),_:1})]),e(n(ae),{options:{wheelPropagation:!1}},{default:l(()=>[e(q,{flat:""},{default:l(()=>[e(L,null,{default:l(()=>[e(re,{ref_key:"refForm",ref:_,modelValue:n(x),"onUpdate:modelValue":t[10]||(t[10]=r=>m(x)?x.value=r:null),onSubmit:oe(R,["prevent"])},{default:l(()=>[e(Z,null,{default:l(()=>[e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(c),"onUpdate:modelValue":t[0]||(t[0]=r=>m(c)?c.value=r:null),rules:[n(J)],label:"Name"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(s),"onUpdate:modelValue":t[1]||(t[1]=r=>m(s)?s.value=r:null),label:"Surname"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(f),"onUpdate:modelValue":t[2]||(t[2]=r=>m(f)?f.value=r:null),rules:[n(ie)],label:"Email"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(ue,{clearable:"",modelValue:n(U),"onUpdate:modelValue":t[3]||(t[3]=r=>m(U)?U.value=r:null),items:g.cities,"item-value":"id","item-title":"name",label:"City",rules:[n(J)]},null,8,["modelValue","items","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(h),"onUpdate:modelValue":t[4]||(t[4]=r=>m(h)?h.value=r:null),rules:[n(J)],label:"Address"},null,8,["modelValue","rules"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(b),"onUpdate:modelValue":t[5]||(t[5]=r=>m(b)?b.value=r:null),label:"Phone"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(d),"onUpdate:modelValue":t[6]||(t[6]=r=>m(d)?d.value=r:null),label:"Num\xE9ro de registre"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(C),"onUpdate:modelValue":t[7]||(t[7]=r=>m(C)?C.value=r:null),label:"Num\xE9ro de NIF"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(k),"onUpdate:modelValue":t[8]||(t[8]=r=>m(k)?k.value=r:null),label:"Num\xE9ro de NIS"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(A,{modelValue:n(S),"onUpdate:modelValue":t[9]||(t[9]=r=>m(S)?S.value=r:null),label:"Num\xE9ro de ARTICLE"},null,8,["modelValue"])]),_:1}),e(w,{cols:"12"},{default:l(()=>[e(z,{type:"submit",class:"me-3"},{default:l(()=>[I(" Submit ")]),_:1}),e(z,{type:"reset",variant:"tonal",color:"secondary",onClick:F},{default:l(()=>[I(" Cancel ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue","onSubmit"])]),_:1})]),_:1})]),_:1})]),_:1},8,["model-value"]))}},Je={__name:"ConfirmationDialog",props:{isDialogVisible:{type:Boolean,required:!0},client:{type:Object,required:!0}},emits:["update:isDrawerOpen","userData"],setup(O,{emit:V}){const g=O,x=o(),_=()=>{V("update:isDialogVisible",!1)};ee(()=>g.user,c=>{c&&(x.value=c.id||0)});const v=()=>{V("clientData",{id:g.client.id}),V("update:isDialogVisible",!1)};return(c,s)=>{const f=ne;return y(),P(pe,{modelValue:g.isDialogVisible,"onUpdate:modelValue":s[2]||(s[2]=h=>g.isDialogVisible=h),persistent:"",class:"v-dialog-sm"},{default:l(()=>[e(f,{onClick:s[0]||(s[0]=h=>_())}),e(q,{title:"Confirmation"},{default:l(()=>[e(L,null,{default:l(()=>[I(" Are you sure you want to delete this client ? ")]),_:1}),e(L,{class:"d-flex justify-end gap-3 flex-wrap"},{default:l(()=>[e(z,{color:"secondary",variant:"tonal",onClick:s[1]||(s[1]=h=>V("update:isDialogVisible",!1))},{default:l(()=>[I(" Disagree ")]),_:1}),e(z,{onClick:v},{default:l(()=>[I(" Agree ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])}}},qe=_e("ClientListStore",{actions:{fetchClients(O){return j.get("/api/clients/list",{params:O})},addClient(O){const{name:V,surname:g,email:x,address:_,phone:v,NRC:c,NIF:s,NIS:f,NART:h,city_id:b}=O;return new Promise((d,C)=>{j.post("/api/clients/store",{name:V,surname:g,email:x,address:_,phone:v,NRC:c,NIF:s,NART:h,NIS:f,city_id:b}).then(k=>d(k)).catch(k=>C(k))})},updateClient(O){const{id:V,name:g,surname:x,email:_,address:v,phone:c,NRC:s,NIF:f,NIS:h,NART:b,city_id:d}=O;return new Promise((C,k)=>{let S={name:g,surname:x,email:_,address:v,phone:c,NRC:s,NIF:f,NART:b,NIS:h,city_id:d};j.post("/api/clients/update/"+V,S).then(U=>C(U)).catch(U=>k(U))})},fetchClient(O){return new Promise((V,g)=>{j.get(`/apps/clients/${O}`).then(x=>V(x)).catch(x=>g(x))})},deleteClient(O){console.log(O);const{id:V}=O;return new Promise((g,x)=>{j.delete("/api/clients/delete/"+V).then(_=>g(_)).catch(_=>x(_))})}}}),je={class:"mt-n4 me-n2"},He={__name:"ClientMonthlyReport",setup(O){const V=he(),g=o(0),x=o(),_=se(()=>{const v=V.current.value.colors,c=V.current.value.variables,s=`rgba(${H(v.primary)},${c["dragged-opacity"]})`,f=`rgba(${H(v["on-background"])},${c["high-emphasis-opacity"]})`,h=`rgba(${H(String(c["border-color"]))},${c["border-opacity"]})`,b=`rgba(${H(v["on-surface"])},${c["disabled-opacity"]})`;return[{title:"Orders",icon:"tabler-shopping-cart",chartOptions:{chart:{parentHeightOffset:0,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{columnWidth:"10%",startingShape:"rounded",borderRadius:4,distributed:!0,dataLabels:{position:"top"}}},grid:{show:!1,padding:{top:0,bottom:0,left:-10,right:-10}},colors:[s,s,v.primary,s,s,s,s,s,s],dataLabels:{enabled:!0,formatter(d){return`${d}k`},offsetY:-25,style:{fontSize:"15px",colors:[f],fontWeight:"600",fontFamily:"Public Sans"}},legend:{show:!1},tooltip:{enabled:!1},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep"],axisBorder:{show:!0,color:h},axisTicks:{show:!1},labels:{style:{colors:b,fontSize:"14px",fontFamily:"Public Sans"}}},yaxis:{labels:{offsetX:-15,formatter(d){return`${parseInt(d/1)}k`},style:{fontSize:"14px",colors:b,fontFamily:"Public Sans"},min:0,max:6e4,tickAmount:6}},responsive:[{breakpoint:1441,options:{plotOptions:{bar:{columnWidth:"10%"}}}},{breakpoint:590,options:{plotOptions:{bar:{columnWidth:"10%"}},yaxis:{labels:{show:!1}},grid:{padding:{right:0,left:-20}},dataLabels:{style:{fontSize:"12px",fontWeight:"400"}}}}]},series:[{data:[28,10,45,38,15,30,35,30,8]}]},{title:"Sales",icon:"tabler-chart-bar",chartOptions:{chart:{parentHeightOffset:0,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{columnWidth:"10%",startingShape:"rounded",borderRadius:4,distributed:!0,dataLabels:{position:"top"}}},grid:{show:!1,padding:{top:0,bottom:0,left:-10,right:-10}},colors:[s,s,s,s,s,s,v.primary,s,s],dataLabels:{enabled:!0,formatter(d){return`${d}k`},offsetY:-25,style:{fontSize:"15px",colors:[f],fontWeight:"600",fontFamily:"Public Sans"}},legend:{show:!1},tooltip:{enabled:!1},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep"],axisBorder:{show:!0,color:h},axisTicks:{show:!1},labels:{style:{colors:b,fontSize:"14px",fontFamily:"Public Sans"}}},yaxis:{labels:{offsetX:-15,formatter(d){return`${parseInt(d/1)}k`},style:{fontSize:"14px",colors:b,fontFamily:"Public Sans"},min:0,max:6e4,tickAmount:6}},responsive:[{breakpoint:1441,options:{plotOptions:{bar:{columnWidth:"10%"}}}},{breakpoint:590,options:{plotOptions:{bar:{columnWidth:"10%"}},grid:{padding:{right:0}},dataLabels:{style:{fontSize:"12px",fontWeight:"400"}},yaxis:{labels:{show:!1}}}}]},series:[{data:[35,25,15,40,42,25,48,8,30]}]},{title:"Profit",icon:"tabler-currency-dollar",chartOptions:{chart:{parentHeightOffset:0,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{columnWidth:"10%",startingShape:"rounded",borderRadius:4,distributed:!0,dataLabels:{position:"top"}}},grid:{show:!1,padding:{top:0,bottom:0,left:-10,right:-10}},colors:[s,s,s,s,v.primary,s,s,s,s],dataLabels:{enabled:!0,formatter(d){return`${d}k`},offsetY:-25,style:{fontSize:"15px",colors:[f],fontWeight:"600",fontFamily:"Public Sans"}},legend:{show:!1},tooltip:{enabled:!1},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep"],axisBorder:{show:!0,color:h},axisTicks:{show:!1},labels:{style:{colors:b,fontSize:"14px",fontFamily:"Public Sans"}}},yaxis:{labels:{offsetX:-15,formatter(d){return`${parseInt(d/1)}k`},style:{fontSize:"14px",colors:b,fontFamily:"Public Sans"},min:0,max:6e4,tickAmount:6}},responsive:[{breakpoint:1441,options:{plotOptions:{bar:{columnWidth:"10%"}}}},{breakpoint:590,options:{plotOptions:{bar:{columnWidth:"10%"}},grid:{padding:{right:0}},dataLabels:{style:{fontSize:"12px",fontWeight:"400"}},yaxis:{labels:{show:!1}}}}]},series:[{data:[10,22,27,33,42,32,27,22,8]}]},{title:"Income",icon:"tabler-chart-pie-2",chartOptions:{chart:{parentHeightOffset:0,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{columnWidth:"10%",startingShape:"rounded",borderRadius:4,distributed:!0,dataLabels:{position:"top"}}},grid:{show:!1,padding:{top:0,bottom:0,left:-10,right:-10}},colors:[s,s,s,s,s,s,s,s,v.primary],dataLabels:{enabled:!0,formatter(d){return`${d}k`},offsetY:-25,style:{fontSize:"15px",colors:[f],fontWeight:"600",fontFamily:"Public Sans"}},legend:{show:!1},tooltip:{enabled:!1},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep"],axisBorder:{show:!0,color:h},axisTicks:{show:!1},labels:{style:{colors:b,fontSize:"14px",fontFamily:"Public Sans"}}},yaxis:{labels:{offsetX:-15,formatter(d){return`${parseInt(d/1)}k`},style:{fontSize:"14px",colors:b,fontFamily:"Public Sans"},min:0,max:6e4,tickAmount:6}},responsive:[{breakpoint:1441,options:{plotOptions:{bar:{columnWidth:"10%"}}}},{breakpoint:590,options:{plotOptions:{bar:{columnWidth:"10%"}},dataLabels:{style:{fontSize:"12px",fontWeight:"400"}},grid:{padding:{right:0}},yaxis:{labels:{show:!1}}}}]},series:[{data:[5,9,12,18,20,25,30,36,48]}]}]});return(v,c)=>(y(),P(q,{title:"Earning Reports",subtitle:"Yearly Earnings Overview"},{append:l(()=>[a("div",je,[e(z,{icon:"",size:"x-small",variant:"plain",color:"default"},{default:l(()=>[e(B,{size:"22",icon:"tabler-dots-vertical"}),e(ke,{activator:"parent"},{default:l(()=>[e(De,null,{default:l(()=>[(y(),$(E,null,M(["View More","Delete"],(s,f)=>e(Ne,{key:f,value:f},{default:l(()=>[e(Ae,null,{default:l(()=>[I(N(s),1)]),_:2},1024)]),_:2},1032,["value"])),64))]),_:1})]),_:1})]),_:1})])]),default:l(()=>[e(L,null,{default:l(()=>[(y(),P(n(Se),{ref_key:"refVueApexChart",ref:x,key:n(g),options:n(_)[Number(n(g))].chartOptions,series:n(_)[Number(n(g))].series,class:"mt-3"},null,8,["options","series"]))]),_:1})]),_:1}))}};const Ye={class:"me-3",style:{width:"80px"}},Xe={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},Qe={style:{width:"9rem"}},Ze=a("thead",null,[a("tr",null,[a("th",{scope:"col"}," ID "),a("th",{scope:"col"}," Client "),a("th",{scope:"col"}," City "),a("th",{scope:"col"}," Phone "),a("th",{scope:"col"}," ACTIONS ")])],-1),Ge={class:"d-flex align-center"},Ke={key:1},el={class:"d-flex flex-column"},ll={class:"text-base"},tl={class:"text-sm text-disabled"},al={class:"text-capitalize text-base font-weight-semibold"},ol={class:"text-capitalize text-base font-weight-semibold"},sl={class:"text-center",style:{width:"5rem"}},nl=a("tr",null,[a("td",{colspan:"7",class:"text-center"}," No data available ")],-1),il=[nl],rl={class:"text-sm text-disabled"},ul=a("thead",null,[a("tr",null,[a("th",{class:"text-caption"}," Product "),a("th",{class:"text-caption"}," Date "),a("th",{class:"text-caption"}," Quantity "),a("th",{class:"text-caption"}," Price "),a("th",{class:"text-caption"}," Total ")])],-1),dl={class:"text-caption"},pl={class:"text-caption"},ml={class:"text-caption"},cl={class:"text-caption"},fl={class:"text-caption"},bl=a("td",null,null,-1),vl=a("td",null,null,-1),Vl=a("td",null,null,-1),gl=a("td",{class:"text-caption"}," Total ",-1),_l={class:"text-caption"},hl=a("td",null,null,-1),yl=a("td",null,null,-1),xl=a("td",null,null,-1),wl=a("td",{class:"text-caption"}," Payment ",-1),Cl={class:"text-caption"},Sl=a("td",null,null,-1),kl=a("td",null,null,-1),Dl=a("td",null,null,-1),Nl=a("td",{class:"text-caption"}," Rest ",-1),Al={class:"text-caption"},Ol=a("thead",null,[a("tr",null,[a("th",null," Date "),a("th",null," Amount ")])],-1),yt={__name:"index",setup(O){const V=qe(),g=o(""),x=o(!1),_=o(!0),v=o(!1);o(),o(),o();const c=o(10),s=o(1),f=o(1),h=o(0),b=o([]);let d=o([]),C=o("Appetizers"),k=o({isActive:!1});const S=[{id:1,name:"Home"},{id:2,name:"Record"},{id:3,name:"Payments"},{id:4,name:"State"}],U=()=>{k.value.isActive=!0,V.fetchClients({searchValue:g.value,perPage:c.value,currentPage:s.value}).then(D=>{k.value.isActive=!1,d.value=D.data.clients.data,console.log(d.value),f.value=D.data.totalPage,b.value=D.data.cities,h.value=D.data.totalClients,x.value=!1}).catch(D=>{console.error(D),k.value.isActive=!1})};G(U),G(()=>{s.value>f.value&&(s.value=f.value)}),ee(g,()=>{U()});const F=o(!1),R=o(!1),p=o(!1);let t=o();G(()=>{s.value>f.value&&(s.value=f.value)});const r=se(()=>{const D=d.value.length?(s.value-1)*c.value+1:0,u=d.value.length+(s.value-1)*c.value;return`Showing ${D} to ${u} of ${h.value} entries`}),W=D=>{V.addClient(D).then(u=>{U()}).catch(u=>{console.log(u)})},me=D=>{V.updateClient(D).then(u=>{U()}).catch(u=>{console.log(u)})},ce=D=>{V.deleteClient(D).then(u=>{U()}).catch(u=>{console.log(u)})},fe=D=>{R.value=!0,t=D},be=D=>{p.value=!0,t=D},ve=D=>{v.value=!0,t=D};return(D,u)=>{const Ve=ye("RouterLink"),ge=ne;return y(),$("section",null,[e(Z,null,{default:l(()=>[e(Oe,{"model-value":n(k).isActive,class:"align-center justify-center"},{default:l(()=>[e(te,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),e(w,{cols:"12"},{default:l(()=>[e(q,{title:"Clients"},{default:l(()=>[e(Y),e(L,{class:"d-flex flex-wrap py-4 gap-4"},{default:l(()=>[a("div",Ye,[e(Ue,{modelValue:n(c),"onUpdate:modelValue":u[0]||(u[0]=i=>m(c)?c.value=i:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),e(le),a("div",Xe,[a("div",Qe,[n(x)?(y(),P(te,{key:0,indeterminate:"",color:"primary"})):(y(),P(A,{key:1,onInput:u[1]||(u[1]=i=>_.value=!0),ref:"searchField",modelValue:n(g),"onUpdate:modelValue":u[2]||(u[2]=i=>m(g)?g.value=i:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),e(z,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:l(()=>[I(" Export ")]),_:1}),e(z,{"prepend-icon":"tabler-plus",onClick:u[3]||(u[3]=i=>F.value=!0)},{default:l(()=>[I(" Add New Client ")]),_:1})])]),_:1}),e(Y),e(K,{class:"text-no-wrap"},{default:l(()=>[Ze,a("tbody",null,[(y(!0),$(E,null,M(n(d),i=>(y(),$("tr",{key:i.id,style:{height:"3.75rem"}},[a("td",null,N(i.id),1),a("td",null,[a("div",Ge,[e(ze,{variant:"tonal",class:"me-3",size:"38"},{default:l(()=>[i.avatar?(y(),P(Pe,{key:0,src:i.avatar},null,8,["src"])):(y(),$("span",Ke,N(i.name.toUpperCase().charAt(0)),1))]),_:2},1024),a("div",el,[a("h6",ll,[e(Ve,{to:{name:"apps-user-view-id",params:{id:i.id}},class:"font-weight-medium user-list-name"},{default:l(()=>[I(N(i.name),1)]),_:2},1032,["to"])]),a("span",tl,"@"+N(i.email),1)])])]),a("td",null,[a("span",al,N(i.city.name),1)]),a("td",null,[a("span",ol,N(i.phone),1)]),a("td",sl,[e(z,{icon:"",size:"x-small",color:"default",variant:"text",onClick:T=>ve(i)},{default:l(()=>[e(B,{size:"22",icon:"tabler-shopping-cart"})]),_:2},1032,["onClick"]),e(z,{icon:"",size:"x-small",color:"default",variant:"text",onClick:T=>fe(i)},{default:l(()=>[e(B,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"]),e(z,{icon:"",size:"x-small",color:"default",variant:"text",onClick:T=>be(i)},{default:l(()=>[e(B,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),e(z,{icon:"",size:"x-small",color:"default",variant:"text"},{default:l(()=>[e(B,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),xe(a("tfoot",null,il,512),[[we,!n(d).length]])]),_:1}),e(Y),e(L,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:l(()=>[a("span",rl,N(n(r)),1),e(Ie,{modelValue:n(s),"onUpdate:modelValue":u[4]||(u[4]=i=>m(s)?s.value=i:null),size:"small","total-visible":5,length:n(f)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),e(pe,{modelValue:n(v),"onUpdate:modelValue":u[9]||(u[9]=i=>m(v)?v.value=i:null),scrollable:"",class:"v-dialog-sm","max-width":"700",width:"auto","content-class":"scrollable-dialog"},{default:l(()=>[e(ge,{onClick:u[5]||(u[5]=i=>v.value=!n(v))}),e(q,{title:" "},{default:l(()=>[e(L,{class:"d-flex justify-end gap-3 flex-wrap"},{default:l(()=>[e($e,{modelValue:n(C),"onUpdate:modelValue":u[6]||(u[6]=i=>m(C)?C.value=i:C=i),grow:""},{default:l(()=>[(y(),$(E,null,M(S,i=>e(Fe,{key:i.id,value:i.id},{default:l(()=>[I(N(i.name),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),e(Y),e(Te,{modelValue:n(C),"onUpdate:modelValue":u[7]||(u[7]=i=>m(C)?C.value=i:C=i),class:"mt-6 pl-3 pa-1"},{default:l(()=>[(y(),P(X,{key:S[0].id,value:S[0].id,width:"150%"},{default:l(()=>[e(Z,null,{default:l(()=>[e(w,{cols:"6",md:"6"},{default:l(()=>[e(He)]),_:1})]),_:1})]),_:1},8,["value"])),(y(),P(X,{key:S[1].id,value:S[1].id,class:"mt-0",style:{"margin-top":"0 !important"}},{default:l(()=>[(y(!0),$(E,null,M(n(t).sales,i=>(y(),P(K,{class:"text-no-wrap mt-0 mb-10"},{default:l(()=>[ul,a("tbody",null,[(y(!0),$(E,null,M(i.sale_items,T=>(y(),$("tr",{key:T.id},[a("td",dl,N(T.product.name),1),a("td",pl,N(T.sale_date),1),a("td",ml,N(T.quantity),1),a("td",cl,N(T.price),1),a("td",fl,N(T.total_price),1)]))),128)),a("tr",null,[bl,vl,Vl,gl,a("td",_l,N(i.total_amount),1)]),a("tr",null,[hl,yl,xl,wl,a("td",Cl,N(i.regulation),1)]),a("tr",null,[Sl,kl,Dl,Nl,a("td",Al,N(i.balance),1)])])]),_:2},1024))),256))]),_:1},8,["value"])),(y(),P(X,{key:S[2].id,value:S[2].id,class:"mt-0",style:{width:"-webkit-fill-available"},width:"100%"},{default:l(()=>[e(K,{class:"text-no-wrap mt-0",style:{width:"-webkit-fill-available"}},{default:l(()=>[Ol,a("tbody",null,[(y(!0),$(E,null,M(n(t).payments,i=>(y(),$("tr",{key:i.id},[a("td",null,N(i.payment_date),1),a("td",null,N(i.amount_paid),1)]))),128))])]),_:1})]),_:1},8,["value"])),(y(),P(X,{key:S[3].id,value:S[3].id},null,8,["value"]))]),_:1},8,["modelValue"]),e(z,{onClick:u[8]||(u[8]=i=>v.value=!1)},{default:l(()=>[I(" Close ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(We,{isDrawerOpen:n(F),"onUpdate:isDrawerOpen":u[10]||(u[10]=i=>m(F)?F.value=i:null),cities:n(b),"onUpdate:cities":u[11]||(u[11]=i=>m(b)?b.value=i:null),onClientData:W},null,8,["isDrawerOpen","cities"]),e(Me,{isDrawerOpen:n(R),"onUpdate:isDrawerOpen":u[12]||(u[12]=i=>m(R)?R.value=i:null),client:n(t),"onUpdate:client":u[13]||(u[13]=i=>m(t)?t.value=i:t=i),cities:n(b),"onUpdate:cities":u[14]||(u[14]=i=>m(b)?b.value=i:null),onClientData:me},null,8,["isDrawerOpen","client","cities"]),n(p)?(y(),P(Je,{key:0,isDialogVisible:n(p),"onUpdate:isDialogVisible":u[15]||(u[15]=i=>m(p)?p.value=i:null),client:n(t),"onUpdate:client":u[16]||(u[16]=i=>m(t)?t.value=i:t=i),onClientData:ce},null,8,["isDialogVisible","client"])):Ce("",!0)])}}};export{yt as default};