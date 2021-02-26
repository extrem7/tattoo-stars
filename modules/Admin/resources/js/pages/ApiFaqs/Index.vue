<template>
  <IndexLayout
    :current-page="currentPage"
    :resource="resource"
    :use-pagination="false">
    <template slot="header">
      <h3 class="mb-0 text-white">Часто задаваемые вопросы</h3>
    </template>
    <table class="table table-dark table-hover">
      <thead>
      <tr>
        <th></th>
        <th class="id-column">Id</th>
        <th>Вопрос</th>
        <th>Создан</th>
        <th>Отредактирован</th>
        <th class="actions-column"></th>
      </tr>
      </thead>
      <draggable v-model="items" handle=".drag" tag="tbody" @update="sort">
        <tr v-for="{id,question,created_at,updated_at} in items" :key="id">
          <td class="vertical-align-center p-0">
            <div class="drag">
              <i class="fa fa-hand-rock" style="width:20px;"></i>
            </div>
          </td>
          <td>{{ id }}</td>
          <td>{{ question }}</td>
          <td>{{ created_at | moment('DD.MM.YYYY HH:mm') }}</td>
          <td> {{ updated_at | moment('DD.MM.YYYY HH:mm') }}</td>
          <td>
            <ActionsButtons
              :id="id"
              :resource="resource"
              @delete="destroy(id)"/>
          </td>
        </tr>
      </draggable>
    </table>
  </IndexLayout>
</template>

<script>
import Draggable from 'vuedraggable'
import index from '@/mixins/crud/index'

export default {
  components: {
    Draggable
  },
  mixins: [index],
  props: {
    faqs: Array
  },
  data() {
    return {
      items: this.faqs,
      fields: [
        {key: 'id', sortable: true},
        {key: 'title', label: 'Заголовок', sortable: true},
        {key: 'created_at', label: 'Создана', thClass: 'date-column', sortable: true},
        {key: 'updated_at', label: 'Оновлена', thClass: 'date-column', sortable: true},
      ]
    }
  },
  methods: {
    async sort() {
      const order = this.items.map(({id}) => id)
      this.isBusy = true
      try {
        await this.$inertia.post(this.route(`${this.resource}.sort`), {order}, {
          preserveState: true,
          preserveScroll: true
        })
      } catch (e) {
        console.log(e)
      } finally {
        this.isBusy = false
      }
    }
  },
}
</script>
