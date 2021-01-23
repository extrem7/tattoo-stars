<template>
  <base-nav
    :class="{'navbar-dark': type === 'default'}"
    class="navbar-top navbar-expand"
    container-classes="container-fluid">
    <a aria-current="page" class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block active router-link-active"
       href="#">
      {{ $page.props.metaInfo.title }} </a>
    <!-- Navbar links -->
    <b-navbar-nav class="align-items-center ml-md-auto">
      <!-- This item dont have <b-nav-item> because item have data-action/data-target on tag <a>, wich we cant add -->
      <li class="nav-item d-sm-none">
        <a class="nav-link" data-action="search-show" data-target="#navbar-search-main" href="#">
          <i class="ni ni-zoom-split-in"></i>
        </a>
      </li>
    </b-navbar-nav>
    <b-navbar-nav class="align-items-center ml-auto ml-md-0">
      <!--<b-form id="navbar-search-main"
              :class="{'navbar-search-dark': type === 'default', 'navbar-search-light': type === 'light'}"
              class="navbar-search form-inline mr-sm-3">
        <b-form-group class="mb-0">
          <b-input-group class="input-group-alternative input-group-merge">
            <b-form-input placeholder="Search" type="text"></b-form-input>

            <div class="input-group-append">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
          </b-input-group>
        </b-form-group>
      </b-form>-->
      <base-dropdown class="nav-item"
                     menu-on-right
                     tag="li"
                     title-classes="nav-link pr-0"
                     title-tag="a">
        <a slot="title-container" class="nav-link pr-0" href="#" @click.prevent>
          <b-media class="align-items-center" no-body>
                  <span class="avatar avatar-sm rounded-circle">
                    <img alt="Image placeholder" src="/admin/dist/img/no-avatar.png">
                  </span>
            <b-media-body class="ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm  font-weight-bold">{{ user.name }}</span>
            </b-media-body>
          </b-media>
        </a>

        <template>

          <b-dropdown-header class="noti-title">
            <h6 class="text-overflow m-0">Приветствую!</h6>
          </b-dropdown-header>
          <b-dropdown-item href="#!">
            <i class="ni ni-single-02"></i>
            <span>My profile</span>
          </b-dropdown-item>
          <b-dropdown-item href="#!">
            <i class="ni ni-settings-gear-65"></i>
            <span>Settings</span>
          </b-dropdown-item>
          <div class="dropdown-divider"></div>
          <BDropdownItem
            @click.prevent="logout"
            href="#logout">
            <i class="ni ni-user-run"></i>
            <span>Выйти</span>
          </BDropdownItem>

        </template>
      </base-dropdown>
    </b-navbar-nav>
  </base-nav>
</template>

<script>
import {CollapseTransition} from 'vue2-transitions'
import {BaseNav, Modal} from '@/argon/components'

export default {
  components: {
    CollapseTransition,
    BaseNav,
    Modal
  },
  props: {
    type: {
      type: String,
      default: 'default', // default|light
    }
  },
  computed: {
    title() {
      return this.$page.props.metaInfo.title
    },
    user() {
      return this.$page.props.user
    }
  },
  methods: {
    logout() {
      this.$inertia.delete(this.route('logout'))
    },
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }
  }
}
</script>
