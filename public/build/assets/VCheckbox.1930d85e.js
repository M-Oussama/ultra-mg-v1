import{m as V,u as C,f as P,a as v}from"./VInput.8a9622b2.js";import{m as A,f as B,V as I}from"./VCheckboxBtn.8b0eba1a.js";import{a6 as F,ah as g,A as y,ae as D,aD as R,p as t,O as o}from"./main.3e4a7664.js";const j=F({name:"VCheckbox",inheritAttrs:!1,props:{...V(),...A()},emits:{"update:focused":e=>!0},setup(e,a){let{attrs:r,slots:s}=a;const{isFocused:u,focus:c,blur:n}=C(e),i=g(),d=y(()=>e.id||`checkbox-${i}`);return D(()=>{const[l,p]=R(r),[f,_]=P(e),[m,$]=B(e);return t(v,o({class:"v-checkbox"},l,f,{id:d.value,focused:u.value}),{...s,default:b=>{let{id:h,isDisabled:k,isReadonly:x}=b;return t(I,o(m,{id:h.value,disabled:k.value,readonly:x.value},p,{onFocus:c,onBlur:n}),s)}})}),{}}});export{j as V};