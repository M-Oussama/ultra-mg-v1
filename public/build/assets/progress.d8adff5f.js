import{V as n}from"./position.e593359d.js";import{k as V,an as C,ak as b,Z as B,o as d,c as g,p as e,q as t,D as y,w as a,m as r,x as P,L as h,E as o,b as k}from"./main.8fd45797.js";import{a as i}from"./VBtn.c93b5ba3.js";import{_ as M}from"./AppCardCode.dc9ff04b.js";import{a as v,V as D}from"./VRow.3025d440.js";import"./router.62fd2ce6.js";import"./vue.runtime.esm-bundler.bf92ea95.js";import"./index.96d998ca.js";import"./VCard.1123f389.js";import"./VAvatar.579b8c09.js";import"./VImg.3f9a9712.js";import"./VDivider.32b554c9.js";const U={class:"demo-space-y"},R={__name:"DemoProgressLinearBuffering",setup(m){const l=V(10),s=V(20),p=V(),u=()=>{clearInterval(p.value),p.value=setInterval(()=>{l.value+=Math.random()*(15-5)+5,s.value+=Math.random()*(15-5)+6},2e3)};return C(u),b(()=>{clearInterval(p.value)}),B(l,()=>{if(l.value<100)return!1;l.value=0,s.value=10,u()}),(f,c)=>(d(),g("div",U,[e(n,{modelValue:t(l),"onUpdate:modelValue":c[0]||(c[0]=_=>y(l)?l.value=_:null),color:"primary",height:"8","buffer-value":t(s)},null,8,["modelValue","buffer-value"]),e(n,{modelValue:t(l),"onUpdate:modelValue":c[1]||(c[1]=_=>y(l)?l.value=_:null),color:"primary",height:"8","buffer-value":t(s)},null,8,["modelValue","buffer-value"]),e(n,{modelValue:t(l),"onUpdate:modelValue":c[2]||(c[2]=_=>y(l)?l.value=_:null),"buffer-value":t(s),color:"primary",height:"8"},null,8,["modelValue","buffer-value"]),e(n,{modelValue:t(l),"onUpdate:modelValue":c[3]||(c[3]=_=>y(l)?l.value=_:null),"buffer-value":t(s),color:"primary",height:"8"},null,8,["modelValue","buffer-value"])]))}},j=r("br",null,null,-1),S=r("br",null,null,-1),T={__name:"DemoProgressLinearSlots",setup(m){const l=V(20),s=V(33),p=V(78);return(u,f)=>(d(),g("div",null,[e(n,{modelValue:t(p),"onUpdate:modelValue":f[0]||(f[0]=c=>y(p)?p.value=c:null),color:"primary",height:"8"},null,8,["modelValue"]),j,e(n,{modelValue:t(l),"onUpdate:modelValue":f[1]||(f[1]=c=>y(l)?l.value=c:null),color:"primary",height:"20"},{default:a(({value:c})=>[r("strong",null,P(Math.ceil(c))+"%",1)]),_:1},8,["modelValue"]),S,e(n,{modelValue:t(s),"onUpdate:modelValue":f[2]||(f[2]=c=>y(s)?s.value=c:null),height:"20",color:"primary"},{default:a(()=>[r("strong",null,P(Math.ceil(t(s)))+"%",1)]),_:1},8,["modelValue"])]))}},N={},A={class:"demo-space-y"};function E(m,l){return d(),g("div",A,[e(n,{"model-value":"100",color:"primary",rounded:""}),e(n,{"model-value":"100",color:"primary",rounded:""}),e(n,{"model-value":"100",color:"primary",rounded:""}),e(n,{"model-value":"100",color:"primary",rounded:""})])}const Y=h(N,[["render",E]]),q={},Z={class:"demo-space-y"};function F(m,l){return d(),g("div",Z,[e(n,{"model-value":"15",color:"primary",reverse:""}),e(n,{color:"primary",indeterminate:"",reverse:""}),e(n,{"model-value":"30","buffer-value":"55",color:"primary",reverse:"",streams:""})])}const G=h(q,[["render",F]]),H={},J={class:"demo-space-y"};function K(m,l){return d(),g("div",J,[e(n,{indeterminate:"",color:"primary"}),e(n,{indeterminate:"",color:"primary"}),e(n,{indeterminate:"",color:"primary"})])}const O=h(H,[["render",K]]),Q={},W={class:"demo-space-y"};function X(m,l){return d(),g("div",W,[e(n,{"model-value":"15","bg-color":"primary",color:"primary"}),e(n,{"model-value":"30","bg-color":"secondary",color:"secondary"}),e(n,{"model-value":"45","bg-color":"success",color:"success"})])}const ee=h(Q,[["render",X]]),re={},oe={class:"demo-space-x"};function le(m,l){return d(),g("div",oe,[e(i,{width:3,color:"primary",indeterminate:""}),e(i,{size:50,color:"primary",indeterminate:""}),e(i,{size:50,color:"primary",indeterminate:""}),e(i,{size:70,width:7,color:"primary",indeterminate:""})])}const ae=h(re,[["render",le]]),se={class:"demo-space-x"},te={__name:"DemoProgressCircularRotate",setup(m){const l=V(),s=V(0);return C(()=>{l.value=setInterval(()=>{if(s.value===100)return s.value=0;s.value+=10},1e3)}),b(()=>{clearInterval(l.value)}),(p,u)=>(d(),g("div",se,[e(i,{rotate:360,size:70,width:6,"model-value":t(s),color:"primary"},{default:a(()=>[o(P(t(s)),1)]),_:1},8,["model-value"]),e(i,{rotate:90,size:70,width:6,"model-value":t(s),color:"primary"},{default:a(()=>[o(P(t(s)),1)]),_:1},8,["model-value"]),e(i,{rotate:170,size:70,width:6,"model-value":t(s),color:"primary"},{default:a(()=>[o(P(t(s)),1)]),_:1},8,["model-value"]),e(i,{rotate:-90,size:70,width:6,"model-value":t(s),color:"primary"},{default:a(()=>[o(P(t(s)),1)]),_:1},8,["model-value"])]))}},ie={},ne={class:"demo-space-x"};function ce(m,l){return d(),g("div",ne,[e(i,{indeterminate:"",color:"primary"}),e(i,{indeterminate:"",color:"secondary"}),e(i,{indeterminate:"",color:"success"}),e(i,{indeterminate:"",color:"info"}),e(i,{indeterminate:"",color:"warning"}),e(i,{indeterminate:"",color:"error"})])}const ue=h(ie,[["render",ce]]),de={},me={class:"demo-space-x"};function pe(m,l){return d(),g("div",me,[e(i,{"model-value":"100",color:"primary"}),e(i,{"model-value":"80",color:"secondary"}),e(i,{"model-value":"60",color:"success"}),e(i,{"model-value":"40",color:"info"}),e(i,{"model-value":"20",color:"warning"}),e(i,{"model-value":"20",color:"error"})])}const ve=h(de,[["render",pe]]),ge={ts:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      model-value="100"
      color="primary"
    />

    <VProgressCircular
      model-value="80"
      color="secondary"
    />

    <VProgressCircular
      model-value="60"
      color="success"
    />

    <VProgressCircular
      model-value="40"
      color="info"
    />

    <VProgressCircular
      model-value="20"
      color="warning"
    />

    <VProgressCircular
      model-value="20"
      color="error"
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      model-value="100"
      color="primary"
    />

    <VProgressCircular
      model-value="80"
      color="secondary"
    />

    <VProgressCircular
      model-value="60"
      color="success"
    />

    <VProgressCircular
      model-value="40"
      color="info"
    />

    <VProgressCircular
      model-value="20"
      color="warning"
    />

    <VProgressCircular
      model-value="20"
      color="error"
    />
  </div>
</template>
`},fe={ts:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      indeterminate
      color="primary"
    />

    <VProgressCircular
      indeterminate
      color="secondary"
    />

    <VProgressCircular
      indeterminate
      color="success"
    />

    <VProgressCircular
      indeterminate
      color="info"
    />

    <VProgressCircular
      indeterminate
      color="warning"
    />

    <VProgressCircular
      indeterminate
      color="error"
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      indeterminate
      color="primary"
    />

    <VProgressCircular
      indeterminate
      color="secondary"
    />

    <VProgressCircular
      indeterminate
      color="success"
    />

    <VProgressCircular
      indeterminate
      color="info"
    />

    <VProgressCircular
      indeterminate
      color="warning"
    />

    <VProgressCircular
      indeterminate
      color="error"
    />
  </div>
</template>
`},_e={ts:`<script setup lang="ts">
const interval = ref()
const progressValue = ref(0)

onMounted(() => {
  interval.value = setInterval(() => {
    if (progressValue.value === 100)
      return progressValue.value = 0
    progressValue.value += 10
  }, 1000)
})

onBeforeUnmount(() => {
  clearInterval(interval.value)
})
<\/script>

<template>
  <div class="demo-space-x">
    <VProgressCircular
      :rotate="360"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="90"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="170"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="-90"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>
  </div>
</template>
`,js:`<script setup>
const interval = ref()
const progressValue = ref(0)

onMounted(() => {
  interval.value = setInterval(() => {
    if (progressValue.value === 100)
      return progressValue.value = 0
    progressValue.value += 10
  }, 1000)
})
onBeforeUnmount(() => {
  clearInterval(interval.value)
})
<\/script>

<template>
  <div class="demo-space-x">
    <VProgressCircular
      :rotate="360"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="90"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="170"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>

    <VProgressCircular
      :rotate="-90"
      :size="70"
      :width="6"
      :model-value="progressValue"
      color="primary"
    >
      {{ progressValue }}
    </VProgressCircular>
  </div>
</template>
`},Ve={ts:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      :width="3"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="50"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="50"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="70"
      :width="7"
      color="primary"
      indeterminate
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-x">
    <VProgressCircular
      :width="3"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="50"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="50"
      color="primary"
      indeterminate
    />

    <VProgressCircular
      :size="70"
      :width="7"
      color="primary"
      indeterminate
    />
  </div>
</template>
`},ye={ts:`<script setup lang="ts">
const modelValue = ref(10)
const bufferValue = ref(20)
const interval = ref()

const startBuffer = () => {
  clearInterval(interval.value)

  interval.value = setInterval(() => {
    modelValue.value += Math.random() * (15 - 5) + 5
    bufferValue.value += Math.random() * (15 - 5) + 6
  }, 2000)
}

onMounted(startBuffer)

onBeforeUnmount(() => {
  clearInterval(interval.value)
})

watch(modelValue, () => {
  if (modelValue.value < 100)
    return false

  modelValue.value = 0
  bufferValue.value = 10
  startBuffer()
})
<\/script>

<template>
  <div class="demo-space-y">
    <VProgressLinear
      v-model="modelValue"
      color="primary"
      height="8"
      :buffer-value="bufferValue"
    />

    <VProgressLinear
      v-model="modelValue"
      color="primary"
      height="8"
      :buffer-value="bufferValue"
    />

    <VProgressLinear
      v-model="modelValue"
      :buffer-value="bufferValue"
      color="primary"
      height="8"
    />

    <VProgressLinear
      v-model="modelValue"
      :buffer-value="bufferValue"
      color="primary"
      height="8"
    />
  </div>
</template>
`,js:`<script setup>
const modelValue = ref(10)
const bufferValue = ref(20)
const interval = ref()

const startBuffer = () => {
  clearInterval(interval.value)
  interval.value = setInterval(() => {
    modelValue.value += Math.random() * (15 - 5) + 5
    bufferValue.value += Math.random() * (15 - 5) + 6
  }, 2000)
}

onMounted(startBuffer)
onBeforeUnmount(() => {
  clearInterval(interval.value)
})
watch(modelValue, () => {
  if (modelValue.value < 100)
    return false
  modelValue.value = 0
  bufferValue.value = 10
  startBuffer()
})
<\/script>

<template>
  <div class="demo-space-y">
    <VProgressLinear
      v-model="modelValue"
      color="primary"
      height="8"
      :buffer-value="bufferValue"
    />

    <VProgressLinear
      v-model="modelValue"
      color="primary"
      height="8"
      :buffer-value="bufferValue"
    />

    <VProgressLinear
      v-model="modelValue"
      :buffer-value="bufferValue"
      color="primary"
      height="8"
    />

    <VProgressLinear
      v-model="modelValue"
      :buffer-value="bufferValue"
      color="primary"
      height="8"
    />
  </div>
</template>
`},he={ts:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="15"
      bg-color="primary"
      color="primary"
    />

    <VProgressLinear
      model-value="30"
      bg-color="secondary"
      color="secondary"
    />

    <VProgressLinear
      model-value="45"
      bg-color="success"
      color="success"
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="15"
      bg-color="primary"
      color="primary"
    />

    <VProgressLinear
      model-value="30"
      bg-color="secondary"
      color="secondary"
    />

    <VProgressLinear
      model-value="45"
      bg-color="success"
      color="success"
    />
  </div>
</template>
`},Pe={ts:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      indeterminate
      color="primary"
    />

    <VProgressLinear
      indeterminate
      color="primary"
    />

    <VProgressLinear
      indeterminate
      color="primary"
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      indeterminate
      color="primary"
    />

    <VProgressLinear
      indeterminate
      color="primary"
    />

    <VProgressLinear
      indeterminate
      color="primary"
    />
  </div>
</template>
`},Ce={ts:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="15"
      color="primary"
      reverse
    />

    <VProgressLinear
      color="primary"
      indeterminate
      reverse
    />

    <VProgressLinear
      model-value="30"
      buffer-value="55"
      color="primary"
      reverse
      streams
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="15"
      color="primary"
      reverse
    />

    <VProgressLinear
      color="primary"
      indeterminate
      reverse
    />

    <VProgressLinear
      model-value="30"
      buffer-value="55"
      color="primary"
      reverse
      streams
    />
  </div>
</template>
`},be={ts:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />
  </div>
</template>
`,js:`<template>
  <div class="demo-space-y">
    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />

    <VProgressLinear
      model-value="100"
      color="primary"
      rounded
    />
  </div>
</template>
`},Le={ts:`<script setup lang="ts">
const skill = ref(20)
const knowledge = ref(33)
const power = ref(78)
<\/script>

<template>
  <div>
    <VProgressLinear
      v-model="power"
      color="primary"
      height="8"
    />

    <br>

    <VProgressLinear
      v-model="skill"
      color="primary"
      height="20"
    >
      <template #default="{ value }">
        <strong>{{ Math.ceil(value) }}%</strong>
      </template>
    </VProgressLinear>

    <br>

    <VProgressLinear
      v-model="knowledge"
      height="20"
      color="primary"
    >
      <strong>{{ Math.ceil(knowledge) }}%</strong>
    </VProgressLinear>
  </div>
</template>
`,js:`<script setup>
const skill = ref(20)
const knowledge = ref(33)
const power = ref(78)
<\/script>

<template>
  <div>
    <VProgressLinear
      v-model="power"
      color="primary"
      height="8"
    />

    <br>

    <VProgressLinear
      v-model="skill"
      color="primary"
      height="20"
    >
      <template #default="{ value }">
        <strong>{{ Math.ceil(value) }}%</strong>
      </template>
    </VProgressLinear>

    <br>

    <VProgressLinear
      v-model="knowledge"
      height="20"
      color="primary"
    >
      <strong>{{ Math.ceil(knowledge) }}%</strong>
    </VProgressLinear>
  </div>
</template>
`},we=r("p",null,[o("Alternate colors can be applied to "),r("code",null,"v-progress-circular"),o(" using the "),r("code",null,"color"),o(" prop.")],-1),xe=r("p",null,[o("Using the "),r("code",null,"indeterminate"),o(" prop, a "),r("code",null,"v-progress-circular"),o(" continues to animate indefinitely.")],-1),$e=r("p",null,[o("The "),r("code",null,"rotate"),o(" prop gives you the ability to customize the "),r("code",null,"v-progress-circular"),o("'s origin.")],-1),ze=r("p",null,[o("The "),r("code",null,"size"),o(" and "),r("code",null,"width"),o(" props allow you to easily alter the size and width of the "),r("code",null,"v-progress-circular"),o(" component.")],-1),Ie=r("p",null,[o("You can set the colors of the progress bar using the props "),r("code",null,"color"),o(" and "),r("code",null,"background-color"),o(".")],-1),Be=r("p",null,[o("Using the "),r("code",null,"indeterminate"),o(" prop, "),r("code",null,"v-progress-linear"),o(" continuously animates.")],-1),ke=r("p",null,[o("Displays reversed progress. The component also has RTL support, such that a progress bar in right-to-left mode with reverse "),r("code",null,"prop"),o(" enabled will display left-to-right.")],-1),Me=r("p",null,[o("The rounded prop is used to apply a border radius to the "),r("code",null,"v-progress-linear"),o(" component.")],-1),De=r("p",null,[o("The "),r("code",null,"v-progress-linear"),o(" component will be responsive to user input when using "),r("code",null,"v-model"),o(". You can use the default slot or bind a local model to display inside of the progress.")],-1),Ue=r("p",null,[o("The primary value is controlled by "),r("code",null,"v-model"),o(", whereas the buffer is controlled by the "),r("code",null,"buffer-value"),o(" prop.")],-1),He={__name:"progress",setup(m){return(l,s)=>{const p=ve,u=M,f=ue,c=te,_=ae,L=ee,w=O,x=G,$=Y,z=T,I=R;return d(),k(D,null,{default:a(()=>[e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Circular Color",code:ge},{default:a(()=>[we,e(p)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Circular Indeterminate",code:fe},{default:a(()=>[xe,e(f)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Circular Rotate",code:_e},{default:a(()=>[$e,e(c)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Circular Size",code:Ve},{default:a(()=>[ze,e(_)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Color",code:he},{default:a(()=>[Ie,e(L)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Indeterminate",code:Pe},{default:a(()=>[Be,e(w)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Reversed",code:Ce},{default:a(()=>[ke,e(x)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Rounded",code:be},{default:a(()=>[Me,e($)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Slots",code:Le},{default:a(()=>[De,e(z)]),_:1},8,["code"])]),_:1}),e(v,{cols:"12",md:"6"},{default:a(()=>[e(u,{title:"Linear Buffering",code:ye},{default:a(()=>[Ue,e(I)]),_:1},8,["code"])]),_:1})]),_:1})}}};export{He as default};
