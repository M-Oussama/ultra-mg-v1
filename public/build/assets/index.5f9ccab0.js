import{k as i,$ as k,Z as L,A as G,o as h,c as z,p as a,w as n,q as o,m as l,D as d,b as S,E as D,F as J,a as K,x as m,C,H as W,I as X,bf as T}from"./main.ccc6aae0.js";import{_ as ee}from"./DialogCloseBtn.eb9ea561.js";import{_ as te,a as ae}from"./EditBenefitDrawer.4b67ade5.js";import{s as le}from"./successMiddleware.53303d8d.js";import{e as oe}from"./errorsMiddleware.ca8f0215.js";import{u as se}from"./useSaleStore.1640952e.js";import{a as ie}from"./VOverlay.0ad0cca7.js";import{a as E,V as c}from"./VBtn.7efa8e0f.js";import{a as ne,V as re}from"./VRow.a17ab33e.js";import{V as $,c as A}from"./VCard.36c7adfd.js";import{V as U}from"./VDivider.09896c6c.js";import{V as de}from"./VSelect.b041d3dc.js";import{V as ue}from"./VSpacer.fc3a499d.js";import{V as pe}from"./VTextField.8db2e7e8.js";import{V as me}from"./VTable.484ee967.js";import{V as ce}from"./VPagination.ced5ec2a.js";import{V as fe}from"./VDialog.11663ea2.js";/* empty css                        */import"./VForm.8ba2a0de.js";import"./VInput.1586f7cc.js";import"./router.25b57325.js";import"./index.720d3b34.js";import"./VImg.e193e870.js";import"./forwardRefs.c003b6b8.js";import"./VAutocomplete.5bdf49b5.js";import"./filter.7282d4f9.js";import"./VList.54d6dfac.js";import"./VAvatar.cb7dc9a5.js";import"./VMenu.01138ff3.js";import"./scopeId.1409e92e.js";import"./dialog-transition.e2b69798.js";import"./easing.36b781ab.js";import"./VCheckboxBtn.cfb39e43.js";import"./VSelectionControl.e824dbee.js";import"./VChip.768b9a43.js";import"./VNavigationDrawer.df836fef.js";import"./ssrBoot.81f110ad.js";import"./lazy.ab6dcefa.js";import"./position.ea536dab.js";/* empty css                   */import"./VField.8b77e1e2.js";import"./VCounter.d2fed1b1.js";const ve={class:"me-3",style:{width:"80px"}},ge={class:"app-user-search-filter d-flex align-center flex-wrap gap-4"},xe={style:{width:"9rem"}},Ve=l("thead",null,[l("tr",null,[l("th",{scope:"col"}," ID "),l("th",{scope:"col"}," MONTH/YEAR "),l("th",{scope:"col"}," AMOUNT "),l("th",{scope:"col"}," PAYMENT "),l("th",{scope:"col"}," NET BENEFIT "),l("th",{scope:"col"}," Quantity "),l("th",{scope:"col"}," ACTIONS ")])],-1),_e={class:"text-capitalize text-base font-weight-semibold"},ye={class:"text-capitalize text-base font-weight-semibold"},we={class:"text-capitalize text-base font-weight-semibold"},he={class:"text-capitalize text-base font-weight-semibold"},De={class:"text-capitalize text-base font-weight-semibold"},be={class:"text-center",style:{width:"5rem"}},Be=l("tr",null,[l("td",{colspan:"7",class:"text-center"}," No data available ")],-1),Ce=[Be],Ae={class:"text-sm text-disabled"},ke={__name:"index",setup(ze){const P=se(),V=i(""),F=i(!1),r=i({isActive:!0}),I=i(!0);i(),i(),i();const f=i(10),u=i(1),v=i(1),N=i(0);let p=i([]),g=i([]),_=i([]);i([]);const y=()=>{r.value.isActive=!0,P.fetchBenefits({searchValue:V.value,perPage:f.value,currentPage:u.value}).then(s=>{p.value=s.data.benefits.data,g.value=s.data.months,_.value=s.data.years,v.value=s.data.totalPage,N.value=s.data.totalBenefits,F.value=!1,r.value.isActive=!1}).catch(s=>{console.error(s),r.value.isActive=!1})};k(y),k(()=>{u.value>v.value&&(u.value=v.value)}),L(V,()=>{y()});const b=i({open:!1}),B=i({open:!1}),w=i({open:!1}),x=i();k(()=>{u.value>v.value&&(u.value=v.value)});const M=G(()=>{const s=p.value.length?(u.value-1)*f.value+1:0,t=p.value.length+(u.value-1)*f.value;return`Showing ${s} to ${t} of ${N.value} entries`}),j=s=>{y()},R=s=>{y()},Z=s=>{x.value=s,B.value.open=!0},H=s=>{w.value.open=!0,x.value=s},O=()=>{w.value.open=!1},Q=()=>{r.value.isActive=!0,w.value.open=!1,P.deleteBenefit(x.value.id).then(s=>{r.value.isActive=!1,le(s.data.message),y()}).catch(s=>{oe(s.data.message),r.value.isActive=!1})};return(s,t)=>{const Y=ee;return h(),z("section",null,[a(re,null,{default:n(()=>[a(ie,{"model-value":o(r).isActive,class:"align-center justify-center"},{default:n(()=>[a(E,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),a(ne,{cols:"12"},{default:n(()=>[a($,{title:"Benefits"},{default:n(()=>[a(U),a(A,{class:"d-flex flex-wrap py-4 gap-4"},{default:n(()=>[l("div",ve,[a(de,{modelValue:o(f),"onUpdate:modelValue":t[0]||(t[0]=e=>d(f)?f.value=e:null),density:"compact",variant:"outlined",items:[10,20,30,50]},null,8,["modelValue"])]),a(ue),l("div",ge,[l("div",xe,[o(F)?(h(),S(E,{key:0,indeterminate:"",color:"primary"})):(h(),S(pe,{key:1,onInput:t[1]||(t[1]=e=>I.value=!0),ref:"searchField",modelValue:o(V),"onUpdate:modelValue":t[2]||(t[2]=e=>d(V)?V.value=e:null),placeholder:"Search",density:"compact"},null,8,["modelValue"]))]),a(c,{variant:"tonal",color:"secondary","prepend-icon":"tabler-screen-share"},{default:n(()=>[D(" Export ")]),_:1}),a(c,{"prepend-icon":"tabler-plus",onClick:t[3]||(t[3]=e=>o(b).open=!0)},{default:n(()=>[D(" Add Benefit ")]),_:1})])]),_:1}),a(U),a(me,{class:"text-no-wrap"},{default:n(()=>[Ve,l("tbody",null,[(h(!0),z(J,null,K(o(p),e=>(h(),z("tr",{key:e.id,style:{height:"3.75rem"}},[l("td",null,m(e.id),1),l("td",null,[l("span",_e,m(o(g)[e.month-1].name)+" / "+m(e.year),1)]),l("td",null,[l("span",ye,m(parseFloat(e.total_amount).toFixed(2)),1)]),l("td",null,[l("span",we,m(e.totalPayments?parseFloat(e.totalPayments).toFixed(2):0)+" DZD",1)]),l("td",null,[l("span",he,m(parseFloat(e.netBenefit).toFixed(2))+" DZD",1)]),l("td",null,[l("span",De,m(parseFloat(e.total_articles)),1)]),l("td",be,[a(c,{icon:"",size:"x-small",color:"default",variant:"text",onClick:q=>Z(e)},{default:n(()=>[a(C,{size:"22",icon:"tabler-edit"})]),_:2},1032,["onClick"]),a(c,{icon:"",size:"x-small",color:"default",variant:"text",to:{name:"apps-POS-benefitUltra-edit-id",params:{id:e.id}}},{default:n(()=>[a(C,{size:"22",icon:"tabler-currency-dollar"})]),_:2},1032,["to"]),a(c,{icon:"",size:"x-small",color:"default",variant:"text",onClick:q=>H(e)},{default:n(()=>[a(C,{size:"22",icon:"tabler-trash"})]),_:2},1032,["onClick"]),a(c,{icon:"",size:"x-small",color:"default",variant:"text"},{default:n(()=>[a(C,{size:"22",icon:"tabler-dots-vertical"})]),_:1})])]))),128))]),W(l("tfoot",null,Ce,512),[[X,!o(p).length]])]),_:1}),a(U),a(A,{class:"d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5"},{default:n(()=>[l("span",Ae,m(o(M)),1),a(ce,{modelValue:o(u),"onUpdate:modelValue":t[4]||(t[4]=e=>d(u)?u.value=e:null),size:"small","total-visible":5,length:o(v)},null,8,["modelValue","length"])]),_:1})]),_:1})]),_:1})]),_:1}),a(te,{isDrawerOpen:o(b),"onUpdate:isDrawerOpen":t[5]||(t[5]=e=>d(b)?b.value=e:null),months:o(g),"onUpdate:months":t[6]||(t[6]=e=>d(g)?g.value=e:g=e),years:o(_),"onUpdate:years":t[7]||(t[7]=e=>d(_)?_.value=e:_=e),benefit:o(p),"onUpdate:benefit":t[8]||(t[8]=e=>d(p)?p.value=e:p=e),loading:o(r),"onUpdate:loading":t[9]||(t[9]=e=>d(r)?r.value=e:null),onBenefitData:j},null,8,["isDrawerOpen","months","years","benefit","loading"]),a(ae,{isDrawerOpen:o(B),"onUpdate:isDrawerOpen":t[10]||(t[10]=e=>d(B)?B.value=e:null),benefit:o(x),"onUpdate:benefit":t[11]||(t[11]=e=>d(x)?x.value=e:null),loading:o(r),"onUpdate:loading":t[12]||(t[12]=e=>d(r)?r.value=e:null),onBenefitData:R},null,8,["isDrawerOpen","benefit","loading"]),a(fe,{modelValue:o(w).open,"onUpdate:modelValue":t[15]||(t[15]=e=>o(w).open=e),persistent:"",class:"v-dialog-sm"},{default:n(()=>[a(Y,{onClick:t[13]||(t[13]=e=>O())}),a($,{title:"Confirmation"},{default:n(()=>[a(A,null,{default:n(()=>[D(" Are you sure you want to delete this record ? ")]),_:1}),a(A,{class:"d-flex justify-end gap-3 flex-wrap"},{default:n(()=>[a(c,{color:"secondary",variant:"tonal",onClick:t[14]||(t[14]=e=>O())},{default:n(()=>[D(" Disagree ")]),_:1}),a(c,{onClick:Q},{default:n(()=>[D(" Agree ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])])}}};typeof T=="function"&&T(ke);export{ke as default};