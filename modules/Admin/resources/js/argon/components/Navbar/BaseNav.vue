<template>
  <b-navbar :class="classes" toggleable>
    <div :class="containerClasses">
      <slot name="brand"></slot>

      <slot name="toggle-button">
        <button
          v-if="hasMenu"
          aria-expanded="false"
          aria-label="Toggle navigation"
          class="navbar-toggler collapsed"
          type="button"
          @click="toggleMenu"
        >
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
      </slot>

      <b-navbar-toggle
        target="nav-text-collapse"
        @click.stop="toggleMenu">
      </b-navbar-toggle>

      <b-collapse
        id="nav-text-collapse"
        v-click-outside="closeMenu"
        :class="menuClasses"
        :visible="show"
        class="navbar-custom-collapse collapse"
        is-nav>
        <slot :close-menu="closeMenu"></slot>
      </b-collapse>
    </div>
  </b-navbar>
</template>
<script>
export default {
  name: 'BaseNav',
  props: {
    show: {
      type: Boolean,
      default: false,
      description:
          'Whether navbar menu is shown (valid for viewports < specified by `expand` prop)'
    },
    transparent: {
      type: Boolean,
      default: false,
      description: 'Whether navbar is transparent'
    },
    expand: {
      type: String,
      default: 'lg',
      description: 'Breakpoint where nav should expand'
    },
    menuClasses: {
      type: [String, Object, Array],
      default: '',
      description:
          'Navbar menu (items) classes. Can be used to align menu items to the right/left'
    },
    containerClasses: {
      type: [String, Object, Array],
      default: 'container',
      description:
          'Container classes. Can be used to control container classes (contains both navbar brand and menu items)'
    },
    type: {
      type: String,
      default: '',
      validator(value) {
        return [
          '',
          'dark',
          'success',
          'danger',
          'warning',
          'white',
          'primary',
          'light',
          'info',
          'vue'
        ].includes(value)
      },
      description: 'Navbar color type'
    }
  },
  model: {
    prop: 'show',
    event: 'change'
  },
  computed: {
    classes() {
      let color = `bg-${this.type}`
      let classes = [
        {'navbar-transparent': this.transparent},
        {[`navbar-expand-${this.expand}`]: this.expand}
      ]
      if (this.position) {
        classes.push(`navbar-${this.position}`)
      }
      if (!this.transparent) {
        classes.push(color)
      }
      return classes
    },
    hasMenu() {
      return this.$slots.default
    }
  },
  methods: {
    toggleMenu() {
      this.$emit('change', !this.show)
    },
    closeMenu() {
      this.$emit('change', false)
    }
  }
}
</script>
<style></style>
