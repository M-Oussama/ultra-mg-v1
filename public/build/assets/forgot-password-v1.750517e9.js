import{k as f,r as _,o as V,c as h,m as s,p as t,q as o,w as e,V as b,v as n,E as i,x as g,be as v,C as x}from"./main.60095230.js";import{a as y,b as w}from"./auth-v1-top-shape.6dee6b8f.js";import{b as m}from"./route-block.3d2a9a4d.js";import{V as d}from"./VImg.cdaed533.js";import{a as k,b as C,c as p,V as B}from"./VCard.b8af7deb.js";import{V as R}from"./VForm.ec185ea1.js";import{V as S,a as l}from"./VRow.8955dee0.js";import{V as T}from"./VTextField.9b7b3347.js";import{V as E}from"./VBtn.5ecb9490.js";import"./router.717d3535.js";import"./VAvatar.ad95b801.js";import"./position.e6185ce0.js";import"./VInput.c62758ee.js";import"./index.7d0b373a.js";import"./forwardRefs.c003b6b8.js";/* empty css                   */import"./VField.e7600162.js";import"./easing.36b781ab.js";import"./VCounter.5d1d4da1.js";const N={class:"auth-wrapper d-flex align-center justify-center pa-4"},j={class:"position-relative my-sm-16"},F={class:"d-flex"},I=s("h5",{class:"text-h5 font-weight-semibold mb-1"}," Forgot Password? \u{1F512} ",-1),L=s("p",{class:"mb-0"}," Enter your email and we'll send you instructions to reset your password ",-1),P=s("span",null,"Back to login",-1),q={__name:"forgot-password-v1",setup(D){const r=f({email:""});return(M,a)=>{const c=_("RouterLink");return V(),h("div",N,[s("div",j,[t(d,{src:o(y),class:"auth-v1-top-shape d-none d-sm-block"},null,8,["src"]),t(d,{src:o(w),class:"auth-v1-bottom-shape d-none d-sm-block"},null,8,["src"]),t(B,{class:"auth-card pa-4","max-width":"448"},{default:e(()=>[t(k,{class:"justify-center"},{prepend:e(()=>[s("div",F,[t(o(b),{nodes:o(n).app.logo},null,8,["nodes"])])]),default:e(()=>[t(C,{class:"font-weight-bold text-h5 py-1"},{default:e(()=>[i(g(o(n).app.title),1)]),_:1})]),_:1}),t(p,{class:"pt-2"},{default:e(()=>[I,L]),_:1}),t(p,null,{default:e(()=>[t(R,{onSubmit:a[1]||(a[1]=v(()=>{},["prevent"]))},{default:e(()=>[t(S,null,{default:e(()=>[t(l,{cols:"12"},{default:e(()=>[t(T,{modelValue:o(r).email,"onUpdate:modelValue":a[0]||(a[0]=u=>o(r).email=u),label:"Email",type:"email"},null,8,["modelValue"])]),_:1}),t(l,{cols:"12"},{default:e(()=>[t(E,{block:"",type:"submit"},{default:e(()=>[i(" Send Reset Link ")]),_:1})]),_:1}),t(l,{cols:"12"},{default:e(()=>[t(c,{class:"d-flex align-center justify-center",to:{name:"pages-authentication-login-v1"}},{default:e(()=>[t(x,{icon:"tabler-chevron-left",class:"flip-in-rtl"}),P]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})])])}}};typeof m=="function"&&m(q);export{q as default};