<template>
  <div class="user-create-or-edit">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <div class="container-fluid mt--9">
      <BRow align-h="center">
        <BCol lg="8" md="10" xl="5">
          <div class="card-wrapper">
            <ValidationObserver ref="formValidator" v-slot="{handleSubmit}">
              <BForm
                ref="form"
                @submit.prevent="handleSubmit(submit)">
                <BCard
                  bg-variant="secondary"
                  no-body>
                  <BCardHeader header-bg-variant="transparent">
                    <h3 class="mb-0">
                      {{ !isEdit ? 'Создать нового' : 'Редактировать' }}
                      {{ !isProfile ? 'пользователя' : 'профиль' }}
                      <span v-if="isEdit && !isProfile">{{ user.name }}</span>
                    </h3>
                  </BCardHeader>
                  <BCardBody>
                    <AvatarUploader
                      v-if="isEdit"
                      :id="user.id"
                      :avatar="user.avatar"
                    />

                    <BaseInput
                      v-model="form.name"
                      :rules="{required: true,max:255}"
                      alternative
                      class="mb-3"
                      label="Имя"
                      name="name"
                      placeholder="Имя"
                    />
                    <BaseInput
                      v-model="form.nickname"
                      :rules="{required: true,max:255}"
                      alternative
                      class="mb-3"
                      label="Никнейм"
                      name="nickname"
                      placeholder="Никнейм"
                    />
                    <BaseInput
                      v-model="form.email"
                      :rules="{required: true, email: true}"
                      alternative
                      class="mb-3"
                      label="Email"
                      name="email"
                      placeholder="Email"
                      prepend-icon="ni ni-email-83"
                      type="email"
                    />
                    <BaseInput
                      v-model="form.password"
                      :rules="{required: !isEdit, min: 8}"
                      alternative
                      class="mb-3"
                      label="Пароль"
                      name="password"
                      placeholder="Пароль"
                      prepend-icon="ni ni-lock-circle-open"
                    />
                    <BaseInput
                      v-if="accountTypes && accountTypes.length"
                      alternative
                      class="mb-3"
                      label="Тип аккаунта"
                      name="role">
                      <BFormSelect
                        v-model="form.account_type_id"
                        :options="accountTypes"/>
                    </BaseInput>
                    <BaseInput
                      v-if="roles && roles.length"
                      alternative
                      class="mb-3"
                      label="Роли"
                      name="role">
                      <BFormSelect
                        v-model="form.roles"
                        :options="roles"
                        :select-size="4"
                        multiple/>
                    </BaseInput>
                  </BCardBody>
                  <BCardFooter
                    class="d-flex justify-content-between"
                    footer-bg-variant="transparent">
                    <BBtn
                      variant="primary"
                      @click="back">
                      Назад
                    </BBtn>
                    <BBtn
                      type="submit"
                      variant="success">
                      {{ !isEdit ? 'Создать' : 'Сохранить' }}
                    </BBtn>
                  </BCardFooter>
                </BCard>
              </BForm>
            </ValidationObserver>
          </div>
          <div class="card-wrapper mt-4">
            <BCard
              bg-variant="secondary"
              no-body>
              <BCardBody>
                <BFormGroup label="Пол">
                  <BFormInput :value="user.gender_id?user.gender.name:null" disabled/>
                </BFormGroup>
                <BFormGroup label="День рождения">
                  <BFormInput :value="user.birthday" disabled/>
                </BFormGroup>
                <BFormGroup label="Город">
                  <BFormInput :value="user.city_id?`${user.city.name}, ${user.city.country.name}`:null" disabled/>
                </BFormGroup>
                <BFormGroup label="Адрес">
                  <BFormInput :value="user.address" disabled/>
                </BFormGroup>
                <BFormGroup label="Био">
                  <BFormInput :value="user.bio" disabled/>
                </BFormGroup>
                <BFormGroup label="Телефон">
                  <BFormInput :value="user.phone" disabled/>
                </BFormGroup>
                <BFormGroup label="Вебсайт">
                  <BLink :href="user.website" target="_blank">{{ user.website }}</BLink>
                </BFormGroup>
                <BFormGroup label="Instagram">
                  <BLink :href="user.instagram" target="_blank">{{ user.instagram }}</BLink>
                </BFormGroup>
                <BFormGroup label="Facebook">
                  <BLink :href="user.facebook" target="_blank">{{ user.facebook }}</BLink>
                </BFormGroup>
                <BListGroup v-if="user.styles.length">
                  <h3 class="mb-2">Стили</h3>
                  <BListGroupItem
                    v-for="{id,name} in user.styles"
                    :key="id">
                    {{ name }}
                  </BListGroupItem>
                </BListGroup>
              </BCardBody>
            </BCard>
          </div>
        </BCol>
      </BRow>
    </div>
  </div>
</template>

<script>
import AvatarUploader from '@/components/AvatarUploader'

export default {
  props: {
    errors: Object,
    user: Object,
    accountTypes: Array,
    roles: Array,
    isProfile: Boolean
  },
  components: {
    AvatarUploader
  },
  data() {
    return {
      form: {
        account_type_id: 1,
        name: '',
        nickname: '',
        email: '',
        password: '',
        role: [],
      }
    }
  },
  computed: {
    isEdit() {
      return !!this.user
    }
  },
  created() {
    this.$inertia.on('error', e => {
      this.$refs.formValidator.setErrors(this.errors)
    })

    if (this.isEdit) {
      this.form.account_type_id = this.user.account_type_id
      this.form.name = this.user.name
      this.form.nickname = this.user.nickname
      this.form.email = this.user.email
      this.form.roles = this.user.roles
      this.form.password = ''
    }
  },
  methods: {
    submit() {
      let updateRoute = this.route('profile.update')
      if (!this.isProfile && this.isEdit) {
        updateRoute = this.route('users.update', this.user.id)
      }

      this.$inertia.visit(
        !this.isEdit ? this.route('users.index') : updateRoute,
        {
          method: !this.isEdit ? 'post' : 'patch',
          data: this.form,
          preserveState: !this.isEdit
        })
    },
    back() {
      history.back()
    }
  }
}
</script>
