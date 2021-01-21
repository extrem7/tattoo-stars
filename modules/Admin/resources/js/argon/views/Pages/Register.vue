<template>
  <div>
    <!-- Header -->
    <div class="header bg-gradient-success py-7 py-lg-8 pt-lg-9">
      <b-container class="container">
        <div class="header-body text-center mb-7">
          <b-row class="justify-content-center">
            <b-col class="px-5" lg="6" md="8" xl="5">
              <h1 class="text-white">Create an account</h1>
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
      <!-- Table -->
      <b-row class="justify-content-center">
        <b-col lg="6" md="8">
          <b-card class="bg-secondary border-0" no-body>
            <b-card-header class="bg-transparent pb-5">
              <div class="text-muted text-center mt-2 mb-4"><small>Sign up with</small></div>
              <div class="text-center">
                <a class="btn btn-neutral btn-icon mr-4" href="#">
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
                <small>Or sign up with credentials</small>
              </div>
              <validation-observer ref="formValidator" v-slot="{handleSubmit}">
                <b-form role="form" @submit.prevent="handleSubmit(onSubmit)">
                  <base-input v-model="model.name"
                              :rules="{required: true}"
                              alternative
                              class="mb-3"
                              name="Name"
                              placeholder="Name"
                              prepend-icon="ni ni-hat-3">
                  </base-input>

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
                              placeholder="password"
                              prepend-icon="ni ni-lock-circle-open"
                              type="password">
                  </base-input>
                  <div class="text-muted font-italic"><small>password strength: <span
                    class="text-success font-weight-700">strong</span></small></div>
                  <b-row class=" my-4">
                    <b-col cols="12">
                      <base-input :rules="{ required: { allowFalse: false } }" Policy name=Privacy>
                        <b-form-checkbox v-model="model.agree">
                          <span class="text-muted">I agree with the <a href="#!">Privacy Policy</a></span>
                        </b-form-checkbox>
                      </base-input>
                    </b-col>
                  </b-row>
                  <div class="text-center">
                    <b-button class="mt-4" type="submit" variant="primary">Create account</b-button>
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

export default {
  name: 'register',
  data() {
    return {
      model: {
        name: '',
        email: '',
        password: '',
        agree: false
      }
    }
  },
  methods: {
    onSubmit() {
      // this will be called only after form is valid. You can do an api call here to register users
    }
  }

}
</script>
<style></style>
