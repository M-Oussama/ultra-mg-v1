<template>
  <div class="h-100 d-flex align-center justify-space-between">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center">
      &copy;
      {{ new Date().getFullYear() }}
      Made With
      <VIcon
        icon="tabler-heart"
        color="error"
        size="1.25rem"
        class="mx-1"
      />
      By <a
        href="#"
        target="_blank"
        rel="noopener noreferrer"
        class="text-primary ms-1"
      >Oussama</a>
    </span>
    <!-- 👉 Footer: right content -->
<!--    <span class="d-md-flex gap-x-4 text-primary d-none">-->
<!--      <a-->
<!--        href="https://themeforest.net/licenses/standard"-->
<!--        target="noopener noreferrer"-->
<!--      >License</a>-->
<!--      <a-->
<!--        href="https://pixinvent.com/"-->
<!--        target="noopener noreferrer"-->
<!--      >More Themes</a>-->
<!--      <a-->
<!--        href="https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/"-->
<!--        target="noopener noreferrer"-->
<!--      >Documentation</a>-->
<!--    </span>-->
  </div>
</template>
