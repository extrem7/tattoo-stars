<template>
  <div class="users-index">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <BContainer class="mt--9" fluid>
      <BRow>
        <BCol>
          <Card
            class="shadow bg-default"
            header-classes="bg-transparent"
            no-body>
            <template slot="header">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0 text-white">{{ !isTrash ? 'Пользователи' : 'Удаленные пользователи' }}</h3>
                <InertiaLink
                  v-if="!isTrash"
                  :href="route(`${resource}.trash`)"
                  class="btn btn-outline-warning">
                  Корзина
                </InertiaLink>
                <InertiaLink
                  v-else
                  :href="route(`${resource}.index`)"
                  class="btn btn-outline-success">
                  Активные
                </InertiaLink>
              </div>
            </template>
            <BCol
              class="d-flex align-items-center justify-content-center justify-content-between flex-column flex-sm-row mb-3"
              cols="12">
              <CreateBtn v-if="can('users.create')"/>
              <SearchForm
                v-model="searchQuery"
                placeholder="Имя или email"
                @search="search"
              />
            </BCol>
            <div class="table-responsive" scroll-region>
              <BTable
                v-show="total"
                ref="table"

                :busy="isBusy"
                :current-page="currentPage"
                :fields="fields"

                :items="()=>initial"
                :per-page="perPage"
                :sort-by.sync="sortBy"

                :sort-desc.sync="sortDesc"
                dark
                hover

                sort-icon-left
                @context-changed="update">
                <template v-slot:cell(icon)="{item:{icon}}">
                  <div class="avatar avatar-sm rounded-circle">
                    <img :src="icon" alt="Image placeholder">
                  </div>
                </template>
                <template v-slot:cell(emailVerified)="{item:{emailVerified}}">
                  {{ emailVerified ? 'Да' : 'Нет' }}
                </template>
                <template v-slot:cell(account_type_id)="{item:{account_type}}">
                  {{ account_type.name }}
                </template>
                <template v-slot:cell(created_at)="{item:{created_at}}">
                  {{ created_at | moment('DD.MM.YYYY HH:mm') }}
                </template>
                <template v-slot:cell(actions)="{item:{id}}">
                  <ActionsButtons
                    :id="id"
                    :resource="resource"
                    @delete="destroy(id)"
                    @restore="restore(id)"
                  />
                </template>
              </BTable>
            </div>
          </Card>
          <BPagination
            v-if="total"
            v-model="currentPage"
            :per-page="perPage"
            :total-rows="total"
            class="mt-4"
          />
        </BCol>
      </BRow>
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
    isTrash: {
      type: Boolean,
      default: false
    },
    table: Object
  },
  provide() {
    return {
      resource: this.resource,
      isTrash: this.isTrash
    }
  },
  data() {
    const fields = [
      {key: 'icon'},
      {key: 'id', sortable: true},
      {key: 'email', sortable: true},
      {key: 'account_type_id', sortable: true, label: 'Тип аккаунта'},
      {key: 'emailVerified', label: 'Email подтвержден'},
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
      initial: null,
      currentPage: null,

      searchQuery: this.table.searchQuery || '',
      sortBy: this.table.sortBy || 'id',
      sortDesc: !!this.table.sortDesc || false,

      perPage: null,
      total: null
    }
  },
  created() {
    this.fillData()
  },
  methods: {
    fillData() {
      const {data, current_page, per_page, total} = this.data
      this.initial = data
      this.currentPage = current_page
      this.perPage = per_page
      this.total = total
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

      this.$inertia.get(this.route(`${this.resource}.index`), params)
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
          console.log('Ошибка при удалении пользователя:', e)
        } finally {
          this.isBusy = false
        }
      }).catch()
    }
  }
}
</script>
