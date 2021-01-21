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
    <b-container class="mt--8 pb-5">
      <b-row class="justify-content-center">
        <b-col lg="5" md="7">
          <b-card class="bg-secondary border-0 mb-0" no-body>
            <b-card-body class="px-lg-5 py-lg-5">
              <validation-observer ref="formValidator" v-slot="{handleSubmit}">
                <b-form role="form" @submit.prevent="handleSubmit(onSubmit)">
                  <b-alert :show="message.length" class="text-center" variant="danger">
                    {{ message }}
                  </b-alert>
                  <base-input v-model="form.email"
                              :rules="{required: true, email: true}"
                              alternative
                              class="mb-3"
                              name="email"
                              placeholder="Email"
                              prepend-icon="ni ni-email-83">
                  </base-input>

                  <base-input v-model="form.password"
                              :rules="{required: true, min: 6}"
                              alternative
                              class="mb-3"
                              name="password"
                              placeholder="Пароль"
                              prepend-icon="ni ni-lock-circle-open"
                              type="password">
                  </base-input>

                  <b-form-checkbox v-model="form.rememberMe">Запомнить меня</b-form-checkbox>
                  <div class="text-center">
                    <base-button class="my-4" native-type="submit" type="primary">
                      Войти
                    </base-button>
                  </div>
                </b-form>
              </validation-observer>
            </b-card-body>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
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
