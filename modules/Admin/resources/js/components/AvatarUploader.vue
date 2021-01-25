<template>
  <div class="avatar-loader">
    <div class="position-relative mb-2">
      <div class="profile-avatar">
        <img :src="avatar||'/admin/dist/img/no-avatar.png'"
             alt="profile"
             class="profile-avatar-img">
      </div>
      <button class="btn btn-action-img btn-primary btn-upload-img"
              @click.prevent="openModal">
        <img alt="add-photo" src="/admin/dist/img/plus-light.svg">
      </button>
      <InertiaLink
          v-if="avatar"
          :href="route('users.avatar.destroy',id)"
          as="button"
          class="btn btn-action-img btn-danger btn-delete"
          method="delete">
        <img alt="delete-photo" src="/admin/dist/img/delete-white.svg">
      </InertiaLink>
    </div>
    <input ref="input"
           accept="image/*"
           class="d-none"
           name="image"
           type="file"
           @change="setImage">
    <Modal :show.sync="isModalOpen">
      <h3 slot="header" class="modal-title">Загрузить фото</h3>
      <div class="tattoo-image-uploader form-group">
        <div class="company-info-logo d-flex justify-content-center">
          <BaseButton
              size="lg"
              type="info"
              @click.prevent="showFileChooser">
            Выбрать файл
          </BaseButton>
        </div>
        <VueCropper
            v-show="image"
            ref="cropper"
            :src="image"
            class="vue-cropper w-100"/>
      </div>
      <template slot="footer">
        <div v-show="image"
             class="modal-actions mt-3">
          <BaseButton
              :outline="true"
              type="primary"
              @click="cancel">
            Отменить
          </BaseButton>
          <BaseButton
              :outline="true"
              type="success"
              @click="upload">
            Загрузить
          </BaseButton>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import VueCropper from 'vue-cropperjs'

export default {
  components: {
    VueCropper
  },
  props: {
    id: Number,
    avatar: String
  },
  data() {
    return {
      imageName: null,
      image: null,
      isModalOpen: false
    }
  },
  methods: {
    async upload() {
      const form = new FormData
      const avatar = await this.getBlob()
      form.append('avatar', avatar, avatar.name)

      this.$inertia.post(this.route('users.avatar.update', this.id), form, {
        onSuccess: page => {
          if (!page.props.flash.error) this.isModalOpen = false
        }
      })
    },
    cancel() {
      this.resetImage()
      this.$bvModal.hide('modal-avatar')
    },
    async getBlob() {
      return new Promise((resolve) => {
        if (!this.image) resolve(null)
        const canvas = this.$refs.cropper.getCroppedCanvas()
        if (canvas)
          canvas.toBlob((blob) => {
            blob.name = this.imageName
            resolve(blob)
          })

      })
    },
    resetImage() {
      this.image = null
      this.$refs.cropper.reset()
      this.errors = {}
    },
    setImage(e) {
      const file = e.target.files[0]
      if (file.type.indexOf('image/') === -1) {
        alert('Please select an image file')
        return
      }
      this.imageName = file.name
      const reader = new FileReader()
      reader.onload = (event) => {
        this.image = event.target.result
        this.$refs.cropper.replace(event.target.result)
      }
      reader.readAsDataURL(file)
    },
    showFileChooser() {
      this.$refs.input.click()
    },
    openModal() {
      this.isModalOpen = true
      this.$refs.input.click()
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~cropperjs/dist/cropper.css';

.avatar-loader {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.btn-action-img {
  width: 26px;
  height: 26px;
  position: absolute;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  flex-shrink: 0;

  img {
    width: 13px;
  }
}

.profile-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background-color: #EFECE8;
  border-radius: 50%;
  overflow: hidden;
  width: 130px;
  height: 130px;
  position: relative;
}

.profile-avatar-img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}

.btn-upload-img {
  bottom: 3px;
  right: 15px;
}

.btn-delete {
  right: -5px;
  bottom: 30px;
}
</style>
