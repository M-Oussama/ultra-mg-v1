import{bd as V,k as n,q as l,o as p,b,w as o,p as t,D as u,c as k,a as v,C as w,m as y,x as B,F as U,y as g}from"./main.60095230.js";import{u as C}from"./useUserListStore.aa3e5cff.js";import{U as N,_ as $,a as x,b as S,c as D,d as L}from"./UserTabSecurity.fd600d79.js";import{a as c,V as R}from"./VRow.8955dee0.js";import{V as T,a as h}from"./VTabs.1e48a5b5.js";import{V as F,a}from"./VWindowItem.f2995b37.js";import"./DialogCloseBtn.2ff0cdff.js";import"./VBtn.5ecb9490.js";import"./router.717d3535.js";import"./position.e6185ce0.js";import"./VCard.b8af7deb.js";import"./VAvatar.ad95b801.js";import"./VImg.cdaed533.js";import"./VForm.ec185ea1.js";import"./VInput.c62758ee.js";import"./index.7d0b373a.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.9b7b3347.js";/* empty css                   */import"./VField.e7600162.js";import"./easing.36b781ab.js";import"./VCounter.5d1d4da1.js";import"./VSelect.4cefa2aa.js";import"./VList.49cad3e0.js";import"./VDivider.f2016316.js";import"./dialog-transition.4295e999.js";import"./VMenu.5abb403b.js";import"./scopeId.0ada01c1.js";import"./VOverlay.07c5d8cd.js";import"./lazy.e4a387c0.js";import"./VCheckboxBtn.248e09d9.js";import"./VSelectionControl.5c498ebb.js";import"./VChip.64738807.js";import"./VSwitch.6912ca31.js";import"./VDialog.6e21af97.js";import"./formatters.4acf44a4.js";import"./index.b522f886.js";import"./VSpacer.fd46eca3.js";import"./VTextarea.07126904.js";import"./EnableOneTimePasswordDialog.0909b43c.js";import"./VAlert.ad8ba9df.js";import"./VTable.c64d737f.js";import"./VCheckbox.846aa4cb.js";import"./useInvoiceStore.9bb0fce1.js";import"./VTooltip.bb425ebd.js";import"./VPagination.58915958.js";import"./xamarin.9738782b.js";import"./VTimelineItem.2bef8cb7.js";import"./VSlideGroup.e21457b7.js";import"./ssrBoot.2c77fa9a.js";const Tt={__name:"[id]",setup(I){const d=C(),f=V(),m=n(),r=n(null),_=[{icon:"tabler-user-check",title:"Overview"},{icon:"tabler-lock",title:"Security"},{icon:"tabler-currency-dollar",title:"Billing & Plan"},{icon:"tabler-bell",title:"Notifications"},{icon:"tabler-link",title:"Connections"}];return d.fetchUser(Number(f.params.id)).then(s=>{m.value=s.data}),(s,i)=>l(m)?(p(),b(R,{key:0},{default:o(()=>[t(c,{cols:"12",md:"5",lg:"4"},{default:o(()=>[t(N,{"user-data":l(m)},null,8,["user-data"])]),_:1}),t(c,{cols:"12",md:"7",lg:"8"},{default:o(()=>[t(T,{modelValue:l(r),"onUpdate:modelValue":i[0]||(i[0]=e=>u(r)?r.value=e:null),class:"v-tabs-pill"},{default:o(()=>[(p(),k(U,null,v(_,e=>t(h,{key:e.icon},{default:o(()=>[t(w,{size:18,icon:e.icon,class:"me-1"},null,8,["icon"]),y("span",null,B(e.title),1)]),_:2},1024)),64))]),_:1},8,["modelValue"]),t(F,{modelValue:l(r),"onUpdate:modelValue":i[1]||(i[1]=e=>u(r)?r.value=e:null),class:"mt-6 disable-tab-transition",touch:!1},{default:o(()=>[t(a,null,{default:o(()=>[t($)]),_:1}),t(a,null,{default:o(()=>[t(x)]),_:1}),t(a,null,{default:o(()=>[t(S)]),_:1}),t(a,null,{default:o(()=>[t(D)]),_:1}),t(a,null,{default:o(()=>[t(L)]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})):g("",!0)}};export{Tt as default};