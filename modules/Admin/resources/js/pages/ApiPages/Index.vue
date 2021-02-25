<template>
  <IndexLayout
    :current-page="currentPage"
    :is-trash="isTrash"
    :per-page="perPage"
    :resource="resource"
    :total="total"
    @change:current-page="currentPage = $event">
    <template slot="header">
      <h3 class="mb-0 text-white">Страницы приложения</h3>
    </template>
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
      <template #cell(created_at)="{item:{created_at}}">
        {{ created_at | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(updated_at)="{item:{updated_at}}">
        {{ updated_at | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(actions)="{item:{id}}">
        <ActionsButtons
          :id="id"
          :resource="resource"
          @delete="destroy(id)"
        />
      </template>
    </BTable>
  </IndexLayout>
</template>

<script>
import index from '@/mixins/crud/index'

export default {
  mixins: [index],
  data() {
    return {
      fields: [
        {key: 'id', sortable: true},
        {key: 'title', label: 'Заголовок', sortable: true},
        {key: 'created_at', label: 'Создана', thClass: 'date-column', sortable: true},
        {key: 'updated_at', label: 'Оновлена', thClass: 'date-column', sortable: true},
      ]
    }
  }
}
</script>
