/* empty css                        */import{e as b}from"./errorsMiddleware.970a9b98.js";/* empty css                                                           */import{a as s,V as u}from"./VRow.fa44e1fc.js";import{V as i}from"./VTextField.b40b8fac.js";import{V as m}from"./VBtn.eec2e34a.js";import{A as q,Z as h,o as k,c as P,m as o,p as e,w as a,x as v,q as w,C as p,F as C}from"./main.e3e9927b.js";import{V as g}from"./VCard.2d51fc32.js";const N={class:"add-products-header mb-2 d-none d-md-flex"},z=o("span",{class:"text-sm"}," Item ",-1),A=o("span",{class:"text-sm"}," Price ",-1),B=o("span",{class:"text-sm"}," Quantity ",-1),F=o("span",{class:"text-sm"}," Total ",-1),I={class:"pa-5 flex-grow-1"},Q={class:"text-sm-center my-2"},U=o("span",{class:"d-inline d-md-none text-sm"},"Price: ",-1),D={class:"text-body-1 text-sm"},H={class:"d-flex flex-column justify-space-between border-s pa-1"},G={__name:"InvoiceProductEdit",props:{id:{type:Number,required:!0},data:{type:Object,required:!0,default:()=>({id:"",name:"",brand:"",description:"",product_code:"",SKU:"",price:15,stockable:!1,tax_rate:.5,product_stock:{quantity:0}})}},emits:["removeProduct","totalAmount","priceHistory"],setup(f,{emit:n}){const t=f,r=t.data.quantity;t.data.oldQuantity=r;const c=q(()=>Number(t.data.price)*Number(t.data.quantity)),_=()=>{n("removeProduct",t.data)},y=()=>{n("totalAmount",t.data)},x=()=>{n("priceHistory",t.data)};h(c,()=>{y()},{immediate:!0});const V=()=>{t.data.product.stockable&&t.data.product.product_stock.quantity+r<t.data.quantity&&(t.data.quantity=r,b("Insufficient quantity only "+t.data.product.product_stock.quantity+" left"))};return(j,d)=>(k(),P(C,null,[o("div",N,[e(u,{class:"font-weight-medium px-4"},{default:a(()=>[e(s,{cols:"12",md:"6"},{default:a(()=>[z]),_:1}),e(s,{cols:"12",md:"2"},{default:a(()=>[A]),_:1}),e(s,{cols:"12",md:"2"},{default:a(()=>[B]),_:1}),e(s,{cols:"12",md:"2"},{default:a(()=>[F]),_:1})]),_:1})]),e(g,{flat:"",border:"",class:"d-flex flex-row"},{default:a(()=>[o("div",I,[e(u,null,{default:a(()=>[e(s,{cols:"12",md:"6",sm:"4"},{default:a(()=>[e(i,{modelValue:t.data.product.name,"onUpdate:modelValue":d[0]||(d[0]=l=>t.data.product.name=l),type:"string",label:"Product",disabled:""},null,8,["modelValue"])]),_:1}),e(s,{cols:"12",md:"2",sm:"4"},{default:a(()=>[e(i,{modelValue:t.data.price,"onUpdate:modelValue":d[1]||(d[1]=l=>t.data.price=l),type:"number",label:"Price"},null,8,["modelValue"])]),_:1}),e(s,{cols:"12",md:"2",sm:"4"},{default:a(()=>[e(i,{modelValue:t.data.quantity,"onUpdate:modelValue":d[2]||(d[2]=l=>t.data.quantity=l),type:"number",label:"Quantity",onChange:V},null,8,["modelValue"])]),_:1}),e(s,{cols:"12",md:"2",sm:"4"},{default:a(()=>[o("p",Q,[U,o("span",D,v(w(c).toFixed(2))+" DZD",1)])]),_:1})]),_:1})]),o("div",H,[e(m,{icon:"",size:"x-small",color:"default",variant:"text",onClick:_},{default:a(()=>[e(p,{size:"20",icon:"tabler-x"})]),_:1}),e(m,{icon:"",size:"x-small",color:"default",variant:"text",onClick:x},{default:a(()=>[e(p,{size:"20",icon:"tabler-eye"})]),_:1})])]),_:1})],64))}};export{G as _};