<template>
  <div class="form-group mb-0">
    <InertiaLink
      :href="route(`${resource}.create`)"
      class="mb-3 mb-sm-0 btn btn-outline-success">
      Создать
    </InertiaLink>
  </div>
</template>
<script>
export default {
  inject: ['resource']
}
</script>
