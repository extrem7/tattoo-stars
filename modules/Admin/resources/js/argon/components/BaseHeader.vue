<template>
  <div :class="{[`bg-${type}`]: type}" class="header">
    <b-container fluid>
      <div class="header-body">
        <slot></slot>
      </div>
    </b-container>
  </div>
</template>
<script>
export default {
  name: 'BaseHeader',
  props: {
    type: {
      type: String,
      default: 'success',
      description: 'Header background type'
    }
  }
}
</script>
<style>
</style>
