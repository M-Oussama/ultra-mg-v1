import{_ as s}from"./AppDateTimePicker.59b23bcb.js";import{k as u,o as l,b as n,q as i,D as d,w as m,p as o}from"./main.0cc37d68.js";import{_ as A}from"./AppCardCode.1055e9f4.js";import{a as c,V as b}from"./VRow.4110b62f.js";import"./VField.6f5675d6.js";import"./index.f155a171.js";import"./VInput.9827f2d3.js";import"./router.9dff56b1.js";import"./VImg.cc77002f.js";import"./position.9a91b720.js";import"./easing.36b781ab.js";import"./vue.runtime.esm-bundler.80aa88db.js";import"./VCard.82c7427c.js";import"./VAvatar.25b42a9b.js";import"./VBtn.bfc904f9.js";import"./VDivider.0832d40a.js";const v={__name:"DemoDateTimePickerInline",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Inline",config:{inline:!0}},null,8,["modelValue"]))}},F={__name:"DemoDateTimePickerDisabledRange",setup(p){const e=new Date,r=e.toLocaleString("default",{month:"2-digit"}),a=e.getFullYear(),t=u("");return(_,D)=>(l(),n(s,{modelValue:i(t),"onUpdate:modelValue":D[0]||(D[0]=f=>d(t)?t.value=f:null),label:"Disabled Range",config:{dateFormat:"Y-m-d",disable:[{from:`${i(a)}-${i(r)}-20`,to:`${i(a)}-${i(r)}-25`}]}},null,8,["modelValue","config"]))}},V={__name:"DemoDateTimePickerHumanFriendly",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Human Friendly",config:{altInput:!0,altFormat:"F j, Y",dateFormat:"Y-m-d"}},null,8,["modelValue"]))}},Y={__name:"DemoDateTimePickerRange",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Range",config:{mode:"range"}},null,8,["modelValue"]))}},$={__name:"DemoDateTimePickerMultipleDates",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Multiple Dates",config:{mode:"multiple",dateFormat:"Y-m-d"}},null,8,["modelValue"]))}},R={__name:"DemoDateTimePickerDateAndTime",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Date & TIme",config:{enableTime:!0,dateFormat:"Y-m-d H:i"}},null,8,["modelValue"]))}},w={__name:"DemoDateTimePickerTimePicker",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Time picker",config:{enableTime:!0,noCalendar:!0,dateFormat:"H:i"}},null,8,["modelValue"]))}},M={__name:"DemoDateTimePickerBasic",setup(p){const e=u("");return(r,a)=>(l(),n(s,{modelValue:i(e),"onUpdate:modelValue":a[0]||(a[0]=t=>d(e)?e.value=t:null),label:"Default"},null,8,["modelValue"]))}},H={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Default"
  />
</template>

`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Default"
  />
</template>

`},I={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Date & TIme"
    :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Date & TIme"
    :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
  />
</template>
`},j={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const now = new Date()
const currentMonth = now.toLocaleString('default', { month: '2-digit' })
const currentYear = now.getFullYear()
const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Disabled Range"
    :config="{ dateFormat: 'Y-m-d', disable: [{ from: \`\${currentYear}-\${currentMonth}-20\`, to: \`\${currentYear}-\${currentMonth}-25\` }] }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const now = new Date()
const currentMonth = now.toLocaleString('default', { month: '2-digit' })
const currentYear = now.getFullYear()
const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Disabled Range"
    :config="{ dateFormat: 'Y-m-d', disable: [{ from: \`\${currentYear}-\${currentMonth}-20\`, to: \`\${currentYear}-\${currentMonth}-25\` }] }"
  />
</template>
`},x={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Human Friendly"
    :config="{ altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Human Friendly"
    :config="{ altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }"
  />
</template>
`},U={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Inline"
    :config="{ inline: true }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const date = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Inline"
    :config="{ inline: true }"
  />
</template>
`},y={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const multipleDate = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="multipleDate"
    label="Multiple Dates"
    :config="{ mode: 'multiple', dateFormat: 'Y-m-d' }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const multipleDate = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="multipleDate"
    label="Multiple Dates"
    :config="{ mode: 'multiple', dateFormat: 'Y-m-d' }"
  />
</template>
`},C={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const dateRange = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="dateRange"
    label="Range"
    :config="{ mode: 'range' }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const dateRange = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="dateRange"
    label="Range"
    :config="{ mode: 'range' }"
  />
</template>
`},h={ts:`<script setup lang="ts">
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const time = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="time"
    label="Time picker"
    :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
  />
</template>
`,js:`<script setup>
import AppDateTimePicker from '@core/components/AppDateTimePicker.vue'

const time = ref('')
<\/script>

<template>
  <AppDateTimePicker
    v-model="time"
    label="Time picker"
    :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
  />
</template>
`},te={__name:"date-time-picker",setup(p){return(e,r)=>{const a=M,t=A,_=w,D=R,f=$,T=Y,k=V,P=F,g=v;return l(),n(b,null,{default:m(()=>[o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Basic",code:H},{default:m(()=>[o(a)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Time Picker",code:h},{default:m(()=>[o(_)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Date and Time",code:I},{default:m(()=>[o(D)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Multiple Dates",code:y},{default:m(()=>[o(f)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Range",code:C},{default:m(()=>[o(T)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Human Friendly",code:x},{default:m(()=>[o(k)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Disabled Range",code:j},{default:m(()=>[o(P)]),_:1},8,["code"])]),_:1}),o(c,{cols:"12",md:"6"},{default:m(()=>[o(t,{title:"Inline",code:U},{default:m(()=>[o(g)]),_:1},8,["code"])]),_:1})]),_:1})}}};export{te as default};
