<template>
  <div class="users-index">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>

    <BContainer class="mt--9" fluid>
      <Card
        class="shadow bg-default"
        header-classes="bg-transparent"
        no-body>
        <template slot="header">
          <h3 class="mb-0 text-white">Пользователи</h3>
        </template>
        <BCol cols="12"
              class="d-flex align-items-center justify-content-center justify-content-between flex-column flex-sm-row mb-3">
          <CreateBtn v-if="can('users.create')"/>
          <SearchForm
            v-model="searchQuery"
            @search="search"
            placeholder="Имя или email"
          />
        </BCol>
        <BTable
          :busy.async="isBusy"
          :current-page="currentPage"

          :fields="fields"
          :items="()=>initial"
          @context-changed="update"

          :per-page="perPage"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"

          hover
          dark

          ref="table"
          sort-icon-left
          v-show="total">
          <template v-slot:cell(icon)="{item:{icon}}">
            <div class="avatar avatar-sm rounded-circle">
              <img :src="icon" alt="Image placeholder">
            </div>
          </template>
          <template v-slot:cell(created_at)="{item:{created_at}}">
            {{ created_at | moment('DD.MM.YYYY HH:mm') }}
          </template>
          <template v-slot:cell(actions)="{item:{id}}">
            <ActionsButtons
              :id="id"
              :resource="resource"
              @delete="destroy(id)"
            />
          </template>
        </BTable>
        <BAlert
          v-if="!total && searchQuery.length"
          class="w-100 mt-2 text-center"
          show
          variant="warning">
          Пользователей не найдено
        </BAlert>
      </Card>
      <BPagination
        v-if="total"
        v-model="currentPage"
        :per-page="perPage"
        :total-rows="total"
        class="mt-4"
      />
    </BContainer>
  </div>
</template>

<script>
import CreateBtn from '@/components/includes/tables/CreateBtn'
import SearchForm from '@/components/includes/tables/SearchForm'
import ActionsButtons from '@/components/includes/tables/ActionsButtons'

export default {
  components: {
    CreateBtn,
    SearchForm,
    ActionsButtons
  },
  props: {
    data: Object,
    table: Object
  },
  provide() {
    return {
      resource: this.resource,
    }
  },
  data() {
    const {data, current_page, per_page, total} = this.data

    const fields = [
      {key: 'icon'},
      {key: 'id', sortable: true},
      {key: 'email', sortable: true},
      {key: 'name', sortable: true, label: 'Имя'},
      {key: 'roles', label: 'Роли'},
      {key: 'created_at', label: 'Зарегистрирован', thClass: 'date-column', sortable: true},
    ]

    if (this.can('users.edit') || this.can('users.delete')) {
      fields.push({key: 'actions', label: '', thClass: 'actions-column'})
    }

    return {
      resource: 'users',
      fields,
      isBusy: false,
      initial: data,
      currentPage: current_page,

      searchQuery: this.table.searchQuery || '',
      sortBy: this.table.sortBy || 'id',
      sortDesc: !!this.table.sortDesc || false,

      perPage: per_page,
      total: total
    }
  },
  methods: {
    update(ctx) {
      console.log(ctx)
      let {currentPage, sortBy, sortDesc} = ctx
      const params = {}

      if (sortBy !== this.sortBy) currentPage = 1

      if (currentPage !== 1) params.page = currentPage
      if (sortBy !== 'id') params.sortBy = sortBy
      if (sortDesc) params.sortDesc = +sortDesc

      if (this.searchQuery) params.searchQuery = this.searchQuery

      this.$inertia.get(this.route(`${this.resource}.index`), params)
    },
    search() {
      this.update({
        sortBy: 'id',
        currentPage: 1
      })
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
          this.$inertia.delete(this.route(`${this.resource}.destroy`, id), {
            only: ['data', 'flash'],
            preserveState: false,
            replace: true
          })
        } catch (e) {
          console.log('Ошибка при удалении пользователя:', e)
        } finally {
          this.isBusy = false
        }
      }).catch()
    }
  }
}
</script>
