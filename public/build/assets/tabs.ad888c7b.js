import{a as T,V as D}from"./VRow.bb367534.js";import{V as v,a as u}from"./VTabs.b3da1f09.js";import{k as I,o as r,b as C,w as t,p as e,q as d,D as V,C as y,E as s,m as i,c as p,F as b,a as h,x as k,Z as U,L as P}from"./main.3e4a7664.js";import{V as j,c as F}from"./VCard.5e5240e5.js";import{V as x,a as w}from"./VWindowItem.2945a511.js";import{V as g}from"./VDivider.b2cf7867.js";import{V as W}from"./VBtn.cda7f3ff.js";import{_ as R}from"./AppCardCode.d2c02027.js";import"./router.edbbf26c.js";import"./easing.36b781ab.js";import"./VSlideGroup.01314c29.js";import"./index.969717a5.js";import"./VAvatar.8e1eaad0.js";import"./VImg.010fd1a5.js";import"./position.7ef297c0.js";import"./lazy.a71a3757.js";import"./ssrBoot.4614d505.js";import"./vue.runtime.esm-bundler.fc928528.js";const M=i("p",null," Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero. ",-1),E=i("p",{class:"mb-0"}," Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio. ",-1),G=i("p",null," Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis. ",-1),H=i("p",{class:"mb-0"}," Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. ",-1),L=i("p",null," Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. ",-1),Z=i("p",{class:"mb-0"}," Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis. ",-1),J={__name:"DemoTabsVerticalPill",setup(f){const a=I(0);return(l,m)=>(r(),C(D,null,{default:t(()=>[e(T,{cols:"5",sm:"4"},{default:t(()=>[e(v,{modelValue:d(a),"onUpdate:modelValue":m[0]||(m[0]=o=>V(a)?a.value=o:null),direction:"vertical",class:"v-tabs-pill"},{default:t(()=>[e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-user"}),s(" Option 1 ")]),_:1}),e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-lock"}),s(" Option 2 ")]),_:1}),e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-access-point"}),s(" Option 3 ")]),_:1})]),_:1},8,["modelValue"])]),_:1}),e(T,{cols:"7",sm:"8"},{default:t(()=>[e(j,null,{default:t(()=>[e(F,null,{default:t(()=>[e(x,{modelValue:d(a),"onUpdate:modelValue":m[1]||(m[1]=o=>V(a)?a.value=o:null)},{default:t(()=>[e(w,null,{default:t(()=>[M,E]),_:1}),e(w,null,{default:t(()=>[G,H]),_:1}),e(w,null,{default:t(()=>[L,Z]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1})]),_:1})]),_:1}))}},K={__name:"DemoTabsBasicPill",setup(f){const a=I(0),l="Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.";return(m,o)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":o[0]||(o[0]=n=>V(a)?a.value=n:null),class:"v-tabs-pill"},{default:t(()=>[e(u,null,{default:t(()=>[s("Tab One")]),_:1}),e(u,null,{default:t(()=>[s("Tab Two")]),_:1}),e(u,null,{default:t(()=>[s("Tab Three")]),_:1})]),_:1},8,["modelValue"]),e(j,{class:"mt-5"},{default:t(()=>[e(F,null,{default:t(()=>[e(x,{modelValue:d(a),"onUpdate:modelValue":o[1]||(o[1]=n=>V(a)?a.value=n:null)},{default:t(()=>[(r(),p(b,null,h(3,n=>e(w,{key:n},{default:t(()=>[s(k(l))]),_:2},1024)),64))]),_:1},8,["modelValue"])]),_:1})]),_:1})],64))}},Q={class:"text-center mt-9"},X={__name:"DemoTabsDynamic",setup(f){const a=I(3),l=I(0);return U(a,m=>{l.value=m-1}),(m,o)=>(r(),p(b,null,[e(v,{modelValue:d(l),"onUpdate:modelValue":o[0]||(o[0]=n=>V(l)?l.value=n:null)},{default:t(()=>[(r(!0),p(b,null,h(d(a),n=>(r(),C(u,{key:n,value:n},{default:t(()=>[s(" Tab "+k(n),1)]),_:2},1032,["value"]))),128))]),_:1},8,["modelValue"]),e(g),i("div",Q,[e(W,{disabled:!d(a),variant:"text",onClick:o[1]||(o[1]=n=>a.value--)},{default:t(()=>[s(" Remove Tab ")]),_:1},8,["disabled"]),e(W,{variant:"text",onClick:o[2]||(o[2]=n=>a.value++)},{default:t(()=>[s(" Add Tab ")]),_:1})])],64))}},Y={class:"text-center"},ee={__name:"DemoTabsProgrammaticNavigation",setup(f){const a=I(0),l=["Appetizers","Entrees","Deserts","Cocktails"],m="Chocolate cake marshmallow toffee sweet caramels tootsie roll chocolate bar. Chocolate candy lemon drops cupcake macaroon liquorice. Icing tiramisu cake pastry jujubes lollipop gummies sugar plum pie.",o=l.length,n=()=>{a.value!==1&&(a.value-=1)},c=()=>{a.value!==o&&(a.value+=1)};return(S,q)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":q[0]||(q[0]=_=>V(a)?a.value=_:null),grow:""},{default:t(()=>[(r(!0),p(b,null,h(l.length,_=>(r(),C(u,{key:_,value:_},{default:t(()=>[s(k(l[_-1]),1)]),_:2},1032,["value"]))),128))]),_:1},8,["modelValue"]),e(g),e(x,{modelValue:d(a),"onUpdate:modelValue":q[1]||(q[1]=_=>V(a)?a.value=_:null),class:"mt-5"},{default:t(()=>[(r(!0),p(b,null,h(l.length,_=>(r(),C(w,{key:_,value:_},{default:t(()=>[s(k(m))]),_:2},1032,["value"]))),128))]),_:1},8,["modelValue"]),i("div",Y,[e(W,{variant:"text",disabled:d(a)===1,onClick:n},{default:t(()=>[s(" Previous ")]),_:1},8,["disabled"]),e(W,{variant:"text",disabled:d(a)===d(o),onClick:c},{default:t(()=>[s(" Next ")]),_:1},8,["disabled"])])],64))}},te={__name:"DemoTabsGrow",setup(f){const a=I("Appetizers"),l=["Appetizers","Entrees","Deserts","Cocktails"],m="hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.";return(o,n)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":n[0]||(n[0]=c=>V(a)?a.value=c:null),grow:""},{default:t(()=>[(r(),p(b,null,h(l,c=>e(u,{key:c,value:c},{default:t(()=>[s(k(c),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),e(g),e(x,{modelValue:d(a),"onUpdate:modelValue":n[1]||(n[1]=c=>V(a)?a.value=c:null),class:"mt-6"},{default:t(()=>[(r(),p(b,null,h(l,c=>e(w,{key:c,value:c},{default:t(()=>[s(k(m))]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"])],64))}},ae={__name:"DemoTabsFixed",setup(f){const a=I("Appetizers"),l=["Fixed Tab 1","Fixed Tab 2","Fixed Tab 3","Fixed Tab 4"],m="hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.";return(o,n)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":n[0]||(n[0]=c=>V(a)?a.value=c:null),"fixed-tabs":""},{default:t(()=>[(r(),p(b,null,h(l,c=>e(u,{key:c,value:c},{default:t(()=>[s(k(c),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),e(g),e(x,{modelValue:d(a),"onUpdate:modelValue":n[1]||(n[1]=c=>V(a)?a.value=c:null),class:"mt-6"},{default:t(()=>[(r(),p(b,null,h(l,c=>e(w,{key:c,value:c},{default:t(()=>[s(k(m))]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"])],64))}},se={};function oe(f,a){return r(),p(b,null,[e(v,{"next-icon":"tabler-arrow-right","prev-icon":"tabler-arrow-left"},{default:t(()=>[(r(),p(b,null,h(10,l=>e(u,{key:l},{default:t(()=>[s(" Item "+k(l),1)]),_:2},1024)),64))]),_:1}),e(g)],64)}const ie=P(se,[["render",oe]]),ne={};function le(f,a){return r(),p(b,null,[e(v,null,{default:t(()=>[(r(),p(b,null,h(10,l=>e(u,{key:l,value:l},{default:t(()=>[s(" Item "+k(l),1)]),_:2},1032,["value"])),64))]),_:1}),e(g)],64)}const ue=P(ne,[["render",le]]),re={},ce={class:"d-flex flex-column gap-4"};function me(f,a){return r(),p("div",ce,[i("div",null,[e(v,null,{default:t(()=>[e(u,null,{default:t(()=>[s("Home")]),_:1}),e(u,null,{default:t(()=>[s("Service")]),_:1}),e(u,null,{default:t(()=>[s("Account")]),_:1})]),_:1}),e(g)]),i("div",null,[e(v,{centered:""},{default:t(()=>[e(u,null,{default:t(()=>[s("Home")]),_:1}),e(u,null,{default:t(()=>[s("Service")]),_:1}),e(u,null,{default:t(()=>[s("Account")]),_:1})]),_:1}),e(g)]),i("div",null,[e(v,{end:""},{default:t(()=>[e(u,null,{default:t(()=>[s("Home")]),_:1}),e(u,null,{default:t(()=>[s("Service")]),_:1}),e(u,null,{default:t(()=>[s("Account")]),_:1})]),_:1}),e(g)])])}const de=P(re,[["render",me]]),pe=i("p",null," Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero. ",-1),be=i("p",{class:"mb-0"}," Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio. ",-1),Ve=i("p",null," Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis. ",-1),Te=i("p",{class:"mb-0"}," Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. ",-1),ve=i("p",null," Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. ",-1),fe=i("p",{class:"mb-0"}," Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis. ",-1),_e={__name:"DemoTabsVertical",setup(f){const a=I(0);return(l,m)=>(r(),C(D,null,{default:t(()=>[e(T,{cols:"5",sm:"4"},{default:t(()=>[e(v,{modelValue:d(a),"onUpdate:modelValue":m[0]||(m[0]=o=>V(a)?a.value=o:null),direction:"vertical"},{default:t(()=>[e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-user"}),s(" Option 1 ")]),_:1}),e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-lock"}),s(" Option 2 ")]),_:1}),e(u,null,{default:t(()=>[e(y,{start:"",icon:"tabler-access-point"}),s(" Option 3 ")]),_:1})]),_:1},8,["modelValue"])]),_:1}),e(g,{vertical:""}),e(T,{cols:"7",sm:"8"},{default:t(()=>[e(x,{modelValue:d(a),"onUpdate:modelValue":m[1]||(m[1]=o=>V(a)?a.value=o:null),class:"ms-3"},{default:t(()=>[e(w,null,{default:t(()=>[pe,be]),_:1}),e(w,null,{default:t(()=>[Ve,Te]),_:1}),e(w,null,{default:t(()=>[ve,fe]),_:1})]),_:1},8,["modelValue"])]),_:1})]),_:1}))}},we=i("span",null,"Recent",-1),ge=i("span",null,"Favorites",-1),he=i("span",null,"Nearby",-1),ke={__name:"DemoTabsStacked",setup(f){const a=I("tab-1"),l="Biscuit cheesecake gingerbread oat cake tiramisu. Marzipan tiramisu jelly-o muffin biscuit jelly cake pie. Chocolate cookie candy croissant brownie cupcake powder cheesecake. Biscuit sesame snaps biscuit topping tiramisu croissant.";return(m,o)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":o[0]||(o[0]=n=>V(a)?a.value=n:null),grow:"",stacked:""},{default:t(()=>[e(u,{value:"tab-1"},{default:t(()=>[e(y,{icon:"tabler-phone",class:"mb-2"}),we]),_:1}),e(u,{value:"tab-2"},{default:t(()=>[e(y,{icon:"tabler-heart",class:"mb-2"}),ge]),_:1}),e(u,{value:"tab-3"},{default:t(()=>[e(y,{icon:"tabler-user",class:"mb-2"}),he]),_:1})]),_:1},8,["modelValue"]),e(g),e(x,{modelValue:d(a),"onUpdate:modelValue":o[1]||(o[1]=n=>V(a)?a.value=n:null),class:"mt-5"},{default:t(()=>[(r(),p(b,null,h(3,n=>e(w,{key:n,value:`tab-${n}`},{default:t(()=>[s(k(l))]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"])],64))}},Ie={__name:"DemoTabsBasic",setup(f){const a=I(0),l="Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.";return(m,o)=>(r(),p(b,null,[e(v,{modelValue:d(a),"onUpdate:modelValue":o[0]||(o[0]=n=>V(a)?a.value=n:null)},{default:t(()=>[e(u,null,{default:t(()=>[s("Tab One")]),_:1}),e(u,null,{default:t(()=>[s("Tab Two")]),_:1}),e(u,null,{default:t(()=>[s("Tab Three")]),_:1})]),_:1},8,["modelValue"]),e(g),e(x,{modelValue:d(a),"onUpdate:modelValue":o[1]||(o[1]=n=>V(a)?a.value=n:null),class:"mt-5"},{default:t(()=>[(r(),p(b,null,h(3,n=>e(w,{key:n},{default:t(()=>[s(k(l))]),_:2},1024)),64))]),_:1},8,["modelValue"])],64))}},ye={ts:`<template>
  <div class="d-flex flex-column gap-4">
    <!-- Default -->
    <div>
      <VTabs>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>

    <!-- Center -->
    <div>
      <VTabs centered>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>

    <!-- Right -->
    <div>
      <VTabs end>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>
  </div>
</template>
`,js:`<template>
  <div class="d-flex flex-column gap-4">
    <!-- Default -->
    <div>
      <VTabs>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>

    <!-- Center -->
    <div>
      <VTabs centered>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>

    <!-- Right -->
    <div>
      <VTabs end>
        <VTab>Home</VTab>
        <VTab>Service</VTab>
        <VTab>Account</VTab>
      </VTabs>
      <VDivider />
    </div>
  </div>
</template>
`},xe={ts:`<script lang="ts" setup>
const currentTab = ref(0)
const tabItemContent = 'Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.'
<\/script>

<template>
  <VTabs v-model="currentTab">
    <VTab>Tab One</VTab>
    <VTab>Tab Two</VTab>
    <VTab>Tab Three</VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="item in 3"
      :key="item"
    >
      {{ tabItemContent }}
    </VWindowItem>
  </VWindow>
</template>
`,js:`<script setup>
const currentTab = ref(0)
const tabItemContent = 'Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.'
<\/script>

<template>
  <VTabs v-model="currentTab">
    <VTab>Tab One</VTab>
    <VTab>Tab Two</VTab>
    <VTab>Tab Three</VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="item in 3"
      :key="item"
    >
      {{ tabItemContent }}
    </VWindowItem>
  </VWindow>
</template>
`},qe={ts:`<script lang="ts" setup>
const currentTab = ref(0)
const tabItemContent = 'Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    class="v-tabs-pill"
  >
    <VTab>Tab One</VTab>
    <VTab>Tab Two</VTab>
    <VTab>Tab Three</VTab>
  </VTabs>

  <VCard class="mt-5">
    <VCardText>
      <VWindow v-model="currentTab">
        <VWindowItem
          v-for="item in 3"
          :key="item"
        >
          {{ tabItemContent }}
        </VWindowItem>
      </VWindow>
    </VCardText>
  </VCard>
</template>
`,js:`<script setup>
const currentTab = ref(0)
const tabItemContent = 'Candy canes donut chupa chups candy canes lemon drops oat cake wafer. Cotton candy candy canes marzipan carrot cake. Sesame snaps lemon drops candy marzipan donut brownie tootsie roll. Icing croissant bonbon biscuit gummi bears. Pudding candy canes sugar plum cookie chocolate cake powder croissant.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    class="v-tabs-pill"
  >
    <VTab>Tab One</VTab>
    <VTab>Tab Two</VTab>
    <VTab>Tab Three</VTab>
  </VTabs>

  <VCard class="mt-5">
    <VCardText>
      <VWindow v-model="currentTab">
        <VWindowItem
          v-for="item in 3"
          :key="item"
        >
          {{ tabItemContent }}
        </VWindowItem>
      </VWindow>
    </VCardText>
  </VCard>
</template>
`},Ce={ts:`<template>
  <VTabs
    next-icon="tabler-arrow-right"
    prev-icon="tabler-arrow-left"
  >
    <VTab
      v-for="i in 10"
      :key="i"
    >
      Item {{ i }}
    </VTab>
  </VTabs>
  <VDivider />
</template>
`,js:`<template>
  <VTabs
    next-icon="tabler-arrow-right"
    prev-icon="tabler-arrow-left"
  >
    <VTab
      v-for="i in 10"
      :key="i"
    >
      Item {{ i }}
    </VTab>
  </VTabs>
  <VDivider />
</template>
`},We={ts:`<script lang="ts" setup>
const totalTabs = ref(3)
const currentTab = ref(0)

watch(totalTabs, newValue => {
  currentTab.value = newValue - 1
})
<\/script>

<template>
  <VTabs v-model="currentTab">
    <VTab
      v-for="n in totalTabs"
      :key="n"
      :value="n"
    >
      Tab {{ n }}
    </VTab>
  </VTabs>
  <VDivider />

  <!-- buttons -->
  <div class="text-center mt-9">
    <VBtn
      :disabled="!totalTabs"
      variant="text"
      @click="totalTabs--"
    >
      Remove Tab
    </VBtn>

    <VBtn
      variant="text"
      @click="totalTabs++"
    >
      Add Tab
    </VBtn>
  </div>
</template>
`,js:`<script setup>
const totalTabs = ref(3)
const currentTab = ref(0)

watch(totalTabs, newValue => {
  currentTab.value = newValue - 1
})
<\/script>

<template>
  <VTabs v-model="currentTab">
    <VTab
      v-for="n in totalTabs"
      :key="n"
      :value="n"
    >
      Tab {{ n }}
    </VTab>
  </VTabs>
  <VDivider />

  <!-- buttons -->
  <div class="text-center mt-9">
    <VBtn
      :disabled="!totalTabs"
      variant="text"
      @click="totalTabs--"
    >
      Remove Tab
    </VBtn>

    <VBtn
      variant="text"
      @click="totalTabs++"
    >
      Add Tab
    </VBtn>
  </div>
</template>
`},De={ts:`<script lang="ts" setup>
const currentTab = ref('Appetizers')
const items = ['Fixed Tab 1', 'Fixed Tab 2', 'Fixed Tab 3', 'Fixed Tab 4']
const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    fixed-tabs
  >
    <VTab
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ item }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-6"
  >
    <VWindowItem
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`,js:`<script setup>
const currentTab = ref('Appetizers')

const items = [
  'Fixed Tab 1',
  'Fixed Tab 2',
  'Fixed Tab 3',
  'Fixed Tab 4',
]

const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    fixed-tabs
  >
    <VTab
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ item }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-6"
  >
    <VWindowItem
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`},Pe={ts:`<script lang="ts" setup>
const currentTab = ref('Appetizers')
const items = ['Appetizers', 'Entrees', 'Deserts', 'Cocktails']
const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
  >
    <VTab
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ item }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-6"
  >
    <VWindowItem
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`,js:`<script setup>
const currentTab = ref('Appetizers')

const items = [
  'Appetizers',
  'Entrees',
  'Deserts',
  'Cocktails',
]

const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice drag\xE9e macaroon.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
  >
    <VTab
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ item }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-6"
  >
    <VWindowItem
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`},Se={ts:`<template>
  <VTabs>
    <VTab
      v-for="i in 10"
      :key="i"
      :value="i"
    >
      Item {{ i }}
    </VTab>
  </VTabs>
  <VDivider />
</template>
`,js:`<template>
  <VTabs>
    <VTab
      v-for="i in 10"
      :key="i"
      :value="i"
    >
      Item {{ i }}
    </VTab>
  </VTabs>
  <VDivider />
</template>
`},je={ts:`<script lang="ts" setup>
const currentTab = ref(0)
const items = ['Appetizers', 'Entrees', 'Deserts', 'Cocktails']
const tabItemText = 'Chocolate cake marshmallow toffee sweet caramels tootsie roll chocolate bar. Chocolate candy lemon drops cupcake macaroon liquorice. Icing tiramisu cake pastry jujubes lollipop gummies sugar plum pie.'
const totalTabs = items.length

const preTab = () => {
  if (currentTab.value !== 1)
    currentTab.value -= 1
}

const nextTab = () => {
  if (currentTab.value !== totalTabs)
    currentTab.value += 1
}
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
  >
    <VTab
      v-for="item in items.length"
      :key="item"
      :value="item"
    >
      {{ items[item - 1] }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="item in items.length"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>

  <div class="text-center">
    <VBtn
      variant="text"
      :disabled="currentTab === 1"
      @click="preTab"
    >
      Previous
    </VBtn>

    <VBtn
      variant="text"
      :disabled="currentTab === totalTabs"
      @click="nextTab"
    >
      Next
    </VBtn>
  </div>
</template>
`,js:`<script setup>
const currentTab = ref(0)

const items = [
  'Appetizers',
  'Entrees',
  'Deserts',
  'Cocktails',
]

const tabItemText = 'Chocolate cake marshmallow toffee sweet caramels tootsie roll chocolate bar. Chocolate candy lemon drops cupcake macaroon liquorice. Icing tiramisu cake pastry jujubes lollipop gummies sugar plum pie.'
const totalTabs = items.length

const preTab = () => {
  if (currentTab.value !== 1)
    currentTab.value -= 1
}

const nextTab = () => {
  if (currentTab.value !== totalTabs)
    currentTab.value += 1
}
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
  >
    <VTab
      v-for="item in items.length"
      :key="item"
      :value="item"
    >
      {{ items[item - 1] }}
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="item in items.length"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>

  <div class="text-center">
    <VBtn
      variant="text"
      :disabled="currentTab === 1"
      @click="preTab"
    >
      Previous
    </VBtn>

    <VBtn
      variant="text"
      :disabled="currentTab === totalTabs"
      @click="nextTab"
    >
      Next
    </VBtn>
  </div>
</template>
`},Fe={ts:`<script lang="ts" setup>
const currentTab = ref('tab-1')
const tabItemText = 'Biscuit cheesecake gingerbread oat cake tiramisu. Marzipan tiramisu jelly-o muffin biscuit jelly cake pie. Chocolate cookie candy croissant brownie cupcake powder cheesecake. Biscuit sesame snaps biscuit topping tiramisu croissant.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
    stacked
  >
    <VTab value="tab-1">
      <VIcon
        icon="tabler-phone"
        class="mb-2"
      />
      <span>Recent</span>
    </VTab>

    <VTab value="tab-2">
      <VIcon
        icon="tabler-heart"
        class="mb-2"
      />
      <span>Favorites</span>
    </VTab>

    <VTab value="tab-3">
      <VIcon
        icon="tabler-user"
        class="mb-2"
      />
      <span>Nearby</span>
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="i in 3"
      :key="i"
      :value="\`tab-\${i}\`"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`,js:`<script setup>
const currentTab = ref('tab-1')
const tabItemText = 'Biscuit cheesecake gingerbread oat cake tiramisu. Marzipan tiramisu jelly-o muffin biscuit jelly cake pie. Chocolate cookie candy croissant brownie cupcake powder cheesecake. Biscuit sesame snaps biscuit topping tiramisu croissant.'
<\/script>

<template>
  <VTabs
    v-model="currentTab"
    grow
    stacked
  >
    <VTab value="tab-1">
      <VIcon
        icon="tabler-phone"
        class="mb-2"
      />
      <span>Recent</span>
    </VTab>

    <VTab value="tab-2">
      <VIcon
        icon="tabler-heart"
        class="mb-2"
      />
      <span>Favorites</span>
    </VTab>

    <VTab value="tab-3">
      <VIcon
        icon="tabler-user"
        class="mb-2"
      />
      <span>Nearby</span>
    </VTab>
  </VTabs>
  <VDivider />

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="i in 3"
      :key="i"
      :value="\`tab-\${i}\`"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
`},$e={ts:`<script setup lang="ts">
const currentTab = ref(0)
<\/script>

<template>
  <VRow>
    <VCol
      cols="5"
      sm="4"
    >
      <VTabs
        v-model="currentTab"
        direction="vertical"
      >
        <VTab>
          <VIcon
            start
            icon="tabler-user"
          />
          Option 1
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-lock"
          />
          Option 2
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-access-point"
          />
          Option 3
        </VTab>
      </VTabs>
    </VCol>

    <VDivider vertical />

    <VCol
      cols="7"
      sm="8"
    >
      <VWindow
        v-model="currentTab"
        class="ms-3"
      >
        <VWindowItem>
          <p>
            Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero.
          </p>

          <p class="mb-0">
            Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
          </p>

          <p class="mb-0">
            Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.
          </p>

          <p class="mb-0">
            Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis.
          </p>
        </VWindowItem>
      </VWindow>
    </VCol>
  </VRow>
</template>
`,js:`<script setup>
const currentTab = ref(0)
<\/script>

<template>
  <VRow>
    <VCol
      cols="5"
      sm="4"
    >
      <VTabs
        v-model="currentTab"
        direction="vertical"
      >
        <VTab>
          <VIcon
            start
            icon="tabler-user"
          />
          Option 1
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-lock"
          />
          Option 2
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-access-point"
          />
          Option 3
        </VTab>
      </VTabs>
    </VCol>

    <VDivider vertical />

    <VCol
      cols="7"
      sm="8"
    >
      <VWindow
        v-model="currentTab"
        class="ms-3"
      >
        <VWindowItem>
          <p>
            Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero.
          </p>

          <p class="mb-0">
            Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
          </p>

          <p class="mb-0">
            Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.
          </p>

          <p class="mb-0">
            Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis.
          </p>
        </VWindowItem>
      </VWindow>
    </VCol>
  </VRow>
</template>
`},Ae={ts:`<script setup lang="ts">
const currentTab = ref(0)
<\/script>

<template>
  <VRow>
    <VCol
      cols="5"
      sm="4"
    >
      <VTabs
        v-model="currentTab"
        direction="vertical"
        class="v-tabs-pill"
      >
        <VTab>
          <VIcon
            start
            icon="tabler-user"
          />
          Option 1
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-lock"
          />
          Option 2
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-access-point"
          />
          Option 3
        </VTab>
      </VTabs>
    </VCol>

    <VCol
      cols="7"
      sm="8"
    >
      <VCard>
        <VCardText>
          <VWindow v-model="currentTab">
            <VWindowItem>
              <p>
                Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero.
              </p>

              <p class="mb-0">
                Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio.
              </p>
            </VWindowItem>

            <VWindowItem>
              <p>
                Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
              </p>

              <p class="mb-0">
                Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
              </p>
            </VWindowItem>

            <VWindowItem>
              <p>
                Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.
              </p>

              <p class="mb-0">
                Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis.
              </p>
            </VWindowItem>
          </VWindow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
`,js:`<script setup>
const currentTab = ref(0)
<\/script>

<template>
  <VRow>
    <VCol
      cols="5"
      sm="4"
    >
      <VTabs
        v-model="currentTab"
        direction="vertical"
        class="v-tabs-pill"
      >
        <VTab>
          <VIcon
            start
            icon="tabler-user"
          />
          Option 1
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-lock"
          />
          Option 2
        </VTab>

        <VTab>
          <VIcon
            start
            icon="tabler-access-point"
          />
          Option 3
        </VTab>
      </VTabs>
    </VCol>

    <VCol
      cols="7"
      sm="8"
    >
      <VCard>
        <VCardText>
          <VWindow v-model="currentTab">
            <VWindowItem>
              <p>
                Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero.
              </p>

              <p class="mb-0">
                Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio.
              </p>
            </VWindowItem>

            <VWindowItem>
              <p>
                Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
              </p>

              <p class="mb-0">
                Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
              </p>
            </VWindowItem>

            <VWindowItem>
              <p>
                Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.
              </p>

              <p class="mb-0">
                Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis.
              </p>
            </VWindowItem>
          </VWindow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
`},Be=i("p",null,[s("The "),i("code",null,"v-tabs"),s(" component is used for hiding content behind a selectable item.")],-1),Ne=i("p",null,[s("Using "),i("code",null,"stacked"),s(" prop you can have buttons that use both icons and text.")],-1),ze=i("p",null,[s("The "),i("code",null,"vertical"),s(" prop allows for "),i("code",null,"v-tab"),s(" components to stack vertically.")],-1),Oe=i("p",null,[s("Use "),i("code",null,"centered"),s(", "),i("code",null,"right"),s(" prop to change the tabs alignment.")],-1),Ue=i("p",null,"If the tab items overflow their container, pagination controls will appear on desktop.",-1),Re=i("p",null,[i("code",null,"prev-icon"),s(" and "),i("code",null,"next-icon"),s(" props can be used for applying custom pagination icons.")],-1),Me=i("p",null,[s("The "),i("code",null,"fixed-tabs"),s(" prop forces "),i("code",null,"v-tab"),s(" to take up all available space up to the maximum width (300px).")],-1),Ee=i("p",null,[s("The "),i("code",null,"grow"),s(" prop will make the tab items take up all available space with no limit.")],-1),Ge=i("p",null,[s("Tabs can be dynamically added and removed. This allows you to update to any number and the "),i("code",null,"v-tabs"),s(" component will react.")],-1),He=i("p",null,[s("Use our custom class "),i("code",null,".v-tabs-pill"),s(" along with "),i("code",null,"v-tabs"),s(" component to style pill tabs.")],-1),Le=i("p",null,"Use our custom class .v-tabs-pill along with v-tabs component to style pill tabs.",-1),dt={__name:"tabs",setup(f){return(a,l)=>{const m=Ie,o=R,n=ke,c=_e,S=de,q=ue,_=ie,$=ae,A=te,B=ee,N=X,z=K,O=J;return r(),C(D,{class:"match-height"},{default:t(()=>[e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Basic",code:xe},{default:t(()=>[Be,e(m)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Stacked",code:Fe},{default:t(()=>[Ne,e(n)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Vertical",code:$e},{default:t(()=>[ze,e(c)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Alignment",code:ye},{default:t(()=>[Oe,e(S)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Pagination",code:Se},{default:t(()=>[Ue,e(q)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Custom Icons",code:Ce},{default:t(()=>[Re,e(_)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Fixed",code:De},{default:t(()=>[Me,e($)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Grow",code:Pe},{default:t(()=>[Ee,e(A)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Programmatic Navigation",code:je},{default:t(()=>[e(B)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{title:"Dynamic",code:We},{default:t(()=>[Ge,e(N)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{border:"",title:"Basic Pill",variant:"text",code:qe},{default:t(()=>[He,e(z)]),_:1},8,["code"])]),_:1}),e(T,{cols:"12",md:"6"},{default:t(()=>[e(o,{border:"",title:"Vertical Pill",variant:"text",code:Ae},{default:t(()=>[Le,e(O)]),_:1},8,["code"])]),_:1})]),_:1})}}};export{dt as default};
