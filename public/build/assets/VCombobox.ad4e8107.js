import{m as ae}from"./VSelect.32c4b2dd.js";import{m as ne,u as ue}from"./filter.73bf6f77.js";import{m as oe}from"./VImg.134af49b.js";import{u as ie,t as A,V as se,a as M}from"./VList.a34172e8.js";import{f as re}from"./forwardRefs.c003b6b8.js";import{as as ce,aC as ve,aE as me,k as g,am as U,A as y,av as fe,ap as de,Z as T,ay as K,ae as pe,p as o,F as j,O as E,E as be}from"./main.cf9f31a9.js";import{m as he,f as Ve,V as ke}from"./VTextField.9102871e.js";import{V as xe}from"./VMenu.3df9bfa4.js";import{V as ge}from"./VCheckboxBtn.b3570faf.js";import{V as ye}from"./router.1658937d.js";import{V as Ce}from"./VChip.6c2e2c44.js";function _e(l,f,C){if(Array.isArray(f))throw new Error("Multiple matches is not implemented");return typeof f=="number"&&~f?o(j,null,[o("span",{class:"v-combobox__unmask"},[l.substr(0,f)]),o("span",{class:"v-combobox__mask"},[l.substr(f,C)]),o("span",{class:"v-combobox__unmask"},[l.substr(f+C)])]):l}const Le=ce()({name:"VCombobox",props:{delimiters:Array,...ne({filterKeys:["title"]}),...ae({hideNoData:!0,returnObject:!0}),...ve(he({modelValue:null}),["validationValue","dirty","appendInnerIcon"]),...oe({transition:!1})},emits:{"update:modelValue":l=>!0,"update:search":l=>!0,"update:menu":l=>!0},setup(l,f){var O;var C;let{emit:z,slots:s}=f;const{t:Z}=me(),d=g(),p=g(!1),b=g(!0),c=U(l,"menu"),a=g(-1),q=y(()=>{var e;return(e=d.value)==null?void 0:e.color}),{items:S,transformIn:G,transformOut:H}=ie(l),{textColorClasses:J,textColorStyles:Q}=fe(q),i=U(l,"modelValue",[],e=>G(de(e||[])),e=>{var t;const n=H(e);return l.multiple?n:(t=n[0])!=null?t:null}),k=g(l.multiple?"":(O=(C=i.value[0])==null?void 0:C.title)!=null?O:""),r=y({get:()=>k.value,set:e=>{var n;if(k.value=e,l.multiple||(i.value=[A(l,e)]),e&&l.multiple&&(n=l.delimiters)!=null&&n.length){const t=e.split(new RegExp(`(?:${l.delimiters.join("|")})+`));t.length>1&&(t.forEach(v=>{v=v.trim(),v&&h(A(l,v))}),k.value="")}e||(a.value=-1),p.value&&(c.value=!0),b.value=!e}});T(k,e=>{z("update:search",e)}),T(i,e=>{var t;if(!l.multiple){var n;k.value=(t=(n=e[0])==null?void 0:n.title)!=null?t:""}});const{filteredItems:D}=ue(l,S,y(()=>b.value?void 0:r.value)),x=y(()=>i.value.map(e=>S.value.find(n=>n.value===e.value)||e)),$=y(()=>x.value.map(e=>e.props.value)),L=y(()=>x.value[a.value]),R=g();function W(e){i.value=[],l.openOnClear&&(c.value=!0)}function N(){l.hideNoData&&!S.value.length||l.readonly||(c.value=!0)}function X(e){if(l.readonly)return;const n=d.value.selectionStart,t=$.value.length;if(a.value>-1&&e.preventDefault(),["Enter","ArrowDown"].includes(e.key)&&(c.value=!0),["Escape"].includes(e.key)&&(c.value=!1),["Enter","Escape","Tab"].includes(e.key)&&(b.value=!0),e.key==="ArrowDown"){var v;e.preventDefault(),(v=R.value)==null||v.focus("next")}else if(e.key==="ArrowUp"){var _;e.preventDefault(),(_=R.value)==null||_.focus("prev")}if(!!l.multiple){if(["Backspace","Delete"].includes(e.key)){if(a.value<0){e.key==="Backspace"&&!r.value&&(a.value=t-1);return}h(L.value),K(()=>!L.value&&(a.value=t-2))}if(e.key==="ArrowLeft"){if(a.value<0&&n>0)return;const u=a.value>-1?a.value-1:t-1;x.value[u]?a.value=u:(a.value=-1,d.value.setSelectionRange(r.value.length,r.value.length))}if(e.key==="ArrowRight"){if(a.value<0)return;const u=a.value+1;x.value[u]?a.value=u:(a.value=-1,d.value.setSelectionRange(0,0))}e.key==="Enter"&&(h(A(l,r.value)),r.value="")}}function Y(){p.value&&(b.value=!0)}function h(e){if(l.multiple){const n=$.value.findIndex(t=>t===e.value);if(n===-1)i.value=[...i.value,e];else{const t=[...i.value];t.splice(n,1),i.value=t}r.value=""}else i.value=[e],k.value=e.title,K(()=>{c.value=!1,b.value=!0})}function ee(e){p.value=!0}function le(e){if(e.relatedTarget==null){var n;(n=d.value)==null||n.focus()}}return T(D,e=>{!e.length&&l.hideNoData&&(c.value=!1)}),T(p,e=>{if(e)a.value=-1;else{if(c.value=!1,!l.multiple||!r.value)return;i.value=[...i.value,A(l,r.value)],r.value=""}}),pe(()=>{const e=!!(l.chips||s.chip),[n]=Ve(l);return o(ke,E({ref:d},n,{modelValue:r.value,"onUpdate:modelValue":[t=>r.value=t,t=>{t==null&&(i.value=[])}],validationValue:i.externalValue,dirty:i.value.length>0,class:["v-combobox",{"v-combobox--active-menu":c.value,"v-combobox--chips":!!l.chips,"v-combobox--selecting-index":a.value>-1,[`v-combobox--${l.multiple?"multiple":"single"}`]:!0}],appendInnerIcon:l.items.length?l.menuIcon:void 0,readonly:l.readonly,"onClick:clear":W,"onClick:control":N,"onClick:input":N,onFocus:()=>p.value=!0,onBlur:()=>p.value=!1,onKeydown:X}),{...s,default:()=>{var t,v,_;return o(j,null,[o(xe,E({modelValue:c.value,"onUpdate:modelValue":u=>c.value=u,activator:"parent",contentClass:"v-combobox__content",eager:l.eager,openOnClick:!1,closeOnContentClick:!1,transition:l.transition,onAfterLeave:Y},l.menuProps),{default:()=>[o(se,{ref:R,selected:$.value,selectStrategy:l.multiple?"independent":"single-independent",onMousedown:u=>u.preventDefault(),onFocusin:ee,onFocusout:le},{default:()=>{var u;return[!D.value.length&&!l.hideNoData&&((u=(t=s["no-data"])==null?void 0:t.call(s))!=null?u:o(M,{title:Z(l.noDataText)},null)),(v=s["prepend-item"])==null?void 0:v.call(s),D.value.map((V,F)=>{var B;var w;let{item:m,matches:te}=V;return(B=(w=s.item)==null?void 0:w.call(s,{item:m,index:F,props:E(m.props,{onClick:()=>h(m)})}))!=null?B:o(M,E({key:F},m.props,{onClick:()=>h(m)}),{prepend:I=>{let{isSelected:P}=I;return l.multiple&&!l.hideSelected?o(ge,{modelValue:P,ripple:!1},null):void 0},title:()=>{var P;var I;return b.value?m.title:_e(m.title,te.title,(P=(I=r.value)==null?void 0:I.length)!=null?P:0)}})}),(_=s["append-item"])==null?void 0:_.call(s)]}})]}),x.value.map((u,V)=>{function F(m){m.stopPropagation(),m.preventDefault(),h(u)}const w={"onClick:close":F,modelValue:!0,"onUpdate:modelValue":void 0};return o("div",{key:u.value,class:["v-combobox__selection",V===a.value&&["v-combobox__selection--selected",J.value]],style:V===a.value?Q.value:{}},[e?o(ye,{defaults:{VChip:{closable:l.closableChips,size:"small",text:u.title}}},{default:()=>[s.chip?s.chip({item:u,index:V,props:w}):o(Ce,w,null)]}):s.selection?s.selection({item:u,index:V}):o("span",{class:"v-combobox__selection-text"},[u.title,l.multiple&&V<x.value.length-1&&o("span",{class:"v-combobox__selection-comma"},[be(",")])])])})])}})}),re({isFocused:p,isPristine:b,menu:c,search:r,selectionIndex:a,filteredItems:D,select:h},d)}});export{Le as V};