import{k as s,o as v,b as w,w as o,p as l,q as e,D as n,E as F,be as _,m as D}from"./main.62b527cd.js";import{r as m,b as S,i as E,a as $,c as A,l as M,p as k,d as L,f as Y,e as R,u as O}from"./validators.330a354f.js";import{V as y,a}from"./VRow.6ca003db.js";import{V as u}from"./VTextField.8161c550.js";import{V as U}from"./VBtn.cdeb389c.js";import{V as N}from"./VForm.b4c543b7.js";import{_ as j}from"./AppCardCode.300e2e3a.js";import"./index.b522f886.js";/* empty css                   */import"./VField.535cf73c.js";import"./index.c16f3052.js";import"./VInput.8f858013.js";import"./router.ae1b773a.js";import"./VImg.520fa55c.js";import"./position.db006387.js";import"./easing.36b781ab.js";import"./forwardRefs.c003b6b8.js";import"./VCounter.d2b3c9c7.js";import"./vue.runtime.esm-bundler.1783b3d8.js";import"./VCard.cb057c3d.js";import"./VAvatar.304e7cac.js";import"./VDivider.e02e64ce.js";const I={__name:"DemoFormValidationValidationTypes",setup(x){const f=s(""),V=s(""),b=s(""),p=s(""),i=s(""),h=s(""),d=s(""),c=s(""),C=s(""),g=s(""),q=s(""),T=s(""),B=s();return(W,r)=>(v(),w(N,{ref_key:"refForm",ref:B,onSubmit:r[13]||(r[13]=_(()=>{},["prevent"]))},{default:o(()=>[l(y,null,{default:o(()=>[l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(f),"onUpdate:modelValue":r[0]||(r[0]=t=>n(f)?f.value=t:null),"persistent-placeholder":"",placeholder:"This field is required",rules:[e(m)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(V),"onUpdate:modelValue":r[1]||(r[1]=t=>n(V)?V.value=t:null),"persistent-placeholder":"",placeholder:"Enter Number between 10 & 20",rules:[e(m),e(S)(e(V),10,20)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(b),"onUpdate:modelValue":r[2]||(r[2]=t=>n(b)?b.value=t:null),"persistent-placeholder":"",placeholder:"Must only consist of numbers",rules:[e(m),e(E)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(p),"onUpdate:modelValue":r[3]||(r[3]=t=>n(p)?p.value=t:null),"persistent-placeholder":"",placeholder:"Must match the specified regular expression : ^([0-9]+)$ - numbers only",rules:[e(m),e($)(e(p),"^([0-9]+)$")]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(i),"onUpdate:modelValue":r[4]||(r[4]=t=>n(i)?i.value=t:null),"persistent-placeholder":"",placeholder:"Only alphabetic characters",rules:[e(m),e(A)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(h),"onUpdate:modelValue":r[5]||(r[5]=t=>n(h)?h.value=t:null),"persistent-placeholder":"",placeholder:"Length should not be less than the specified length : 3",rules:[e(m),e(M)(e(h),3)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(d),"onUpdate:modelValue":r[6]||(r[6]=t=>n(d)?d.value=t:null),"persistent-placeholder":"",placeholder:"Password Input Field",type:"password",rules:[e(m),e(k)],autocomplete:"on"},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(c),"onUpdate:modelValue":r[7]||(r[7]=t=>n(c)?c.value=t:null),"persistent-placeholder":"",placeholder:"The digits field must be numeric and exactly contain 3 digits",rules:[e(m),e(M)(e(c),3),e(E)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(C),"onUpdate:modelValue":r[8]||(r[8]=t=>n(C)?C.value=t:null),"persistent-placeholder":"",placeholder:"Repeat password must match",type:"password",rules:[e(m),e(L)(e(C),e(d))],autocomplete:"on"},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(g),"onUpdate:modelValue":r[9]||(r[9]=t=>n(g)?g.value=t:null),"persistent-placeholder":"",placeholder:"Only alphabetic characters, numbers, dashes or underscores",rules:[e(m),e(Y)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(q),"onUpdate:modelValue":r[10]||(r[10]=t=>n(q)?q.value=t:null),"persistent-placeholder":"",placeholder:"Must be a valid email",rules:[e(m),e(R)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(T),"onUpdate:modelValue":r[11]||(r[11]=t=>n(T)?T.value=t:null),"persistent-placeholder":"",placeholder:"Must be a valid url",rules:[e(m),e(O)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12"},{default:o(()=>[l(U,{type:"submit",onClick:r[12]||(r[12]=t=>{var P;return(P=e(B))==null?void 0:P.validate()})},{default:o(()=>[F(" Submit ")]),_:1})]),_:1})]),_:1})]),_:1},512))}},z={__name:"DemoFormValidationValidatingMultipleRules",setup(x){const f=s(""),V=s(""),b=s(),p=s(""),i=s("");return(h,d)=>(v(),w(N,{ref_key:"refForm",ref:b,onSubmit:d[5]||(d[5]=_(()=>{},["prevent"]))},{default:o(()=>[l(y,null,{default:o(()=>[l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(f),"onUpdate:modelValue":d[0]||(d[0]=c=>n(f)?f.value=c:null),placeholder:"Your Name","persistent-placeholder":"",rules:[e(m)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(V),"onUpdate:modelValue":d[1]||(d[1]=c=>n(V)?V.value=c:null),placeholder:"Your Email","persistent-placeholder":"",rules:[e(m),e(R)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(p),"onUpdate:modelValue":d[2]||(d[2]=c=>n(p)?p.value=c:null),type:"password",placeholder:"Your Password","persistent-placeholder":"",rules:[e(m),e(k)],autocomplete:"on"},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(i),"onUpdate:modelValue":d[3]||(d[3]=c=>n(i)?i.value=c:null),type:"password",placeholder:"Confirm Password","persistent-placeholder":"",rules:[e(m),e(L)(e(i),e(p))],autocomplete:"on"},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12"},{default:o(()=>[l(U,{type:"submit",onClick:d[4]||(d[4]=c=>{var C;return(C=e(b))==null?void 0:C.validate()})},{default:o(()=>[F(" Submit ")]),_:1})]),_:1})]),_:1})]),_:1},512))}},G={__name:"DemoFormValidationSimpleFormValidation",setup(x){const f=s(""),V=s(""),b=s();return(p,i)=>(v(),w(N,{ref_key:"refForm",ref:b,onSubmit:i[3]||(i[3]=_(()=>{},["prevent"]))},{default:o(()=>[l(y,null,{default:o(()=>[l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(f),"onUpdate:modelValue":i[0]||(i[0]=h=>n(f)?f.value=h:null),label:"First Name",rules:[e(m)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12",md:"6"},{default:o(()=>[l(u,{modelValue:e(V),"onUpdate:modelValue":i[1]||(i[1]=h=>n(V)?V.value=h:null),label:"Email",rules:[e(m),e(R)]},null,8,["modelValue","rules"])]),_:1}),l(a,{cols:"12"},{default:o(()=>[l(U,{type:"submit",onClick:i[2]||(i[2]=h=>{var d;return(d=e(b))==null?void 0:d.validate()})},{default:o(()=>[F(" Submit ")]),_:1})]),_:1})]),_:1})]),_:1},512))}},H={ts:`<script lang="ts" setup>
// eslint-disable-next-line @typescript-eslint/consistent-type-imports
import type { VForm } from 'vuetify/components'
import { emailValidator, requiredValidator } from '@validators'

const firstName = ref('')
const email = ref('')

const refForm = ref<VForm>()
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent="() => {}"
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="firstName"
          label="First Name"
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          label="Email"
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`,js:`<script setup>
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const firstName = ref('')
const email = ref('')
const refForm = ref()
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent="() => {}"
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="firstName"
          label="First Name"
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          label="Email"
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`},J={ts:`<script lang="ts" setup>
// eslint-disable-next-line @typescript-eslint/consistent-type-imports
import type { VForm } from 'vuetify/components'
import { confirmedValidator, emailValidator, passwordValidator, requiredValidator } from '@validators'

const name = ref('')
const email = ref('')
const refForm = ref<VForm>()
const password = ref('')
const confirmPassword = ref('')
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent="() => {}"
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="name"
          placeholder="Your Name"
          persistent-placeholder
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          placeholder="Your Email"
          persistent-placeholder
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="password"
          type="password"
          placeholder="Your Password"
          persistent-placeholder
          :rules="[requiredValidator, passwordValidator]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="confirmPassword"
          type="password"
          placeholder="Confirm Password"
          persistent-placeholder
          :rules="[requiredValidator, confirmedValidator(confirmPassword, password)]"
          autocomplete="on"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`,js:`<script setup>
import {
  confirmedValidator,
  emailValidator,
  passwordValidator,
  requiredValidator,
} from '@validators'

const name = ref('')
const email = ref('')
const refForm = ref()
const password = ref('')
const confirmPassword = ref('')
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent="() => {}"
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="name"
          placeholder="Your Name"
          persistent-placeholder
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          placeholder="Your Email"
          persistent-placeholder
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="password"
          type="password"
          placeholder="Your Password"
          persistent-placeholder
          :rules="[requiredValidator, passwordValidator]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="confirmPassword"
          type="password"
          placeholder="Confirm Password"
          persistent-placeholder
          :rules="[requiredValidator, confirmedValidator(confirmPassword, password)]"
          autocomplete="on"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`},K={ts:`<script lang="ts" setup>
// eslint-disable-next-line @typescript-eslint/consistent-type-imports
import type { VForm } from 'vuetify/components'
import {
  alphaDashValidator,
  alphaValidator,
  betweenValidator,
  confirmedValidator,
  emailValidator,
  integerValidator,
  lengthValidator,
  passwordValidator,
  regexValidator,
  requiredValidator,
  urlValidator,
} from '@validators'

const requiredField = ref('')
const numberBetween10to20 = ref('')
const onlyConsistNumber = ref('')
const matchRegularEx = ref('')
const onlyAlphabeticCharacters = ref('')
const specifiedLength = ref('')
const password = ref('')
const digits = ref('')
const repeatPassword = ref('')
const onlyAlphabeticNumbersDashesUnderscores = ref('')
const email = ref('')
const validURL = ref('')
const refForm = ref<VForm>()
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="requiredField"
          persistent-placeholder
          placeholder="This field is required"
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="numberBetween10to20"
          persistent-placeholder
          placeholder="Enter Number between 10 & 20"
          :rules="[requiredValidator, betweenValidator(numberBetween10to20, 10, 20)]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyConsistNumber"
          persistent-placeholder
          placeholder="Must only consist of numbers"
          :rules="[requiredValidator, integerValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="matchRegularEx"
          persistent-placeholder
          placeholder="Must match the specified regular expression : ^([0-9]+)$ - numbers only"
          :rules="[requiredValidator, regexValidator(matchRegularEx, '^([0-9]+)$')]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyAlphabeticCharacters"
          persistent-placeholder
          placeholder="Only alphabetic characters"
          :rules="[requiredValidator, alphaValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="specifiedLength"
          persistent-placeholder
          placeholder="Length should not be less than the specified length : 3"
          :rules="[requiredValidator, lengthValidator(specifiedLength, 3)]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="password"
          persistent-placeholder
          placeholder="Password Input Field"
          type="password"
          :rules="[requiredValidator, passwordValidator]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="digits"
          persistent-placeholder
          placeholder="The digits field must be numeric and exactly contain 3 digits"
          :rules="[requiredValidator, lengthValidator(digits, 3), integerValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="repeatPassword"
          persistent-placeholder
          placeholder="Repeat password must match"
          type="password"
          :rules="[requiredValidator, confirmedValidator(repeatPassword, password)]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyAlphabeticNumbersDashesUnderscores"
          persistent-placeholder
          placeholder="Only alphabetic characters, numbers, dashes or underscores"
          :rules="[requiredValidator, alphaDashValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          persistent-placeholder
          placeholder="Must be a valid email"
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="validURL"
          persistent-placeholder
          placeholder="Must be a valid url"
          :rules="[requiredValidator, urlValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`,js:`<script setup>
import {
  alphaDashValidator,
  alphaValidator,
  betweenValidator,
  confirmedValidator,
  emailValidator,
  integerValidator,
  lengthValidator,
  passwordValidator,
  regexValidator,
  requiredValidator,
  urlValidator,
} from '@validators'

const requiredField = ref('')
const numberBetween10to20 = ref('')
const onlyConsistNumber = ref('')
const matchRegularEx = ref('')
const onlyAlphabeticCharacters = ref('')
const specifiedLength = ref('')
const password = ref('')
const digits = ref('')
const repeatPassword = ref('')
const onlyAlphabeticNumbersDashesUnderscores = ref('')
const email = ref('')
const validURL = ref('')
const refForm = ref()
<\/script>

<template>
  <VForm
    ref="refForm"
    @submit.prevent
  >
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="requiredField"
          persistent-placeholder
          placeholder="This field is required"
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="numberBetween10to20"
          persistent-placeholder
          placeholder="Enter Number between 10 & 20"
          :rules="[requiredValidator, betweenValidator(numberBetween10to20, 10, 20)]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyConsistNumber"
          persistent-placeholder
          placeholder="Must only consist of numbers"
          :rules="[requiredValidator, integerValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="matchRegularEx"
          persistent-placeholder
          placeholder="Must match the specified regular expression : ^([0-9]+)$ - numbers only"
          :rules="[requiredValidator, regexValidator(matchRegularEx, '^([0-9]+)$')]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyAlphabeticCharacters"
          persistent-placeholder
          placeholder="Only alphabetic characters"
          :rules="[requiredValidator, alphaValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="specifiedLength"
          persistent-placeholder
          placeholder="Length should not be less than the specified length : 3"
          :rules="[requiredValidator, lengthValidator(specifiedLength, 3)]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="password"
          persistent-placeholder
          placeholder="Password Input Field"
          type="password"
          :rules="[requiredValidator, passwordValidator]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="digits"
          persistent-placeholder
          placeholder="The digits field must be numeric and exactly contain 3 digits"
          :rules="[requiredValidator, lengthValidator(digits, 3), integerValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="repeatPassword"
          persistent-placeholder
          placeholder="Repeat password must match"
          type="password"
          :rules="[requiredValidator, confirmedValidator(repeatPassword, password)]"
          autocomplete="on"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="onlyAlphabeticNumbersDashesUnderscores"
          persistent-placeholder
          placeholder="Only alphabetic characters, numbers, dashes or underscores"
          :rules="[requiredValidator, alphaDashValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="email"
          persistent-placeholder
          placeholder="Must be a valid email"
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <VTextField
          v-model="validURL"
          persistent-placeholder
          placeholder="Must be a valid url"
          :rules="[requiredValidator, urlValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VBtn
          type="submit"
          @click="refForm?.validate()"
        >
          Submit
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
`},Q=D("p",null,[F("Use "),D("code",null,"Rules"),F(" prop to validate the input.")],-1),ve={__name:"form-validation",setup(x){return(f,V)=>{const b=G,p=j,i=z,h=I;return v(),w(y,null,{default:o(()=>[l(a,{cols:"12"},{default:o(()=>[l(p,{title:"Simple Form Validation",code:H},{default:o(()=>[Q,l(b)]),_:1},8,["code"])]),_:1}),l(a,{cols:"12"},{default:o(()=>[l(p,{title:"Validating Multiple Rules",code:J},{default:o(()=>[l(i)]),_:1},8,["code"])]),_:1}),l(a,{cols:"12"},{default:o(()=>[l(p,{title:"Validation Types",code:K},{default:o(()=>[l(h)]),_:1},8,["code"])]),_:1})]),_:1})}}};export{ve as default};
