import{k as i,$ as k,Z as L,A as Y,o as D,c as z,p as a,w as n,q as o,m as l,D as d,b as P,E as h,F as G,a as J,x as c,C,H as K,I as W}from"./main.cf9f31a9.js";import{_ as X}from"./DialogCloseBtn.1c4165b0.js";import{_ as ee,a as te}from"./EditBenefitDrawer.656214c1.js";import{s as ae}from"./successMiddleware.8a77ff6b.js";import{e as le}from"./errorsMiddleware.ca71b8e1.js";import{u as oe}from"./useSaleStore.04f1c1ac.js";import{a as se}from"./VOverlay.9d9650d6.js";import{a as T,V as m}from"./VBtn.d73656ae.js";import{a as ie,V as ne}from"./VRow.a394feb3.js";import{V as $,c as A}from"./VCard.a1247ba2.js";import{V as U}from"./VDivider.01acf602.js";import{V as re}from"./VSelect.32c4b2dd.js";import{V as de}from"./VSpacer.6ec948ae.js";import{V as ue}from"./VTextField.9102871e.js";import{V as pe}from"./VTable.60781dfc.js";import{V as me}from"./VPagination.7fcfaf73.js";import{V as ce}from"./VDialog.ac89ffbe.js";/* empty css                        */import"./VForm.f1d9c3ac.js";import"./VInput.d6b4ca43.js";import"./router.1658937d.js";import"./index.d1f0132a.js";import"./VImg.134af49b.js";import"./forwardRefs.c003b6b8.js";import"./VAutocomplete.dab6d47c.js";import"./filter.73bf6f77.js";import"./VList.a34172e8.js";import"./VAvatar.9270a597.js";import"./VMenu.3df9bfa4.js";import"./scopeId.08611610.js";import"./dialog-transition.b0009893.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.b3570faf.js";import"./VSelectionControl.5a5e7888.js";import"./VChip.6c2e2c44.js";import"./VNavigationDrawer.2d863e08.js";import"./ssrBoot.e94d367d.js";import"./lazy.f959659a.js";import"./position.b360a9d1.js";/* empty css                   */import"./VField.6a2ec846.js";import"./VCounter.4f92d6c5.js";const fe={class:"me-3",style:{width:"80px"}},ve={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},ge={style:{width:"9rem"}},xe=l("thead",null,[l("tr",null,[l("th",{scope:"col"}," ID "),l("th",{scope:"col"}," MONTH/YEAR "),l("th",{scope:"col"}," AMOUNT "),l("th",{scope:"col"}," NET BENEFIT "),l("th",{scope:"col"}," Quantity "),l("th",{scope:"col"}," ACTIONS ")])],-1),Ve={class:"text-capitalize text-base font-weight-semibold"},_e={class:"text-capitalize text-base font-weight-semibold"},we={class:"text-capitalize text-base font-weight-semibold"},ye={class:"text-capitalize text-base font-weight-semibold"},De={class:"text-center",style:{width:"5rem"}},he=l("tr",null,[l("td",{colspan:"7",class:"text-center"}," No data available ")],-1),be=[he],Be={class:"text-sm text-disabled"},vt={__name:"index",setup(Ce){const N=oe(),V=i(""),O=i(!1),r=i({isActive:!0}),E=i(!0);i(),i(),i();const f=i(10),u=i(1),v=i(1),S=i(0);let p=i([]),g=i([]),_=i([]);i([]);const w=()=>{r.value.isActive=!0,N.fetchBenefits({searchValue:V.value,perPage:f.value,currentPage:u.value}).then(s=>{p.value=s.data.benefits.data,g.value=s.data.months,_.value=s.data.years,v.value=s.data.totalPage,S.value=s.data.totalBenefits,O.value=!1,r.value.isActive=!1}).catch(s=>{console.error(s),r.value.isActive=!1})};k(w),k(()=>{u.value>v.value&&(u.value=v.value)}),L(V,()=>{w()});const b=i({open:!1}),B=i({open:!1}),y=i({open:!1}),x=i();k(()=>{u.value>v.value&&(u.value=v.value)});const I=Y(()=>{const s=p.value.length?(u.value-1)*f.value+1:0,t=p.value.length+(u.value-1)*f.value;return`Showing ${s} to ${t} of ${S.value} entries`}),M=s=>{w()},j=s=>{w()},R=s=>{x.value=s,B.value.open=!0},Z=s=>{y.value.open=!0,x.value=s},F=()=>{y.value.open=!1},H=()=>{r.value.isActive=!0,y.value.open=!1,N.deleteBenefit(x.value.id).then(s=>{r.value.isActive=!1,ae(s.data.message),w()}).catch(s=>{le(s.data.message),r.value.isActive=!1})};return(s,t)=>{const Q=X;return D(),z("section",null,[a(ne,null,{default:n(()=>[a(se,{"model-value":o(r).isActive,class:"align-center justify-center"},{default:n(()=>[a(T,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),a(ie,{cols:"12"},{default:n(()=>[a($,{title:"Benefits"},{default:n(()=>[a(U),a(A,{class:"d-flex flex-wrap py-4 gap-4"},{default:n(()=>[l("div",fe,[a(re,{modelValue:o(f),"onUpdate:modelValue":t[0]||(t[0]=e=>d(f)?f.value=e:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),a(de),l("div",ve,[l("div",ge,[o(O)?(D(),P(T,{key:0,indeterminate:"",color:"primary"})):(D(),P(ue,{key:1,onInput:t[1]||(t[1]=e=>E.value=!0),ref:"searchField",modelValue:o(V),"onUpdate:modelValue":t[2]||(t[2]=e=>d(V)?V.value=e:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),a(m,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:n(()=>[h(" Export ")]),_:1}),a(m,{"prepend-icon":"tabler-plus",onClick:t[3]||(t[3]=e=>o(b).open=!0)},{default:n(()=>[h(" Add Benefit ")]),_:1})])]),_:1}),a(U),a(pe,{class:"text-no-wrap"},{default:n(()=>[xe,l("tbody",null,[(D(!0),z(G,null,J(o(p),e=>(D(),z("tr",{key:e.id,style:{height:"3.75rem"}},[l("td",null,c(e.id),1),l("td",null,[l("span",Ve,c(o(g)[e.month-1].name)+" / "+c(e.year),1)]),l("td",null,[l("span",_e,c(parseFloat(e.total_amount).toFixed(2))+" DZD",1)]),l("td",null,[l("span",we,c(parseFloat(e.netBenefit).toFixed(2))+" DZD",1)]),l("td",null,[l("span",ye,c(parseFloat(e.total_articles)),1)]),l("td",De,[a(m,{icon:"",size:"x-small",color:"default",variant:"text",onClick:q=>R(e)},{default:n(()=>[a(C,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"]),a(m,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-POS-benefit-edit-id",params:{id:e.id}}},{default:n(()=>[a(C,{size:"22",icon:"tabler-currency-dollar"})]),_:2},1032,["to"]),a(m,{icon:"",size:"x-small",color:"default",variant:"text",onClick:q=>Z(e)},{default:n(()=>[a(C,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),a(m,{icon:"",size:"x-small",color:"default",variant:"text"},{default:n(()=>[a(C,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),K(l("tfoot",null,be,512),[[W,!o(p).length]])]),_:1}),a(U),a(A,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:n(()=>[l("span",Be,c(o(I)),1),a(me,{modelValue:o(u),"onUpdate:modelValue":t[4]||(t[4]=e=>d(u)?u.value=e:null),size:"small","total-visible":5,length:o(v)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),a(ee,{isDrawerOpen:o(b),"onUpdate:isDrawerOpen":t[5]||(t[5]=e=>d(b)?b.value=e:null),months:o(g),"onUpdate:months":t[6]||(t[6]=e=>d(g)?g.value=e:g=e),years:o(_),"onUpdate:years":t[7]||(t[7]=e=>d(_)?_.value=e:_=e),benefit:o(p),"onUpdate:benefit":t[8]||(t[8]=e=>d(p)?p.value=e:p=e),loading:o(r),"onUpdate:loading":t[9]||(t[9]=e=>d(r)?r.value=e:null),onBenefitData:M},null,8,["isDrawerOpen","months","years","benefit","loading"]),a(te,{isDrawerOpen:o(B),"onUpdate:isDrawerOpen":t[10]||(t[10]=e=>d(B)?B.value=e:null),benefit:o(x),"onUpdate:benefit":t[11]||(t[11]=e=>d(x)?x.value=e:null),loading:o(r),"onUpdate:loading":t[12]||(t[12]=e=>d(r)?r.value=e:null),onBenefitData:j},null,8,["isDrawerOpen","benefit","loading"]),a(ce,{modelValue:o(y).open,"onUpdate:modelValue":t[15]||(t[15]=e=>o(y).open=e),persistent:"",class:"v-dialog-sm"},{default:n(()=>[a(Q,{onClick:t[13]||(t[13]=e=>F())}),a($,{title:"Confirmation"},{default:n(()=>[a(A,null,{default:n(()=>[h(" Are you sure you want to delete this record ? ")]),_:1}),a(A,{class:"d-flex justify-end gap-3 flex-wrap"},{default:n(()=>[a(m,{color:"secondary",variant:"tonal",onClick:t[14]||(t[14]=e=>F())},{default:n(()=>[h(" Disagree ")]),_:1}),a(m,{onClick:H},{default:n(()=>[h(" Agree ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])])}}};export{vt as default};