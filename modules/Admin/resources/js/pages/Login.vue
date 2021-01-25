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
    <BContainer class="mt--8 pb-5">
      <BRow class="justify-content-center">
        <BCol lg="5" md="7">
          <BCard class="bg-secondary border-0 mb-0" no-body>
            <BCardBody class="px-lg-5 py-lg-5">
              <ValidationObserver ref="formValidator" v-slot="{handleSubmit}">
                <BForm role="form" @submit.prevent="handleSubmit(onSubmit)">
                  <BAlert :show="message.length" class="text-center" variant="danger">
                    {{ message }}
                  </BAlert>
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
import axios from 'axios'

import AuthLayout from '@/argon/views/Pages/AuthLayout'

export default {
  layout: (h, page) => h(AuthLayout, [page]),
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
  methods: {
    async onSubmit() {
      try {
        await axios.post('/login', this.form)
        location.href = '/'
      } catch ({response: {status, data}}) {
        if (status === 429) {
          this.message = 'Too Many Attempts.'
        } else if (status === 422) {
          this.message = data.message
          console.log(data.errors)
          this.$refs.formValidator.setErrors(data.errors)
        }
      }
    }
  }
}
</script>

<style>
.main-content {
  min-height: calc(100vh - 120px);
}
</style>
