import CreateBtn from '@/components/includes/tables/CreateBtn'
import Search from '@/components/includes/tables/SearchForm'
import ActionsButtons from '@/components/includes/tables/ActionsButtons'
import DragBtn from '@/components/includes/tables/DragBtn'

export const index = {
  data() {
    return {
      isBusy: false,
    }
  },
  provide() {
    return {
      resource: this.resource,
    }
  },
  components: {
    CreateBtn,
    ActionsButtons
  }
}

export const destroy = {
  methods: {
    async destroy(id, provided = false) {
      try {
        const destroy = await this.$confirm('Are you sure?')
        if (destroy) {
          try {
            this.isBusy = true
            const {status, data} = await this.axios.delete(this.route(`${this.resource}.destroy`, id))
            if (status === 200) {
              this.$bus.emit('alert', {text: data.status})
              if (!provided)
                this.items = this.items.filter(item => item.id !== id)
              return true
            }
          } catch (e) {
            console.log(e)
          } finally {
            this.isBusy = false
          }
        }
      } catch (e) {
      }
    }
  }
}

export const sortable = {
  methods: {
    async sort() {
      const order = this.items.map(({id}) => id)
      this.isBusy = true
      try {
        const {status, data} = await this.axios.post(this.route(`${this.resource}.sort`), {order})
        if (status === 200 && data.status) {
          this.$bus.emit('alert', {text: data.status})
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isBusy = false
      }
    }
  },
  components: {
    //Draggable: () => import( 'vuedraggable'),
    DragBtn
  }
}

export const datatable = {
  data() {

    return {
      initialized: false,
      isBusy: false,
      initial: null,

      perPage: null,
      currentPage: null,

      sortDesc: false,

      total: null,
    }
  },
  methods: {
    async destroy(resource, id) {
      if (await this.$super(destroy).destroy(resource, id, true))
        this.$refs.table.refresh()
    },
  },
  provide() {
    return {
      resource: this.resource,
    }
  },
  mixins: [index, destroy],
  components: {
    Search
  }
}

