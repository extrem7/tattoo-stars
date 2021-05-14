<template>
  <IndexLayout
    :current-page="currentPage"
    :is-trash="isTrash"
    :per-page="perPage"
    :resource="resource"
    :total="total"
    @change:current-page="currentPage = $event">
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
    <template slot="search">
      <SearchForm
        v-model="searchQuery"
        placeholder="Имя или email"
        @search="search"
      />
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
      <template #cell(icon)="{item:{icon}}">
        <div class="avatar avatar-sm rounded-circle">
          <img :src="icon">
        </div>
      </template>
      <template #cell(emailVerified)="{item:{emailVerified}}">
        {{ emailVerified ? 'Да' : 'Нет' }}
      </template>
      <template #cell(account_type_id)="{item:{account_type}}">
        <span v-if="account_type">{{ account_type.name }}</span>
      </template>
      <template #cell(created_at)="{item:{created_at}}">
        {{ created_at | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(actions)="{item:{id}}">
        <ActionsButtons
          :id="id"
          :is-trash="isTrash"
          :resource="resource"
          @delete="destroy(id)"
          @restore="restore(id)"
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
        {key: 'icon'},
        {key: 'id', sortable: true},
        {key: 'email', sortable: true},
        {key: 'account_type_id', sortable: true, label: 'Тип аккаунта'},
        {key: 'emailVerified', label: 'Email подтвержден'},
        {key: 'name', sortable: true, label: 'Имя'},
        {key: 'roles', label: 'Роли'},
        {key: 'created_at', label: 'Зарегистрирован', thClass: 'date-column', sortable: true},
      ]
    }
  }
}
</script>
