import{bd as V,k as n,q as l,o as p,b,w as o,p as t,D as u,c as k,a as v,C as w,m as y,x as B,F as U,y as g}from"./main.e3e9927b.js";import{u as C}from"./useUserListStore.d5bc17d3.js";import{U as N,_ as $,a as x,b as S,c as D,d as L}from"./UserTabSecurity.f7d8c152.js";import{a as c,V as R}from"./VRow.fa44e1fc.js";import{V as T,a as h}from"./VTabs.907b383e.js";import{V as F,a}from"./VWindowItem.386e8a17.js";import"./DialogCloseBtn.76a96468.js";import"./VBtn.eec2e34a.js";import"./router.f7f5a52c.js";import"./position.e1779594.js";import"./VCard.2d51fc32.js";import"./VAvatar.3716d284.js";import"./VImg.5147d623.js";import"./VForm.eb6e8a91.js";import"./VInput.4e88dd00.js";import"./index.73872f79.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.b40b8fac.js";/* empty css                   */import"./VField.1a330e06.js";import"./easing.36b781ab.js";import"./VCounter.01cc504b.js";import"./VSelect.c2493951.js";import"./VList.2683ce5e.js";import"./VDivider.87b4f161.js";import"./dialog-transition.4db747b4.js";import"./VMenu.da48d093.js";import"./scopeId.d9d96941.js";import"./VOverlay.69973680.js";import"./lazy.1d27421d.js";import"./VCheckboxBtn.d4509fcf.js";import"./VSelectionControl.bdb5dadf.js";import"./VChip.8fdd4bbd.js";import"./VSwitch.c89adff2.js";import"./VDialog.cdcf31e7.js";import"./formatters.4acf44a4.js";import"./index.b522f886.js";import"./VSpacer.7ad3e650.js";import"./VTextarea.e2c2416d.js";import"./EnableOneTimePasswordDialog.97cee15b.js";import"./VAlert.e3080eb9.js";import"./VTable.547f6da8.js";import"./VCheckbox.1abdc290.js";import"./useInvoiceStore.e7466d89.js";import"./VTooltip.4c8aadf8.js";import"./VPagination.a5e99551.js";import"./xamarin.9738782b.js";import"./VTimelineItem.98bfb0b2.js";import"./VSlideGroup.194f57a7.js";import"./ssrBoot.246faa36.js";const Tt={__name:"[id]",setup(I){const d=C(),f=V(),m=n(),r=n(null),_=[{icon:"tabler-user-check",title:"Overview"},{icon:"tabler-lock",title:"Security"},{icon:"tabler-currency-dollar",title:"Billing & Plan"},{icon:"tabler-bell",title:"Notifications"},{icon:"tabler-link",title:"Connections"}];return d.fetchUser(Number(f.params.id)).then(s=>{m.value=s.data}),(s,i)=>l(m)?(p(),b(R,{key:0},{default:o(()=>[t(c,{cols:"12",md:"5",lg:"4"},{default:o(()=>[t(N,{"user-data":l(m)},null,8,["user-data"])]),_:1}),t(c,{cols:"12",md:"7",lg:"8"},{default:o(()=>[t(T,{modelValue:l(r),"onUpdate:modelValue":i[0]||(i[0]=e=>u(r)?r.value=e:null),class:"v-tabs-pill"},{default:o(()=>[(p(),k(U,null,v(_,e=>t(h,{key:e.icon},{default:o(()=>[t(w,{size:18,icon:e.icon,class:"me-1"},null,8,["icon"]),y("span",null,B(e.title),1)]),_:2},1024)),64))]),_:1},8,["modelValue"]),t(F,{modelValue:l(r),"onUpdate:modelValue":i[1]||(i[1]=e=>u(r)?r.value=e:null),class:"mt-6 disable-tab-transition",touch:!1},{default:o(()=>[t(a,null,{default:o(()=>[t($)]),_:1}),t(a,null,{default:o(()=>[t(x)]),_:1}),t(a,null,{default:o(()=>[t(S)]),_:1}),t(a,null,{default:o(()=>[t(D)]),_:1}),t(a,null,{default:o(()=>[t(L)]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})):g("",!0)}};export{Tt as default};