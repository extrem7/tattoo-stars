<template>
  <div class="d-flex">
    <InertiaLink
      v-if="can(`${resource}.edit`)"
      :href="route(`${resource}.edit`,id)"
      class="btn btn-primary">
      Редактировать
    </InertiaLink>
    <BaseButton
      v-if="isTrash && can(`${resource}.restore`)"
      class="ml-2"
      type="info"
      @click.prevent="$emit('restore')">
      Восстановить
    </BaseButton>
    <BaseButton
      v-if="can(`${resource}.${!isTrash?'delete':'force-delete'}`)"
      @click.prevent="$emit('delete')"
      type="danger"
      class="ml-2">
      Удалить {{ isTrash ? 'окончательно' : '' }}
    </BaseButton>
  </div>
</template>

<script>
export default {
  props: {
    id: Number
  },
  inject: ['resource', 'isTrash']
}
</script>

