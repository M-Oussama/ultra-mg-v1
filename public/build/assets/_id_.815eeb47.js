import{bd as P,j as D,k as r,o as b,b as g,w as o,p as t,q as l,E as m,y as A,m as u,D as c}from"./main.eb2abb0f.js";import"./AppDateTimePicker.444d287c.js";import"./useSaleStore.aefb40c5.js";import{e as p}from"./errorsMiddleware.9385531a.js";import{s as O}from"./successMiddleware.5e0a2589.js";import{_ as T}from"./InvoiceSendInvoiceDrawer.50480ee8.js";import{_ as j}from"./InvoiceEditable.26c7ce3d.js";import{u as B}from"./usereturnStore.547318c0.js";/* empty css                        */import{a as I,V as w}from"./VBtn.4b29655b.js";import{a as U}from"./VOverlay.8b37277a.js";import{a as N,V as M}from"./VRow.fc64865f.js";import{V as $,c as E}from"./VCard.d18cee11.js";import{V as S}from"./VInput.12678d62.js";import{V as h}from"./VSwitch.10a5493c.js";import"./VField.d31c7729.js";import"./index.1d60d957.js";import"./position.4305ca24.js";import"./router.cee8299d.js";import"./easing.36b781ab.js";import"./VSpacer.47fd3527.js";import"./VAvatar.ef806db0.js";import"./VImg.c89b2431.js";import"./VForm.b7335b57.js";import"./forwardRefs.c003b6b8.js";import"./VTextField.cfaf068e.js";/* empty css                   */import"./VCounter.28fd36d2.js";import"./VTextarea.49536005.js";import"./VChip.574f5191.js";import"./VNavigationDrawer.5a003ebe.js";import"./ssrBoot.b678eb07.js";import"./DialogCloseBtn.d07cf106.js";import"./InvoiceProductEdit.c7f3d3ce.js";/* empty css                                                           *//* empty css                                                        */import"./VDialog.9c7be325.js";import"./scopeId.deee8bf4.js";import"./dialog-transition.ae223f33.js";import"./VTable.dc32512e.js";import"./VDivider.d741eb3a.js";import"./VAutocomplete.984d747d.js";import"./VSelect.1a2459fe.js";import"./VList.abe45db6.js";import"./VMenu.6af9ca11.js";import"./VCheckboxBtn.c57877f7.js";import"./VSelectionControl.b7f754bc.js";import"./filter.44b73af0.js";import"./lazy.4714770b.js";const K=u("h6",{class:"text-sm font-weight-medium mb-3"}," Payment Method: ",-1),L={class:"mt-5 d-flex align-center justify-space-between"},q={class:"d-flex align-center justify-space-between"},z={class:"d-flex align-center justify-space-between"},Ke={__name:"[id]",setup(F){const C=P(),R=D(),e=r({id:1,balance:0,total_amount:0,date:null,client_id:1,city:null,cities:[],sale_status:{id:-1,name:""},amount_letter:"",notes:"",client:{id:-1,name:"",surname:"",address:"",email:"",phone:"",NRC:"",NIF:"",NART:"",NIS:""},sale_items:[],clients:[],products:[],sale_statues:[],payment:!0,paymentAmount:0,payment_total:0,totalPayment:0,removePaymentRecords:!1}),k=B(),s=r({isActive:!1}),f=r(null);k.getReturnData(Number(C.params.id)).then(a=>{s.isActive=!1,e.value=a.data.product_return,e.value.sale_date=e.value.date,e.value.clients=a.data.clients,e.value.cities=a.data.cities,e.value.products=a.data.products,e.value.sale_statues=a.data.sale_statues}).catch(a=>{s.isActive=!1,console.log(a)});const d=r(!1);r(!1);const v=r(!0),_=r(!1),y=r(!1),V=r(!1),x=()=>{e.value.client.id===-1||e.value.date===null||e.value.sale_items.length===0?(e.value.client.id===-1&&p("Client Not Selected","Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"),e.value.date===null&&p("The date field is empty","Date field remains empty, and no client has been chosen for the invoice."),e.value._product_return_items.length===0&&p("Select a least one product.","Kindly ensure you've selected at least one product before proceeding.")):s.isActive||(s.isActive=!0,k.updateReturn(e.value).then(a=>{s.isActive=!1,V.value=!0,f.value=a.data.id,O("Product Return updated Successfully"),R.push("/apps/pos/return/preview/"+f.value)}).catch(a=>{s.isActive=!1,console.log(a),p("Error",""),console.log(a)}))};return(a,i)=>(b(),g(M,null,{default:o(()=>[t(U,{"model-value":l(s).isActive,class:"align-center justify-center"},{default:o(()=>[t(I,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),t(N,{cols:"12",md:"9"},{default:o(()=>[t(j,{data:l(e),loading:l(s)},null,8,["data","loading"])]),_:1}),t(N,{cols:"12",md:"3"},{default:o(()=>[t($,{class:"mb-8"},{default:o(()=>[t(E,null,{default:o(()=>[t(w,{block:"","prepend-icon":"tabler-send",class:"mb-2",onClick:i[0]||(i[0]=n=>d.value=!0)},{default:o(()=>[m(" Send Invoice ")]),_:1}),l(V)?(b(),g(w,{key:0,block:"",color:"secondary",variant:"tonal",class:"mb-2",to:{name:"apps-POS-return-preview-id",params:{id:l(f)}}},{default:o(()=>[m(" Preview ")]),_:1},8,["to"])):A("",!0),l(V)?A("",!0):(b(),g(w,{key:1,loading:l(s).isActive,disabled:l(s).isActive,block:"",color:"success",class:"mb-2",onClick:x},{default:o(()=>[m(" Save ")]),_:1},8,["loading","disabled"]))]),_:1})]),_:1}),K,u("div",L,[t(S,{for:"payment-terms"},{default:o(()=>[m(" Payment Terms ")]),_:1}),u("div",null,[t(h,{id:"payment-terms",modelValue:l(v),"onUpdate:modelValue":i[1]||(i[1]=n=>c(v)?v.value=n:null)},null,8,["modelValue"])])]),u("div",q,[t(S,{for:"client-notes"},{default:o(()=>[m(" Client Notes ")]),_:1}),u("div",null,[t(h,{id:"client-notes",modelValue:l(_),"onUpdate:modelValue":i[2]||(i[2]=n=>c(_)?_.value=n:null)},null,8,["modelValue"])])]),u("div",z,[t(S,{for:"payment-stub"},{default:o(()=>[m(" Payment Stub ")]),_:1}),u("div",null,[t(h,{id:"payment-stub",modelValue:l(y),"onUpdate:modelValue":i[3]||(i[3]=n=>c(y)?y.value=n:null)},null,8,["modelValue"])])])]),_:1}),t(T,{isDrawerOpen:l(d),"onUpdate:isDrawerOpen":i[4]||(i[4]=n=>c(d)?d.value=n:null)},null,8,["isDrawerOpen"])]),_:1}))}};export{Ke as default};