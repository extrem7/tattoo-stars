<template>
  <div class="user-create-or-edit">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <div class="container-fluid mt--9">
      <b-row align-h="center">
        <b-col lg="4">
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
        </b-col>
      </b-row>
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
