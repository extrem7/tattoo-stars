<template>
  <div>
    <!-- Header -->
    <div class="header bg-gradient-success py-7 py-lg-8 pt-lg-9">
      <b-container>
        <div class="header-body text-center mb-7">
          <b-row class="justify-content-center">
            <b-col class="px-5" lg="6" md="8" xl="5">
              <h1 class="text-white">Welcome!</h1>
              <p class="text-lead text-white">Use these awesome forms to login or create new account in your project for
                free.</p>
            </b-col>
          </b-row>
        </div>
      </b-container>
      <div class="separator separator-bottom separator-skew zindex-100">
        <svg preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" xmlns="http://www.w3.org/2000/svg"
             y="0">
          <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
        </svg>
      </div>
    </div>
    <!-- Page content -->
    <b-container class="mt--8 pb-5">
      <b-row class="justify-content-center">
        <b-col lg="5" md="7">
          <b-card class="bg-secondary border-0 mb-0" no-body>
            <b-card-header class="bg-transparent pb-5">
              <div class="text-muted text-center mt-2 mb-3"><small>Sign in with</small></div>
              <div class="btn-wrapper text-center">
                <a class="btn btn-neutral btn-icon" href="#">
                  <span class="btn-inner--icon"><img src="img/icons/common/github.svg"></span>
                  <span class="btn-inner--text">Github</span>
                </a>
                <a class="btn btn-neutral btn-icon" href="#">
                  <span class="btn-inner--icon"><img src="img/icons/common/google.svg"></span>
                  <span class="btn-inner--text">Google</span>
                </a>
              </div>
            </b-card-header>
            <b-card-body class="px-lg-5 py-lg-5">
              <div class="text-center text-muted mb-4">
                <small>Or sign in with credentials</small>
              </div>
              <validation-observer ref="formValidator" v-slot="{handleSubmit}">
                <b-form role="form" @submit.prevent="handleSubmit(onSubmit)">
                  <base-input v-model="model.email"
                              :rules="{required: true, email: true}"
                              alternative
                              class="mb-3"
                              name="Email"
                              placeholder="Email"
                              prepend-icon="ni ni-email-83">
                  </base-input>

                  <base-input v-model="model.password"
                              :rules="{required: true, min: 6}"
                              alternative
                              class="mb-3"
                              name="Password"
                              placeholder="Password"
                              prepend-icon="ni ni-lock-circle-open"
                              type="password">
                  </base-input>

                  <b-form-checkbox v-model="model.rememberMe">Remember me</b-form-checkbox>
                  <div class="text-center">
                    <base-button class="my-4" native-type="submit" type="primary">Sign in</base-button>
                  </div>
                </b-form>
              </validation-observer>
            </b-card-body>
          </b-card>
          <b-row class="mt-3">
            <b-col cols="6">
              <router-link class="text-light" to="/dashboard"><small>Forgot password?</small></router-link>
            </b-col>
            <b-col class="text-right" cols="6">
              <router-link class="text-light" to="/register"><small>Create new account</small></router-link>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>
<script>
export default {
  data() {
    return {
      model: {
        email: '',
        password: '',
        rememberMe: false
      }
    }
  },
  methods: {
    onSubmit() {
      // this will be called only after form is valid. You can do api call here to login
    }
  }
}
</script>
