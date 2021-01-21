<template>
  <div class="btn-group-toggle" data-toggle="buttons">
    <label :class="[{ active: value }, buttonClasses]" class="btn">
      <input v-model="model" autocomplete="off" checked="" type="checkbox">
      <slot></slot>
    </label>
  </div>
</template>
<script>
export default {
  name: 'button-checkbox',
  props: {
    value: {
      type: Boolean,
      description: 'Checked value'
    },
    buttonClasses: {
      type: [String, Object],
      description: 'Inner button css classes'
    }
  },
  model: {
    prop: 'value',
    event: 'change'
  },
  computed: {
    model: {
      get() {
        return this.value
      },
      set(val) {
        this.$emit('change', val)
      }
    }
  }
}
</script>
<style>
</style>
