<template>
  <card>
    <b-row slot="header" align-v="center">
      <b-col cols="8">
        <h3 class="mb-0">Edit profile </h3>
      </b-col>
      <b-col class="text-right" cols="4">
        <a class="btn btn-sm btn-primary" href="#!">Settings</a>
      </b-col>
    </b-row>

    <b-form @submit.prevent="updateProfile">
      <h6 class="heading-small text-muted mb-4">User information</h6>

      <div class="pl-lg-4">
        <b-row>
          <b-col lg="6">
            <base-input
              v-model="user.username"
              label="Username"
              placeholder="Username"
              type="text"
            >
            </base-input>
          </b-col>
          <b-col lg="6">
            <base-input
              v-model="user.email"
              label="Email address"
              placeholder="mike@email.com"
              type="email"
            >
            </base-input>
          </b-col>
        </b-row>
        <b-row>
          <b-col lg="6">
            <base-input
              v-model="user.firstName"
              label="First Name"
              placeholder="First Name"
              type="text"
            >
            </base-input>
          </b-col>
          <b-col lg="6">
            <base-input
              v-model="user.lastName"
              label="Last Name"
              placeholder="Last Name"
              type="text"
            >
            </base-input>
          </b-col>
        </b-row>
      </div>
      <hr class="my-4">

      <!-- Address -->
      <h6 class="heading-small text-muted mb-4">Contact information</h6>

      <div class="pl-lg-4">
        <b-row>
          <b-col md="12">
            <base-input
              v-model="user.address"
              label="Address"
              placeholder="Home Address"
              type="text"
            >
            </base-input>
          </b-col>
        </b-row>
        <b-row>
          <b-col lg="4">
            <base-input
              v-model="user.city"
              label="City"
              placeholder="City"
              type="text"
            >
            </base-input>
          </b-col>
          <b-col lg="4">
            <base-input
              v-model="user.country"
              label="Country"
              placeholder="Country"
              type="text"
            >
            </base-input>
          </b-col>
          <b-col lg="4">
            <base-input
              v-model="user.postalCode"
              label="Postal Code"
              placeholder="ZIP Code"
            >
            </base-input>
          </b-col>
        </b-row>
      </div>

      <hr class="my-4">
      <!-- Description -->
      <h6 class="heading-small text-muted mb-4">About me</h6>
      <div class="pl-lg-4">
        <b-form-group class="mb-0" label="About Me" label-class="form-control-label" label-for="about-form-textaria">
          <!--  <label class="form-control-label">About Me</label> -->
          <b-form-textarea id="about-form-textaria" placeholder="A few words about you ..." rows="4"
                           value="A beautiful premium dashboard for BootstrapVue."></b-form-textarea>
        </b-form-group>
      </div>

    </b-form>
  </card>
</template>
<script>
export default {
  data() {
    return {
      user: {
        company: 'Creative Code Inc.',
        username: 'michael23',
        email: '',
        firstName: 'Mike',
        lastName: 'Andrew',
        address: 'Bld Mihail Kogalniceanu, nr. 8 Bl 1, Sc 1, Ap 09',
        city: 'New York',
        country: 'USA',
        postalCode: '',
        aboutMe: `Lamborghini Mercy, Your chick she so thirsty, I'm in that two seat Lambo.`
      }
    }
  },
  methods: {
    updateProfile() {
      alert('Your data: ' + JSON.stringify(this.user))
    }
  }
}
</script>
<style></style>
