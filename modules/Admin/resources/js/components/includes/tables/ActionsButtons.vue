<template>
  <div class="d-flex">
    <slot></slot>
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
      class="ml-2"
      type="danger"
      @click.prevent="$emit('delete')">
      Удалить {{ isTrash ? 'окончательно' : '' }}
    </BaseButton>
  </div>
</template>

<script>
export default {
  props: {
    id: Number,
    resource: String,
    isTrash: {
      type: Boolean,
      default: false
    }
  }
}
</script>

