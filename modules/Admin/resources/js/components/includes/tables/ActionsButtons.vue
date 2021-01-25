<template>
  <div class="d-flex">
    <InertiaLink
      v-if="can(`${resource}.edit`)"
      :href="route(`${resource}.edit`,id)"
      as="BaseButton"
      type="primary">
      Редактировать
    </InertiaLink>
    <BaseButton
      v-if="can(`${resource}.delete`)"
      @click.prevent="$emit('delete')"
      type="danger"
      class="ml-2">
      Удалить
    </BaseButton>
  </div>
</template>

<script>
export default {
  props: {
    id: Number
  },
  inject: ['resource']
}
</script>

