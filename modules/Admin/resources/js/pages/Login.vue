<template>
  <div>
    <div class="header bg-gradient-success py-7 py-lg-8 pt-lg-9">
      <div class="separator separator-bottom separator-skew zindex-100">
        <svg preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" xmlns="http://www.w3.org/2000/svg"
             y="0">
          <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
        </svg>
      </div>
    </div>
    <BContainer class="mt--5 mt-lg--8 pb-5">
      <BRow class="justify-content-center">
        <BCol lg="5" md="7">
          <BCard class="bg-secondary border-0 mb-0" no-body>
            <BCardBody class="px-lg-5 py-lg-5">
              <ValidationObserver ref="formValidator" v-slot="{handleSubmit}">
                <BForm role="form" @submit.prevent="handleSubmit(submit)">
                  <BaseInput
                    v-model="form.email"
                    :rules="{required: true, email: true}"
                    alternative
                    class="mb-3"
                    name="email"
                    placeholder="Email"
                    prepend-icon="ni ni-email-83"
                  />

                  <BaseInput
                    v-model="form.password"
                    :rules="{required: true}"
                    alternative
                    class="mb-3"
                    name="password"
                    placeholder="Пароль"
                    prepend-icon="ni ni-lock-circle-open"
                    type="password"
                  />

                  <BFormCheckbox v-model="form.rememberMe">Запомнить меня</BFormCheckbox>
                  <div class="text-center">
                    <BaseButton
                      class="my-4"
                      native-type="submit"
                      type="primary">
                      Войти
                    </BaseButton>
                  </div>
                </BForm>
              </ValidationObserver>
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
    </BContainer>
  </div>
</template>

<script>
import AuthLayout from '@/argon/views/Pages/AuthLayout'

export default {
  layout: (h, page) => h(AuthLayout, [page]),
  props: {
    errors: Object,
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
        rememberMe: false
      },
      message: ''
    }
  },
  created() {
    this.$inertia.on('error', () => {
      this.$refs.formValidator.setErrors(this.errors)
    })
  },
  methods: {
    async submit() {
      this.$inertia.post('/login', this.form, {})
    }
  }
}
</script>

<style>
.main-content {
  min-height: calc(100vh - 120px);
}
</style>
