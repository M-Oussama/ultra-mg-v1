import{bd as A,k as n,o as D,b as P,w as a,q as l,p as e,y as C,E as i,m as s,D as r}from"./main.e3ae6f5b.js";import{_ as N}from"./InvoiceAddPaymentDrawer.98e91030.js";import{_ as T}from"./InvoiceEditable.f00b6f17.js";import{_ as U}from"./InvoiceSendInvoiceDrawer.411e4a43.js";import{u as B}from"./useInvoiceStore.84181b39.js";import{a as S,V as h}from"./VRow.307db058.js";import{V as I,c as O}from"./VCard.9574192c.js";import{V as u}from"./VBtn.78b9f864.js";import{V as $}from"./VSelect.e8db8923.js";import{V as b}from"./VInput.ebff45cb.js";import{V as _}from"./VSwitch.2c046205.js";import"./AppDateTimePicker.fa89fced.js";import"./VField.93385836.js";import"./index.ba8dc909.js";import"./position.4c99774d.js";import"./router.92e08cce.js";import"./easing.36b781ab.js";import"./useSaleStore.33d18f08.js";import"./errorsMiddleware.cd79b49a.js";import"./VSpacer.50e7ecc1.js";import"./VAvatar.a66dff35.js";import"./VImg.a8f9a1c8.js";import"./VForm.724a7186.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.c4710a67.js";/* empty css                   */import"./VCounter.a137105a.js";import"./VTextarea.f0669caf.js";import"./VNavigationDrawer.2a3e31b0.js";import"./ssrBoot.170b97eb.js";/* empty css                        */import"./useCertifyInvoiceListStore.ff29b3f1.js";import"./VueSearchSelect.73cea5c2.js";import"./VDivider.04b9ef9a.js";import"./VChip.dd80b8e5.js";import"./VList.d6dc74e3.js";import"./dialog-transition.cc00eae2.js";import"./VMenu.d6d893ca.js";import"./scopeId.55b8accf.js";import"./VOverlay.bef3a4a8.js";import"./lazy.f3535b64.js";import"./VCheckboxBtn.41b91637.js";import"./VSelectionControl.90a7537b.js";const j={class:"d-flex gap-2"},M={class:"w-50"},R={class:"w-50"},L={class:"d-flex align-center justify-space-between"},q={class:"d-flex align-center justify-space-between"},E={class:"d-flex align-center justify-space-between"},he={__name:"[id]",setup(W){const g=B(),w=A(),c=n();g.fetchInvoice(Number(w.params.id)).then(m=>{c.value={invoice:m.data.invoice,paymentDetails:m.data.paymentDetails,purchasedProducts:[{title:"App Design",cost:24,hours:2,description:"Designed UI kit & app pages."}],note:"It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance projects. Thank You!",paymentMethod:"Bank Account",salesperson:"Tom Cook",thanksNote:"Thanks for your business"}}).catch(m=>{console.log(m)});const p=n(!1),d=n(!1),f=n(!0),v=n(!1),V=n(!1),y=n("Bank Account"),x=["Bank Account","PayPal","UPI Transfer"];return(m,t)=>(D(),P(h,null,{default:a(()=>{var k;return[(k=l(c))!=null&&k.invoice?(D(),P(S,{key:0,cols:"12",md:"9"},{default:a(()=>[e(T,{data:l(c)},null,8,["data"])]),_:1})):C("",!0),e(S,{cols:"12",md:"3"},{default:a(()=>[e(I,{class:"mb-8"},{default:a(()=>[e(O,null,{default:a(()=>[e(u,{block:"","prepend-icon":"tabler-send",class:"mb-2",onClick:t[0]||(t[0]=o=>p.value=!0)},{default:a(()=>[i(" Send Invoice ")]),_:1}),s("div",j,[s("div",M,[e(u,{block:"",color:"secondary",variant:"tonal",class:"mb-2",to:{name:"apps-invoice-preview-id",params:{id:l(w).params.id}}},{default:a(()=>[i(" Preview ")]),_:1},8,["to"])]),s("div",R,[e(u,{block:"",color:"secondary",variant:"tonal",class:"mb-2"},{default:a(()=>[i(" Save ")]),_:1})])]),e(u,{block:"","prepend-icon":"tabler-currency-dollar",onClick:t[1]||(t[1]=o=>d.value=!0)},{default:a(()=>[i(" Add Payment ")]),_:1})]),_:1})]),_:1}),e($,{modelValue:l(y),"onUpdate:modelValue":t[2]||(t[2]=o=>r(y)?y.value=o:null),items:x,label:"Accept Payment Via",class:"mb-6"},null,8,["modelValue"]),s("div",L,[e(b,{for:"payment-terms"},{default:a(()=>[i(" Payment Terms ")]),_:1}),s("div",null,[e(_,{id:"payment-terms",modelValue:l(f),"onUpdate:modelValue":t[3]||(t[3]=o=>r(f)?f.value=o:null)},null,8,["modelValue"])])]),s("div",q,[e(b,{for:"client-notes"},{default:a(()=>[i(" Client Notes ")]),_:1}),s("div",null,[e(_,{id:"client-notes",modelValue:l(v),"onUpdate:modelValue":t[4]||(t[4]=o=>r(v)?v.value=o:null)},null,8,["modelValue"])])]),s("div",E,[e(b,{for:"payment-stub"},{default:a(()=>[i(" Payment Stub ")]),_:1}),s("div",null,[e(_,{id:"payment-stub",modelValue:l(V),"onUpdate:modelValue":t[5]||(t[5]=o=>r(V)?V.value=o:null)},null,8,["modelValue"])])])]),_:1}),e(U,{isDrawerOpen:l(p),"onUpdate:isDrawerOpen":t[6]||(t[6]=o=>r(p)?p.value=o:null)},null,8,["isDrawerOpen"]),e(N,{isDrawerOpen:l(d),"onUpdate:isDrawerOpen":t[7]||(t[7]=o=>r(d)?d.value=o:null)},null,8,["isDrawerOpen"])]}),_:1}))}};export{he as default};