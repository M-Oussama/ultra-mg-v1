import{as as E,az as B,at as H,a9 as D,aa as U,aE as j,ab as q,am as G,A as r,b8 as J,bp as K,k as _,ah as O,ae as Q,p as t,F,E as W}from"./main.80073fb3.js";import{a as X}from"./router.9f57d7dd.js";import{V as Y}from"./VBtn.bc513066.js";const ne=E()({name:"VRating",props:{name:String,itemAriaLabel:{type:String,default:"$vuetify.rating.ariaLabel.item"},activeColor:String,color:String,clearable:Boolean,disabled:Boolean,emptyIcon:{type:B,default:"$ratingEmpty"},fullIcon:{type:B,default:"$ratingFull"},halfIncrements:Boolean,hover:Boolean,length:{type:[Number,String],default:5},readonly:Boolean,modelValue:{type:[Number,String],default:0},itemLabels:Array,itemLabelPosition:{type:String,default:"top",validator:e=>["top","bottom"].includes(e)},ripple:Boolean,...X(),...H(),...D(),...U()},emits:{"update:modelValue":e=>!0},setup(e,C){let{slots:c}=C;const{t:P}=j(),{themeClasses:R}=q(e),L=G(e,"modelValue"),d=r(()=>J(parseFloat(L.value),0,+e.length)),I=r(()=>K(Number(e.length),1)),M=r(()=>I.value.flatMap(n=>e.halfIncrements?[n-.5,n]:[n])),m=_(-1),g=_(-1),S=_();let b=!1;const h=r(()=>M.value.map(n=>{var f;const i=e.hover&&m.value>-1,l=d.value>=n,a=m.value>=n,u=(i?a:l)?e.fullIcon:e.emptyIcon,s=(f=e.activeColor)!=null?f:e.color,y=l||a?s:e.color;return{isFilled:l,isHovered:a,icon:u,color:y}})),w=r(()=>[0,...M.value].map(n=>{function i(){m.value=n}function l(){m.value=-1}function a(){if(n===0&&d.value===0){var s;(s=S.value)==null||s.focus()}else g.value=n}function o(){b||(g.value=-1)}function u(){e.disabled||e.readonly||(L.value=d.value===n&&e.clearable?0:n)}return{onMouseenter:e.hover?i:void 0,onMouseleave:e.hover?l:void 0,onFocus:a,onBlur:o,onClick:u}}));function z(){b=!0}function A(){b=!1}const V=r(()=>{var n;return(n=e.name)!=null?n:`v-rating-${O()}`});function v(n){var i,l;let{value:a,index:o,showStar:u=!0}=n;const{onMouseenter:s,onMouseleave:y,onFocus:f,onBlur:N,onClick:T}=w.value[o+1],$=`${V.value}-${String(a).replace(".","-")}`,k={color:(i=h.value[o])==null?void 0:i.color,density:e.density,disabled:e.disabled,icon:(l=h.value[o])==null?void 0:l.icon,ripple:e.ripple,size:e.size,tag:"span",variant:"plain"};return t(F,null,[t("label",{for:$,class:{"v-rating__item--half":e.halfIncrements&&a%1>0,"v-rating__item--full":e.halfIncrements&&a%1===0},onMousedown:z,onMouseup:A,onMouseenter:s,onMouseleave:y},[t("span",{class:"v-rating__hidden"},[P(e.itemAriaLabel,a,e.length)]),u?c.item?c.item({...h.value[o],props:k,value:a,index:o}):t(Y,k,null):void 0]),t("input",{class:"v-rating__hidden",name:V.value,id:$,type:"radio",value:a,checked:d.value===a,onClick:T,onFocus:f,onBlur:N,ref:o===0?S:void 0,readonly:e.readonly,disabled:e.disabled},null)])}function x(n){return c["item-label"]?c["item-label"](n):n.label?t("span",null,[n.label]):t("span",null,[W("\xA0")])}return Q(()=>{var n;const i=!!((n=e.itemLabels)!=null&&n.length)||c["item-label"];return t(e.tag,{class:["v-rating",{"v-rating--hover":e.hover,"v-rating--readonly":e.readonly},R.value]},{default:()=>[t(v,{value:0,index:-1,showStar:!1},null),I.value.map((l,a)=>{var o,u;return t("div",{class:"v-rating__wrapper"},[i&&e.itemLabelPosition==="top"?x({value:l,index:a,label:(o=e.itemLabels)==null?void 0:o[a]}):void 0,t("div",{class:["v-rating__item",{"v-rating__item--focused":Math.ceil(g.value)===l}]},[e.halfIncrements?t(F,null,[t(v,{value:l-.5,index:a*2},null),t(v,{value:l,index:a*2+1},null)]):t(v,{value:l,index:a},null)]),i&&e.itemLabelPosition==="bottom"?x({value:l,index:a,label:(u=e.itemLabels)==null?void 0:u[a]}):void 0])})]})}),{}}});export{ne as V};