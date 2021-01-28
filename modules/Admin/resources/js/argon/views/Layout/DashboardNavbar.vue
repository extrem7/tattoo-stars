<template>
  <BaseNav
    :class="{'navbar-dark': type === 'default'}"
    class="navbar-top navbar-expand"
    container-classes="container-fluid">
    <slot name="toggle-button"></slot>

    <a aria-current="page" class="h4 mb-0 text-white d-none d-lg-inline-block active router-link-active"
       href="#">
      {{ $page.props.metaInfo.title }}
    </a>
    <BNavbarNav class="align-items-center ml-md-auto">
      <li class="nav-item d-sm-none">
        <a class="nav-link" data-action="search-show" data-target="#navbar-search-main" href="#">
          <i class="ni ni-zoom-split-in"></i>
        </a>
      </li>
    </BNavbarNav>
    <BNavbarNav class="align-items-center ml-auto ml-md-0">
      <BaseDropdown
        class="nav-item"
        menu-on-right
        tag="li"
        title-classes="nav-link pr-0"
        title-tag="a">
        <a slot="title-container" class="nav-link pr-0" href="#" @click.prevent>
          <BMedia class="align-items-center" no-body>
                  <span class="avatar avatar-sm rounded-circle">
                    <img :src="avatar"
                         class="avatar-image"
                         alt="Image placeholder">
                  </span>
            <BMediaBody class="ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm  font-weight-bold">
                {{ user.name }}
              </span>
            </BMediaBody>
          </BMedia>
        </a>

        <template>
          <BDropdownHeader class="noti-title">
            <h6 class="text-overflow m-0">Приветствую!</h6>
          </BDropdownHeader>
          <li>
            <InertiaLink
              :href="route('profile.edit')"
              class="dropdown-item">
              <i class="ni ni-settings-gear-65"></i>
              <span>Настройки</span>
            </InertiaLink>
          </li>
          <div class="dropdown-divider"></div>
          <BDropdownItem
            @click.prevent="logout"
            href="#logout">
            <i class="ni ni-user-run"></i>
            <span>Выйти</span>
          </BDropdownItem>
        </template>
      </BaseDropdown>
    </BNavbarNav>
  </BaseNav>
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
      return this.$page.props.auth
    },
    avatar() {
      return this.user.avatar || '/admin/dist/img/no-avatar.png'
    }
  },
  methods: {
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }
  }
}
</script>
