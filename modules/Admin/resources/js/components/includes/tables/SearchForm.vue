<template>
  <BForm class="d-flex flex-column flex-sm-row align-items-center" @submit.prevent="$emit('search')">
    <BFormGroup class="mb-1 mb-sm-0">
      <BInputGroup class="input-group-alternative input-group-merge border border-primary">
        <BFormInput
          :value="value"
          :placeholder="placeholder"
          class="bg-transparent"
          type="search"
          @input="$emit('input',$event)"
        />
        <div class="input-group-append">
          <span class="input-group-text bg-transparent">
            <i class="fas fa-search"></i>
          </span>
        </div>
      </BInputGroup>
    </BFormGroup>
    <BaseButton
      native-type="submit"
      type="primary"
      outline
      class="ml-1">
      Поиск
    </BaseButton>
  </BForm>
</template>

<script>
export default {
  props: {
    value: String,
    placeholder: String
  }
}
</script>

<style scoped>
.form-control {
  position: relative;
  min-width: 150px;
}
</style>
