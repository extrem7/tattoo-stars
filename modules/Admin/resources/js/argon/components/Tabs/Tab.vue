<template>
  <div
    v-show="active"
    :id="id || title"
    :aria-expanded="active"
    :class="{ active: active }"
    class="tab-pane"
  >
    <slot></slot>
  </div>
</template>
<script>
export default {
  name: 'tab-pane',
  props: ['title', 'id'],
  inject: ['addTab', 'removeTab'],
  data() {
    return {
      active: false
    }
  },
  mounted() {
    this.addTab(this)
  },
  destroyed() {
    if (this.$el && this.$el.parentNode) {
      this.$el.parentNode.removeChild(this.$el)
    }
    this.removeTab(this)
  }
}
</script>
<style></style>
