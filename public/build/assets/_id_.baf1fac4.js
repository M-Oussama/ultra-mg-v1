import{bd as z,k as x,Z as E,q as e,o as n,c as m,p as s,w as i,m as t,x as o,b as w,y as r,F as G,a as M,E as h,D as V}from"./main.3e4a7664.js";import{_ as Q}from"./InvoiceAddPaymentDrawer.3c6d0d6c.js";import{_ as Y}from"./InvoiceSendInvoiceDrawer.6ca9cae3.js";import{u as H}from"./useSaleStore.4931bd92.js";import{a as J,V as C}from"./VBtn.cda7f3ff.js";import{a as W}from"./VOverlay.0742860e.js";import{a as f,V as A}from"./VRow.bb367534.js";import{V as P,c as g}from"./VCard.5e5240e5.js";import{V as S}from"./VDivider.b2cf7867.js";import{V as X}from"./VTable.27e9b22c.js";import{V as b}from"./VInput.8a9622b2.js";import{V as I}from"./VSwitch.da78dae7.js";import{V as B}from"./VAutocomplete.63a7f0ca.js";import{V as L}from"./VTextField.9e08004a.js";import"./AppDateTimePicker.092ee910.js";import"./VField.d470a36f.js";import"./index.969717a5.js";import"./position.7ef297c0.js";import"./router.edbbf26c.js";import"./easing.36b781ab.js";import"./errorsMiddleware.3e8cce59.js";import"./VSpacer.c701354e.js";import"./VAvatar.8e1eaad0.js";import"./VImg.010fd1a5.js";import"./VForm.2c2bef0a.js";import"./forwardRefs.c003b6b8.js";import"./VTextarea.cbbe783d.js";/* empty css                   */import"./VCounter.3fda8fca.js";import"./VNavigationDrawer.65bef4a8.js";import"./ssrBoot.4614d505.js";import"./VChip.2a43419b.js";import"./lazy.a71a3757.js";import"./VSelectionControl.af9e00ba.js";import"./VSelect.d0e4adb9.js";import"./VList.313bf15a.js";import"./dialog-transition.5f6c212d.js";import"./VMenu.59c9c2bc.js";import"./scopeId.0ab93db8.js";import"./VCheckboxBtn.8b0eba1a.js";import"./filter.0f00992b.js";const tt={key:0},et={class:"ma-sm-3"},st={class:"d-flex align-center mb-6"},ot={class:"font-weight-bold text-xl"},lt={class:"mb-0"},it=t("h4",{class:"mb-0"}," KASR EL ABTALE, 19000, SETIF ",-1),dt={class:"mb-0"},at={class:"mb-0"},nt={class:"mt-4 ma-sm-4"},rt={class:"font-weight-medium text-xl mb-2"},ct={class:"mb-2"},mt=t("span",null,"Date : ",-1),ut={class:"font-weight-semibold"},xt={key:0,class:"mb-2"},ht=t("span",null,"Payment: ",-1),_t={class:"font-weight-semibold"},pt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border"},ft=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right"}," N\xB0RC ",-1),bt={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font"},vt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},wt=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right"}," N\xB0IF ",-1),gt={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font"},yt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Vt=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right"}," N\xB0IS ",-1),kt={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font"},Ft={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Nt=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right padding-right-0"},[t("span",null," N\xB0ART ")],-1),Dt={class:"v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font"},jt={class:"font-weight-medium text-sm mb-2"},Tt={key:0,class:"mb-2 text-sm"},Ct=t("span",null,"Payment: ",-1),St={class:"font-weight-semibold"},It={class:"mb-2 text-sm"},At=t("span",null,"Date : ",-1),Pt={class:"font-weight-semibold"},Rt={class:"v-col-md-6"},Ut=t("h4",{class:"font-weight-bold text-sm-h6 mb-4 text-color-black text-center"},null,-1),Zt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Ot=t("div",{class:"v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Client ",-1),Et={class:"v-col-md-10 border-left padding-8 data-font text-weight-bold"},Bt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Lt=t("div",{class:"v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Address ",-1),Kt={class:"v-col-md-10 border-left padding-8 data-font text-weight-bold"},$t={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},qt=t("div",{class:"v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Phone ",-1),zt={class:"v-col-md-10 border-left padding-8 data-font text-weight-bold"},Gt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Mt=t("div",{class:"v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Email ",-1),Qt={class:"v-col-md-10 border-left padding-8 data-font text-weight-bold"},Yt=t("div",{class:"v-col-md-2"},null,-1),Ht={key:0,class:"v-col-md-4"},Jt=t("h4",{class:"font-weight-bold text-sm-h6 mb-4 text-color-black text-center text-h4 align-center"},null,-1),Wt={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border"},Xt=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," N\xB0RC ",-1),te={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},ee={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},se=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," N\xB0IF ",-1),oe={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},le={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},ie=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," N\xB0IS ",-1),de={class:"v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},ae={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},ne=t("div",{class:"v-col-md-3 text-sm-subtitle-2 border-right padding-right-0 text-h4 text-weight-bold"},[t("span",null," N\xB0ART ")],-1),re={class:"v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font text-h4 text-weight-bold"},ce={key:1,class:"v-col-md-4"},me=t("h4",{class:"font-weight-bold text-sm-h6 mb-4 text-color-black text-center align-center"},null,-1),ue={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border"},xe=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Facture ",-1),he={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},_e={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},pe=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Payment Type ",-1),fe={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},be={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},ve=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Date ",-1),we={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},ge=t("th",{class:"text-sm-caption text-h4 text-weight-bold",scope:"col"}," ID ",-1),ye={key:0,class:"text-sm-caption text-h4 text-weight-bold",scope:"col"},Ve=t("th",{class:"text-sm-caption text-h4 text-weight-bold",scope:"col"}," name ",-1),ke={key:1,scope:"col",class:"text-center text-sm-caption text-h4 text-weight-bold"},Fe=t("th",{scope:"col",class:"text-center text-sm-caption text-h4 text-weight-bold"}," QTY ",-1),Ne={key:2,scope:"col",class:"text-center text-sm-caption text-h4 text-weight-bold"},De={class:"text-no-wrap text-sm-caption text-h4 text-weight-bold"},je={key:0,class:"text-no-wrap text-sm-caption text-h4 text-weight-bold"},Te={class:"text-no-wrap"},Ce={class:"text-sm-caption mb-0 text-h4 text-weight-bold"},Se={key:1,class:"text-center text-sm-caption text-h4 text-weight-bold"},Ie={class:"text-center text-sm-caption text-h4 text-weight-bold"},Ae={key:2,class:"text-center text-sm-caption text-h4 text-weight-bold"},Pe={class:"my-2 mx-sm-5 v-col-md-6"},Re={key:0,class:"mt-10 d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Ue=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Ancien solde ",-1),Ze={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},Oe={key:1,class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Ee={key:0,class:"v-col-md-5 text-sm-subtitle-2 border-right text-success text-h4 text-weight-bold"},Be={key:1,class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"},Le={key:2,class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-success text-h4 text-weight-bold"},Ke={key:3,class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},$e={class:"my-2 mx-sm-4 v-col-md-4"},qe={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},ze=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Total ",-1),Ge={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},Me={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Qe=t("div",{class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"}," Paiement ",-1),Ye={class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},He={class:"d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border"},Je={key:0,class:"v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"},We={key:1,class:"v-col-md-5 text-sm-subtitle-2 border-right text-success text-h4 text-weight-bold"},Xe={key:2,class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-success text-h4 text-weight-bold"},ts={key:3,class:"v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold"},es=t("div",{class:"d-flex mx-sm-4"},[t("h6",{class:"text-sm font-weight-semibold me-1"}," Note: "),t("span",null,"Merci pour votre r\xE8glement rapide. Nous appr\xE9cions votre collaboration ")],-1),ss={class:"d-flex align-center justify-space-between mt-5"},os={class:"d-flex align-center justify-space-between"},ls={class:"d-flex align-center justify-space-between"},is={class:"d-flex align-center justify-space-between"},ds={key:0},Ys={__name:"[id]",setup(as){const K=H(),R=z(),d=x({id:1,balance:0,total_amount:0,sale_date:null,client_id:1,sale_status:{id:-1,name:""},amount_letter:"",notes:"",client:{id:-1,name:"",surname:"",address:"",email:"",phone:"",NRC:"",NIF:"",NART:"",NIS:""},sale_items:[]}),l=x({sku:!1,invoice:!1,price:!0,paymentType:"Espece",sold:!1}),p=x(0),U=x();let y=x([]);const k=x(1),_=x({name:"",address:"",phone:"",email:"",NRC:"",NIF:"",NART:"",NIS:"",capitale:""});let F=x([]);const Z=x(!1);x();const N=x(!1),D=x(!1),O=x({isActive:!1});K.fetchSale(Number(R.params.id)).then(u=>{d.value=u.data.sale,p.value=u.data.sold,U.value={...d.value.client},F=u.data.companies,y=u.data.clients,_.value=F[0],Z.value=!0}).catch(u=>{console.log(u)});const $=()=>{window.print()};E(k,(u,c,a)=>{_.value=F[u-1],console.log(_)});const q=u=>u.name+" "+u.surname,j=x("Facture Proforma"),T=x();return E(T,(u,c,a)=>{u||(d.value.client=U);for(let v=0;v<y.length;v++)y[v].id===u&&(d.value.client={...y[v]})}),(u,c)=>e(Z)?(n(),m("section",tt,[s(W,{"model-value":e(O).isActive,class:"align-center justify-center"},{default:i(()=>[s(J,{color:"primary",indeterminate:"",size:"64"})]),_:1},8,["model-value"]),s(A,null,{default:i(()=>[s(f,{class:"printed",cols:"12",md:"9"},{default:i(()=>[s(P,null,{default:i(()=>[s(g,{class:"d-flex flex-wrap justify-space-between flex-column flex-sm-row print-row pb-0"},{default:i(()=>[s(A,null,{default:i(()=>[s(f,{cols:"8"},{default:i(()=>[t("div",et,[t("div",st,[t("h6",ot,o(e(_).name),1)]),t("h4",lt,o(e(_).address),1),it,t("h4",dt,o(e(_).email),1),t("h4",at,o(e(_).phone),1)])]),_:1}),e(l).invoice?r("",!0):(n(),w(f,{key:0,cols:"4"},{default:i(()=>[t("div",nt,[t("h6",rt," Bon Livraison #"+o(e(d).id),1),t("h4",ct,[mt,t("span",ut,o(e(d).sale_date),1)]),e(l).price?(n(),m("h4",xt,[ht,t("span",_t,o(e(l).paymentType),1)])):r("",!0)])]),_:1})),e(l).invoice?(n(),w(f,{key:1,cols:"4",class:"mt-5"},{default:i(()=>[t("div",pt,[ft,t("div",bt,o(e(_).NRC),1)]),t("div",vt,[wt,t("div",gt,o(e(_).NIF),1)]),t("div",yt,[Vt,t("div",kt,o(e(_).NIS),1)]),t("div",Ft,[Nt,t("div",Dt,o(e(_).NART),1)])]),_:1})):r("",!0)]),_:1})]),_:1}),e(l).invoice?(n(),w(S,{key:0})):r("",!0),e(l).invoice?(n(),w(A,{key:1,class:"ma-3 text-center"},{default:i(()=>[s(f,{cols:"4"},{default:i(()=>[t("h6",jt,o(e(j))+" #"+o(e(d).id),1)]),_:1}),s(f,{cols:"4"},{default:i(()=>[e(l).price?(n(),m("h4",Tt,[Ct,t("span",St,o(e(l).paymentType),1)])):r("",!0)]),_:1}),s(f,{cols:"4"},{default:i(()=>[t("h4",It,[At,t("span",Pt,o(e(d).sale_date),1)])]),_:1})]),_:1})):r("",!0),s(g,{class:"invoiceGeneral d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row"},{default:i(()=>[t("div",Rt,[Ut,t("div",Zt,[Ot,t("div",Et,o(e(d).client.name)+" "+o(e(d).client.surname),1)]),t("div",Bt,[Lt,t("div",Kt,o(e(d).client.address),1)]),t("div",$t,[qt,t("div",zt,o(e(d).client.phone),1)]),t("div",Gt,[Mt,t("div",Qt,o(e(d).client.email),1)])]),Yt,e(l).invoice?(n(),m("div",Ht,[Jt,t("div",Wt,[Xt,t("div",te,o(e(d).client.NRC),1)]),t("div",ee,[se,t("div",oe,o(e(d).client.NIF),1)]),t("div",le,[ie,t("div",de,o(e(d).client.NIS),1)]),t("div",ae,[ne,t("div",re,o(e(d).client.NART),1)])])):r("",!0),e(l).invoice?r("",!0):(n(),m("div",ce,[me,t("div",ue,[xe,t("div",he," #"+o(e(d).id),1)]),t("div",_e,[pe,t("div",fe,o(e(l).paymentType),1)]),t("div",be,[ve,t("div",we,o(e(d).sale_date),1)])]))]),_:1}),s(S),s(X,null,{default:i(()=>[t("thead",null,[t("tr",null,[ge,e(l).sku?(n(),m("th",ye," SKU ")):r("",!0),Ve,e(l).price?(n(),m("th",ke," Price ")):r("",!0),Fe,e(l).price?(n(),m("th",Ne," TOTAL ")):r("",!0)])]),t("tbody",null,[(n(!0),m(G,null,M(e(d).sale_items,(a,v)=>(n(),m("tr",{key:a.name},[t("td",De,o(v+1),1),e(l).sku?(n(),m("td",je,o(a.product.SKU),1)):r("",!0),t("td",Te,[t("h4",Ce,o(a.product.name),1)]),e(l).price?(n(),m("td",Se,o(a.price)+" DZD ",1)):r("",!0),t("td",Ie,o(a.quantity),1),e(l).price?(n(),m("td",Ae,o(a.total_price)+" DZD ",1)):r("",!0)]))),128))])]),_:1}),e(l).price?(n(),w(S,{key:2,class:"my-2"})):r("",!0),e(l).price?(n(),w(g,{key:3,class:"d-flex justify-space-between flex-column flex-sm-row print-row pt-0"},{default:i(()=>[t("div",Pe,[t("h5",null,"La Facture est arret\xE9e \xE0 la somme de : "+o(e(d).amount_letter),1),e(l).sold?(n(),m("div",Re,[Ue,t("div",Ze,o(parseFloat(e(p)).toFixed(2))+" DZD ",1)])):r("",!0),e(l).sold?(n(),m("div",Oe,[e(l).price?(n(),m("div",Ee," Nouveau ")):r("",!0),e(l).price?r("",!0):(n(),m("div",Be," Nouveau ")),e(l).price?(n(),m("div",Le,o(parseFloat(e(p))>0?(parseFloat(e(d).total_amount)+parseFloat(e(p))).toFixed(2):(-parseFloat(e(d).total_amount)+parseFloat(e(p))).toFixed(2))+" DZD ",1)):r("",!0),e(l).price?r("",!0):(n(),m("div",Ke,o(parseFloat(e(p))>0?(parseFloat(e(d).total_amount)+parseFloat(e(p))).toFixed(2):(-parseFloat(e(d).total_amount)+parseFloat(e(p))).toFixed(2))+" DZD ",1))])):r("",!0)]),t("div",$e,[t("div",qe,[ze,t("div",Ge,o(e(d).total_amount)+" DZD ",1)]),t("div",Me,[Qe,t("div",Ye,o(parseFloat(parseFloat(e(d).regulation)+parseFloat(e(d).total_amount-e(d).balance)).toFixed(2))+" DZD ",1)]),t("div",He,[e(l).sold?(n(),m("div",Je," Nouveau ")):r("",!0),e(l).sold?r("",!0):(n(),m("div",We," Nouveau ")),e(l).sold?r("",!0):(n(),m("div",Xe,o(parseFloat(e(d).balance).toFixed(2))+" DZD ",1)),e(l).sold?(n(),m("div",ts,o(parseFloat(e(d).balance).toFixed(2))+" DZD ",1)):r("",!0)])])]),_:1})):r("",!0),s(S),s(g,null,{default:i(()=>[es]),_:1})]),_:1})]),_:1}),s(f,{cols:"12",md:"3",class:"d-print-none"},{default:i(()=>[s(P,null,{default:i(()=>[s(g,null,{default:i(()=>[s(C,{block:"","prepend-icon":"tabler-send",class:"mb-2",onClick:c[0]||(c[0]=a=>D.value=!0)},{default:i(()=>[h(" Send Invoice ")]),_:1}),s(C,{block:"",variant:"tonal",color:"secondary",class:"mb-2",onClick:$},{default:i(()=>[h(" Print ")]),_:1}),s(C,{block:"",color:"secondary",variant:"tonal",class:"mb-2",to:{name:"apps-POS-sale-edit-id",params:{id:e(R).params.id}}},{default:i(()=>[h(" Edit Invoice ")]),_:1},8,["to"]),s(C,{block:"","prepend-icon":"tabler-currency-dollar",class:"mb-2",onClick:c[1]||(c[1]=a=>N.value=!0)},{default:i(()=>[h(" Add Payment ")]),_:1})]),_:1})]),_:1}),s(P,{class:"mt-7"},{default:i(()=>[s(g,null,{default:i(()=>[t("div",ss,[s(b,{for:"sold"},{default:i(()=>[h(" Sold ")]),_:1}),t("div",null,[s(I,{id:"sku",modelValue:e(l).sold,"onUpdate:modelValue":c[2]||(c[2]=a=>e(l).sold=a)},null,8,["modelValue"])])]),t("div",os,[s(b,{for:"price"},{default:i(()=>[h(" SKU ")]),_:1}),t("div",null,[s(I,{id:"sku",modelValue:e(l).sku,"onUpdate:modelValue":c[3]||(c[3]=a=>e(l).sku=a)},null,8,["modelValue"])])]),t("div",ls,[s(b,{for:"price"},{default:i(()=>[h(" Facture ")]),_:1}),t("div",null,[s(I,{id:"sku",modelValue:e(l).invoice,"onUpdate:modelValue":c[4]||(c[4]=a=>e(l).invoice=a)},null,8,["modelValue"])])]),t("div",is,[s(b,{for:"price"},{default:i(()=>[h(" Price ")]),_:1}),t("div",null,[s(I,{id:"price",modelValue:e(l).price,"onUpdate:modelValue":c[5]||(c[5]=a=>e(l).price=a)},null,8,["modelValue"])])]),t("div",null,[s(b,{class:"mb-4 mt-1"},{default:i(()=>[h(" Company ")]),_:1}),s(B,{modelValue:e(k),"onUpdate:modelValue":c[6]||(c[6]=a=>V(k)?k.value=a:null),items:e(F),"item-value":"id",class:"text-sm-subtitle-2","item-title":"name",label:"Company"},null,8,["modelValue","items"])]),t("div",null,[t("div",null,[s(b,{for:"payment-terms",class:"mb-1 mt-1"},{default:i(()=>[h(" Payment ")]),_:1}),s(L,{modelValue:e(l).paymentType,"onUpdate:modelValue":c[7]||(c[7]=a=>e(l).paymentType=a),placeholder:"Thanks for your business"},null,8,["modelValue"])])]),e(l).invoice?(n(),m("div",ds,[s(b,{for:"payment-terms",class:"mb-1 mt-1"},{default:i(()=>[h(" Invoice Type ")]),_:1}),s(L,{modelValue:e(j),"onUpdate:modelValue":c[8]||(c[8]=a=>V(j)?j.value=a:null),placeholder:"Put the Invoice Type"},null,8,["modelValue"])])):r("",!0),t("div",null,[s(b,{class:"mb-4 mt-1"},{default:i(()=>[h(" Client ")]),_:1}),s(B,{clearable:"",modelValue:e(T),"onUpdate:modelValue":c[9]||(c[9]=a=>V(T)?T.value=a:null),items:e(y),"item-value":"id","item-title":q,label:"Client"},null,8,["modelValue","items"])])]),_:1})]),_:1})]),_:1})]),_:1}),s(Q,{data:e(d),loading:e(O),isDrawerOpen:e(N),"onUpdate:isDrawerOpen":c[10]||(c[10]=a=>V(N)?N.value=a:null)},null,8,["data","loading","isDrawerOpen"]),s(Y,{isDrawerOpen:e(D),"onUpdate:isDrawerOpen":c[11]||(c[11]=a=>V(D)?D.value=a:null)},null,8,["isDrawerOpen"])])):r("",!0)}};export{Ys as default};