<template>
  <div class="btn-group-toggle" data-toggle="buttons">
    <label v-for="(option, index) in options"
           :key="index"
           :class="[{ active: value === option.value }, buttonClasses]"
           class="btn">
      <input id="option1" v-model="model" :value="option.value" autocomplete="off" checked="" type="radio">
      {{ option.label }}
    </label>
  </div>
</template>
<script>
export default {
  name: 'button-radio-group',
  props: {
    options: {
      type: Array,
      description: 'Radio options. Should be an array of objects {value: "", label: ""}',
      default: () => []
    },
    value: {
      type: String,
      description: 'Radio value'
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
