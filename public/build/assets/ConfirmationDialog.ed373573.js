import{_ as c}from"./DialogCloseBtn.0b4ca758.js";import{V as g,c as n}from"./VCard.bed48467.js";import{V as p}from"./VBtn.bc513066.js";import{V as D}from"./VDialog.809ecb4b.js";import{k as _,Z as y,o as C,b,w as o,p as a,E as r}from"./main.80073fb3.js";const $={__name:"ConfirmationDialog",props:{isDialogVisible:{type:Boolean,required:!0},employee:{type:Object,required:!0}},emits:["update:isDrawerOpen","employeeData"],setup(u,{emit:t}){const i=u,d=_(),f=()=>{t("update:isDialogVisible",!1)};y(()=>i.user,s=>{s&&(d.value=s.id||0)});const m=()=>{t("employeeData",i.employee),t("update:isDialogVisible",!1)};return(s,e)=>{const V=c;return C(),b(D,{modelValue:i.isDialogVisible,"onUpdate:modelValue":e[2]||(e[2]=l=>i.isDialogVisible=l),persistent:"",class:"v-dialog-sm"},{default:o(()=>[a(V,{onClick:e[0]||(e[0]=l=>f())}),a(g,{title:"Confirmation"},{default:o(()=>[a(n,null,{default:o(()=>[r(" Are you sure you want to delete this employee ? ")]),_:1}),a(n,{class:"d-flex justify-end gap-3 flex-wrap"},{default:o(()=>[a(p,{color:"secondary",variant:"tonal",onClick:e[1]||(e[1]=l=>t("update:isDialogVisible",!1))},{default:o(()=>[r(" Disagree ")]),_:1}),a(p,{onClick:m},{default:o(()=>[r(" Agree ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])}}};export{$ as _};