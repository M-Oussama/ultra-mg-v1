import{k as m,r as v,o as k,b as x,w as s,p as e,m as l,q as t,V as y,v as P,be as C,E as _,C as I}from"./main.60095230.js";import{u,m as R,a as N}from"./useGenerateImageVariant.718fb920.js";import{b as p}from"./route-block.3d2a9a4d.js";import{V as f}from"./VImg.cdaed533.js";import{a as o,V as c}from"./VRow.8955dee0.js";import{V as B,c as w}from"./VCard.b8af7deb.js";import{V as T}from"./VForm.ec185ea1.js";import{V}from"./VTextField.9b7b3347.js";import{V as j}from"./VBtn.5ecb9490.js";import"./router.717d3535.js";import"./VAvatar.ad95b801.js";import"./position.e6185ce0.js";import"./VInput.c62758ee.js";import"./index.7d0b373a.js";import"./forwardRefs.c003b6b8.js";/* empty css                   */import"./VField.e7600162.js";import"./easing.36b781ab.js";import"./VCounter.5d1d4da1.js";const L="/build/assets/auth-v2-reset-password-illustration-dark.d3746a9f.png",M="/build/assets/auth-v2-reset-password-illustration-light.435863a4.png";const D={class:"position-relative auth-bg rounded-lg w-100 ma-8 me-0"},F={class:"d-flex align-center justify-center w-100 h-100"},S=l("h5",{class:"text-h5 font-weight-semibold mb-1"}," Reset Password \u{1F512} ",-1),U=l("p",{class:"mb-0"},[_(" for "),l("span",{class:"font-weight-bold"},"john.doe@email.com")],-1),$=l("span",null,"Back to login",-1),q={__name:"reset-password-v2",setup(E){const n=m({newPassword:"",confirmPassword:""}),b=u(M,L),h=u(N,R),i=m(!1),d=m(!1);return(G,a)=>{const g=v("RouterLink");return k(),x(c,{"no-gutters":"",class:"auth-wrapper"},{default:s(()=>[e(o,{md:"8",class:"d-none d-md-flex"},{default:s(()=>[l("div",D,[l("div",F,[e(f,{"max-width":"400",src:t(b),class:"auth-illustration mt-16 mb-2"},null,8,["src"])]),e(f,{class:"auth-footer-mask",src:t(h)},null,8,["src"])])]),_:1}),e(o,{cols:"12",md:"4",class:"auth-card-v2 d-flex align-center justify-center"},{default:s(()=>[e(B,{flat:"","max-width":500,class:"mt-12 mt-sm-0 pa-4"},{default:s(()=>[e(w,null,{default:s(()=>[e(t(y),{nodes:t(P).app.logo,class:"mb-6"},null,8,["nodes"]),S,U]),_:1}),e(w,null,{default:s(()=>[e(T,{onSubmit:a[4]||(a[4]=C(()=>{},["prevent"]))},{default:s(()=>[e(c,null,{default:s(()=>[e(o,{cols:"12"},{default:s(()=>[e(V,{modelValue:t(n).newPassword,"onUpdate:modelValue":a[0]||(a[0]=r=>t(n).newPassword=r),label:"New Password",type:t(i)?"text":"password","append-inner-icon":t(i)?"tabler-eye-off":"tabler-eye","onClick:appendInner":a[1]||(a[1]=r=>i.value=!t(i))},null,8,["modelValue","type","append-inner-icon"])]),_:1}),e(o,{cols:"12"},{default:s(()=>[e(V,{modelValue:t(n).confirmPassword,"onUpdate:modelValue":a[2]||(a[2]=r=>t(n).confirmPassword=r),label:"Confirm Password",type:t(d)?"text":"password","append-inner-icon":t(d)?"tabler-eye-off":"tabler-eye","onClick:appendInner":a[3]||(a[3]=r=>d.value=!t(d))},null,8,["modelValue","type","append-inner-icon"])]),_:1}),e(o,{cols:"12"},{default:s(()=>[e(j,{block:"",type:"submit"},{default:s(()=>[_(" Set New Password ")]),_:1})]),_:1}),e(o,{cols:"12"},{default:s(()=>[e(g,{class:"d-flex align-center justify-center",to:{name:"pages-authentication-login-v2"}},{default:s(()=>[e(I,{icon:"tabler-chevron-left",class:"flip-in-rtl"}),$]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})}}};typeof p=="function"&&p(q);export{q as default};