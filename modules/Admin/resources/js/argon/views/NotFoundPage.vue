<template>
  <div>
    <!-- Header -->
    <div class="header bg-gradient-info py-7 py-lg-8 pt-lg-9">
      <b-container>
        <div class="header-body text-center mb-7">
          <b-row class="justify-content-center">
            <b-col class="px-5" lg="6" md="6" xl="5">
              <h1 class="text-light">Ooops!</h1>
              <p class="text-lead text-white">Page not found. Don't worry though, we have plenty of other pages to
                explore</p>

              <router-link class="font-weight-bold text-white mt-5" to="/dashboard">Back to dashboard</router-link>
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
    <section class="py-6">
      <b-container>
        <b-row align-v="center" class="row-grid">
          <b-col md="6">
            <b-img fluid src="img/theme/landing-2.png"/>
          </b-col>
          <b-col md="6">
            <div class="pr-md-5">
              <h1 class="text-light">Explore pages</h1>
              <p class="text-light">If you want to get inspiration or just show something directly to your clients, you
                can jump start your development with our pre-built example pages.</p>
              <router-link class="font-weight-bold text-warning mt-5" to="/user">Explore pages</router-link>
            </div>
          </b-col>
        </b-row>
      </b-container>
    </section>
  </div>
</template>

<script>

export default {
  name: 'not-found'
}
</script>
