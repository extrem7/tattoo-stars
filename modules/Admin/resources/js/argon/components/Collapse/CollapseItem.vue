<template>
  <b-card no-body>
    <b-card-header :aria-expanded="active" class="card-header" role="tab">
      <a
        :aria-controls="`content-${itemId}`"
        :href="`#${itemId}`"
        data-parent="#accordion"
        data-toggle="collapse"
        @click.prevent="activate"
      >
        <slot name="title"> {{ title }}</slot>
        <i class="tim-icons icon-minimal-down"></i>
      </a>
    </b-card-header>
    <collapse-transition :duration="animationDuration">
      <div
        v-show="active"
        :id="`content-${itemId}`"
        :aria-labelledby="title"
        class="collapsed"
        role="tabpanel"
      >
        <div class="card-body">
          <slot></slot>
        </div>
      </div>
    </collapse-transition>
  </b-card>
</template>
<script>
import {CollapseTransition} from 'vue2-transitions'

export default {
  name: 'collapse-item',
  components: {
    CollapseTransition
  },
  props: {
    title: {
      type: String,
      default: '',
      description: 'Collapse item title'
    },
    id: String
  },
  inject: {
    animationDuration: {
      default: 250
    },
    multipleActive: {
      default: false
    },
    addItem: {
      default: () => {
      }
    },
    removeItem: {
      default: () => {
      }
    },
    deactivateAll: {
      default: () => {
      }
    }
  },
  computed: {
    itemId() {
      return this.id || this.title
    }
  },
  data() {
    return {
      active: false
    }
  },
  methods: {
    activate() {
      let wasActive = this.active
      if (!this.multipleActive) {
        this.deactivateAll()
      }
      this.active = !wasActive
      console.log(this.active)
    }
  },
  mounted() {
    this.addItem(this)
  },
  destroyed() {
    if (this.$el && this.$el.parentNode) {
      this.$el.parentNode.removeChild(this.$el)
    }
    this.removeItem(this)
  }
}
</script>
<style></style>
