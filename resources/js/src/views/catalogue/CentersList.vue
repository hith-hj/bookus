<template>
  <div>
    <b-card class="mb-0">
      <b-row>
        <b-col
          cols="7"
          class="mb-2"
        >
          <h2 class="mb-0">
            Services menu
          </h2>
        </b-col>
        <b-col
          cols="5"
          class="mb-2"
        >
          <div class="d-flex justify-content-start p-0 m-0">
            <b-button
              :to="{ name: 'add-serv-center'}"
              variant="primary"
              class="d-inline mr-1 mt-sm-1 "
            >
              <feather-icon icon="ServerIcon" /><span>Add service</span>
            </b-button>

            <b-button
              :to="{ name: 'add-cat-center'}"
              variant="outline-secondary"
              class="d-inline  mt-sm-1 "
            >
              <feather-icon icon="GridIcon" />Add Category
            </b-button>

          </div>
        </b-col>
        <b-col
          v-for="category in categories"
          :key="category.id"
          cols="10"
          class="m-1"
        >
          <span class="mb-0 text-uppercase h1">{{ category.name }}     </span>
          <b-link
            :to="{ name: 'dit-center-cat', params: { id: category.id } }"
            class="link-opacity-50 h3 text-muted"
          >
            Edit
          </b-link>

          <b-row
            v-for="service in category.Services"
            :key="service.id"
            class="m-1 d-flex align-items-between w-100 border border-light rounded bg-light "
          >
            <b-col
              cols="6"
              class="my-1"
            >
              <h3>{{ service.name }}</h3>
            </b-col>
            <b-col
              cols="3"
              class="my-1"
            >
              <h5>{{ service.retail_price + " "+center.currency }}</h5>
            </b-col>
            <b-col
              cols="3"
              class="my-1"
            >
              <div class="d-flex justify-content-around p-0 m-0">
                <h4>{{ service.Duration }}</h4>

                <b-dropdown
                  variant="link"
                  no-caret
                >
                  <template #button-content>
                    <feather-icon
                      icon="MoreVerticalIcon"
                      size="16"
                      class="align-middle text-body"
                    />
                  </template>
                  <b-dropdown-item
                    to=""
                  >
                    <feather-icon icon="EditIcon" />
                    <span class="align-middle ml-50">Edit</span>
                  </b-dropdown-item>
                  <b-dropdown-item
                    to=""
                  >
                    <feather-icon icon="EditIcon" />
                    <span class="align-middle ml-50">Add Apointment</span>
                  </b-dropdown-item>
                  <b-dropdown-item
                    v-b-modal.modal-prevent-closing
                  >
                    <feather-icon icon="EditIcon" />
                    <span class="align-middle ml-50">Add contact</span>
                  </b-dropdown-item>

                  <b-dropdown-item
                    @click="deleteService(service.id)"
                  >
                    <feather-icon icon="TrashIcon" />
                    <span class="align-middle ml-50">Delete</span>
                  </b-dropdown-item>
                </b-dropdown>
              </div>
            </b-col>
          </b-row>
          <div class="divider my-2" />
        </b-col>
      </b-row>
    </b-card>
  </div>
</template>

<script>
import {
  BCard,
  BTooltip,
  BRow,
  BImg,
  BCol,
  BFormInput,
  BButton,
  BTable,
  BMedia,
  BLink,
  BBadge,
  BDropdown,
  BAvatar,
  BDropdownItem,
  BPagination,
  BIconTelegram,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import { onMounted, ref, inject } from '@vue/composition-api'

import Swal from 'sweetalert2'
import axios from 'axios'
import { ThermometerIcon } from 'vue-feather-icons'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
  components: {
    BCard,
    BRow,
    BTooltip,
    BCol,
    BFormInput,
    BButton,
    BTable,
    BMedia,
    BImg,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,
    vSelect,
  },
  data() {
    return {
      categories: {},
      center: {},
    }
  },
  setup() {
    const perPageOptions = [5, 10, 25, 50, 100]
    return {
      avatarText,
      perPageOptions,
    }
  },
  watch: {},
  mounted() {
    this.getCategories()
  },
  methods: {
    getCategories() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)
      const userDetails = this.$store.getters['auth/getUser']
      console.log('user', userDetails)
      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(`/api/admin/get-center-categories/${userDetails.id}`, config)
        .then(res => {
          this.categories = res.data.content.categories
          this.center = res.data.content.center
          console.log('center', this.center)
        })
        .catch(err => {
          console.log(err)
        })
    },
    deleteService(id) {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)
      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      this.$swal({
        title: 'Are you sure?',
        text: "You won't be able to delete this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          axios
            .delete(`/api/admin/center-service/${id}`, config)
            .then(response => {
              this.$swal({
                icon: 'success',
                title: 'Deleted!',
                text: 'Your file has been deleted.',
                customClass: {
                  confirmButton: 'btn btn-success',
                },
              })
              this.getCategories()
            })
        } else if (result.dismiss === 'cancel') {
          this.$swal({
            title: 'Cancelled',
            text: 'Your imaginary file is safe :)',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
        }
      })
    },
  },
}
</script>

<style lang="scss" scoped>
.per-page-selector {
  width: 90px;
}
.contact {
  width: 150px;
}

</style>

<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
</style>
