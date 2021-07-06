import ActionsButtons from '@/components/includes/tables/ActionsButtons'

export default {
  components: {
    ActionsButtons,
    SearchForm: () => import('@/components/includes/tables/SearchForm'),
    IndexLayout: () => import('@/components/includes/crud/Index')
  },
  provide() {
    return {
      resource: this.resource,
      isTrash: this.isTrash || false
    }
  },
  props: {
    resource: String,
    data: Object,
    table: Object,
    isTrash: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      isBusy: false,

      initial: null,
      currentPage: null,
      perPage: null,
      total: null,

      searchQuery: null,
      sortBy: null,
      sortDesc: null,

      actionsClass: 'actions-column'
    }
  },
  created() {
    if (this.data) {
      this.fillData()
    }
    if (this.can([`${this.resource}.edit`, `${this.resource}.delete`])) {
      this.fields.push({key: 'actions', label: '', thClass: this.actionsClass})
    }
  },
  methods: {
    fillData() {
      const {data, current_page, per_page, total} = this.data
      this.initial = data
      this.currentPage = current_page
      this.perPage = per_page
      this.total = total

      this.searchQuery = this.table.searchQuery || ''
      this.sortBy = this.table.sortBy || 'id'
      this.sortDesc = !!this.table.sortDesc || false
    },
    update(ctx) {
      console.log(ctx)
      let {currentPage, sortBy, sortDesc} = ctx
      const params = {}

      if (sortBy !== this.sortBy) currentPage = 1

      if (currentPage !== 1) params.page = currentPage
      if (sortBy !== 'id') params.sortBy = sortBy
      if (sortDesc) params.sortDesc = +sortDesc

      if (this.searchQuery) params.searchQuery = this.searchQuery

      this.$inertia.get(this.route(`${this.resource}.${!this.isTrash ? 'index' : 'trash'}`), params)
    },
    search() {
      this.update({
        sortBy: 'id',
        currentPage: 1
      })
    },
    async restore(id) {
      try {
        this.isBusy = true
        this.$inertia.post(this.route(`${this.resource}.restore`, id), null, {
          only: ['data', 'flash'],
          preserveState: false,
          preserveScroll: true,
          replace: true
        })
      } catch (e) {
        console.log('Ошибка при восстановлении пользователя:', e)
      } finally {
        this.isBusy = false
      }
    },
    async destroy(id) {
      this.$confirm('Вы уверены?', null, null, {
        confirmButtonText: 'Да',
        cancelButtonText: 'Нет',
        buttonsStyling: false,
        customClass: {
          popup: 'bg-secondary',
          confirmButton: 'btn base-button btn-primary',
          cancelButton: 'btn base-button btn-danger'
        }
      }).then(async () => {
        try {
          this.isBusy = true
          this.$inertia.delete(this.route(`${this.resource}.${!this.isTrash ? 'destroy' : 'forceDestroy'}`, id), {
            only: ['data', 'flash'],
            preserveState: false,
            preserveScroll: true,
            replace: true
          })
        } catch (e) {
          console.log('Ошибка при удалении:', e)
        } finally {
          this.isBusy = false
        }
      }).catch()
    }
  }
}
