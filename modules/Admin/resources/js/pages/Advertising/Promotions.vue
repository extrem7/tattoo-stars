<template>
  <IndexLayout
    :current-page="currentPage"
    :is-trash="isTrash"
    :per-page="perPage"
    :resource="resource"
    :total="total"
    @change:current-page="currentPage = $event">
    <template slot="header">
      <h3 class="mb-0 text-white">Реклама публикаций</h3>
    </template>
    <p class="pl-3 text-white">
      Всего: {{ totals.total }}, активных: {{ totals.active }}, на паузе: {{ totals.paused }}.
    </p>
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
      <template #cell(user)="{item:{user}}">
        ID:{{ `${user.id} ${user.nickname}` }}
      </template>
      <template #cell(on_pause)="{item:{on_pause}}">
        {{ on_pause ? 'Да' : 'Нет' }}
      </template>
      <template #cell(verified)="{item:{id,verified}}">
        <div v-if="verified===null">
          <BaseButton
            v-if="can(`${resource}.verify`)"
            type="success"
            class="mr-0 mb-2"
            size="sm"
            @click.prevent="verify(id)"
          >
            Подтвердить
          </BaseButton>
          <BaseButton
            v-if="can(`${resource}.reject`)"
            class="d-block"
            type="warning"
            size="sm"
            @click.prevent="reject(id)"
          >
            Отказать
          </BaseButton>
        </div>
        <span v-else> {{ verified ? 'Да' : 'Нет' }}</span>
      </template>
      <template #cell(created_at)="{item:{created_at}}">
        {{ created_at | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(updated_at)="{item:{updated_at}}">
        {{ updated_at | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(actions)="{item:{id,thumbnail}}">
        <ActionsButtons
          :id="id"
          :resource="resource"
          @delete="destroy(id)"
        >
          <a
            href="#"
            class="btn btn-primary"
            @click.prevent="show(thumbnail)"
          >Просмотреть</a>
        </ActionsButtons>
      </template>
    </BTable>
  </IndexLayout>
</template>

<script>
import index from '@/mixins/crud/index'
import Swal from 'sweetalert2'

export default {
  mixins: [index],
  props: {
    totals: Object
  },
  data() {
    return {
      fields: [
        {key: 'id', sortable: true},
        {key: 'user', label: 'Пользователь'},
        {key: 'location', label: 'Локация', sortable: true},
        {key: 'account_type', label: 'Показывать', sortable: true},
        {key: 'clicks', label: 'Кликов', sortable: true},
        {key: 'views', label: 'Просмотров', sortable: true},
        {key: 'budget', label: 'Бюджет', sortable: true},
        {key: 'on_pause', label: 'Остановлена'},
        {key: 'verified', label: 'Подтверждена'},
        {key: 'reject_reason', label: 'Причина отказа'},
        {key: 'created_at', label: 'Создана', thClass: 'date-column', sortable: true},
        {key: 'updated_at', label: 'Оновлена', thClass: 'date-column', sortable: true},
      ]
    }
  },
  methods: {
    async verify(id) {
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
          this.$inertia.post(this.route(`${this.resource}.verify`, id), null, {
            only: ['data', 'flash'],
            preserveState: false,
            preserveScroll: true,
            replace: true
          })
        } catch (e) {
          console.log('Ошибка при подтверждении:', e)
        } finally {
          this.isBusy = false
        }
      }).catch()
    },
    async reject(id) {
      const {value: reject_reason} = await Swal.fire({
        text: 'Введите причину отказа',
        input: 'text',
        confirmButtonText: 'Да',
        cancelButtonText: 'Нет',
        buttonsStyling: false,
        customClass: {
          popup: 'bg-secondary',
          confirmButton: 'btn base-button btn-primary',
          cancelButton: 'btn base-button btn-danger'
        }
      })
      if (reject_reason) {
        try {
          this.isBusy = true
          this.$inertia.post(this.route(`${this.resource}.reject`, id), {reject_reason}, {
            only: ['data', 'flash'],
            preserveState: false,
            preserveScroll: true,
            replace: true
          })
        } catch (e) {
          console.log('Ошибка при отказе:', e)
        } finally {
          this.isBusy = false
        }
      }
    },
    show(url) {
      Swal.fire({
        imageUrl: url,
        // imageHeight: 1500,
      })
    }
  }
}
</script>
