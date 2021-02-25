<template>
  <div v-if="list.length">
    <span
      v-for="(error,key) in list"
      :key="key"
      class="invalid-feedback">
      {{ error }}
    </span>
  </div>
</template>

<script>
export default {
  props: {
    name: String,
    errors: Object,
  },
  computed: {
    list() {
      const errors = []
      for (let key in this.errors) {
        console.log(key, this.name)
        if (key.includes(this.name)) errors.push(this.errors[key])
      }
      return errors
    }
  }
}
</script>
