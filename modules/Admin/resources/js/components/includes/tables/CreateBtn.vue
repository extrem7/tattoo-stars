<template>
  <div class="form-group mb-0">
    <InertiaLink
      :href="route(`${resource}.create`)"
      as="BBtn"
      class="mb-3 mb-sm-0"
      variant="outline-success">
      Создать
    </InertiaLink>
  </div>
</template>
<script>
export default {
  inject: ['resource']
}
</script>
