<template>
  <div>
   
    <!-- Table Container Card -->
    <b-card
      no-body
      class="mb-0"
    >
      <div class="m-2">
        <!-- Table Top -->
        <b-row>
          <!-- Per Page -->
          <b-col
            cols="12"
            md="6"
            class="d-flex align-items-center justify-content-start mb-1 mb-md-0"
          >
            <label>Show</label>
            <v-select
              v-model="perPage"
              :options="perPageOptions"
              :clearable="false"
              class="per-page-selector d-inline-block mx-50"
            />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col
            cols="12"
            md="6"
          >
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input
                v-model="memberSearch"
                class="d-inline-block mr-1"
                placeholder="Search..."
              />

              <b-button
                variant="primary"
              >
                <router-link
                  :to="{ name: 'add-member'}"
                  class="text-nowrap"
                  tag="span"
                >
                  Add Member
                </router-link>
              </b-button>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <div class="d-flex justify-content-start p-0 m-0">
              <b-button
                :to="{}"
                variant="primary"
                class="d-inline mr-1 mt-sm-1 "
                @click="exportExcel"
              >
                <feather-icon icon="DownloadCloudIcon" />Export
              </b-button>

              <b-button
                :to="{}"
                variant="outline-secondary"
                class="d-inline mr-1 mt-sm-1 "
                @click="exportPdf"
              >
                <feather-icon icon="DownloadCloudIcon" />PDF
              </b-button>

            </div>
          </b-col>
        </b-row>
      </div>

      <b-table
        ref="refUserListTable"
        class="position-relative"
        :fields="fields"
        :items="members"
        responsive
        primary-key="id"
        show-empty
        empty-text="No matching records found"
      >
        <!-- Column: Actions -->
        <!-- pharmacy-verification -->
        <template #cell(action)="data">
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
              :to="{ name: 'category1-edit', params: { id: data.item.id } }"
            >
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Edit</span>
            </b-dropdown-item>

            <b-dropdown-item
              @click="delMember(data.item.id)"
            >
              <feather-icon icon="TrashIcon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>
        <!-- name -->
        <template #cell(name)="data">
          <b-media vertical-align="center">


              {{ data.item.first_name }}


          </b-media>
        </template>

        <!-- end name -->
        <!-- status -->
        <template #cell(status)="data">
          <b-badge
            pill
            :variant="`light-${data.item.status == 1 ? 'success' : 'warning'}`"
            class="text-capitalize"
          >
            {{ data.item.status == 1 ? "active" : "inactive" }}
          </b-badge>
        </template>

      </b-table>
      <div class="mx-2 mb-2">
        <b-row>
          <b-col
            cols="12"
            sm="6"
            class="
              d-flex
              align-items-center
              justify-content-center justify-content-sm-start
            "
          >
            <span class="text-muted">Showing entries</span>
          </b-col>
          <!-- Pagination -->
          <b-col
            cols="12"
            sm="6"
            class="
              d-flex
              align-items-center
              justify-content-center justify-content-sm-end
            "
          >
            <b-pagination
              v-model="currentPage"
              :total-rows="rows"
              :per-page="perPage"
              class="mb-0 mt-1 mt-sm-0"
              prev-class="prev-item"
              next-class="next-item"
            >
              <template #prev-text>
                <feather-icon
                  icon="ChevronLeftIcon"
                  size="18"
                />
              </template>
              <template #next-text>
                <feather-icon
                  icon="ChevronRightIcon"
                  size="18"
                />
              </template>
            </b-pagination>
          </b-col>
        </b-row>
      </div>
    </b-card>

  

  </div>

</template>

<script>
import {
  BCard,
  BRow,
  BCol,
  BFormInput,
  BButton,
  BTable,
  BMedia,
  BAvatar,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
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
    BCol,
    BFormInput,
    BButton,
    BTable,
    BMedia,
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
      fields: ['name', 'phone_number','email','center_position', 'action'],
      members: [],
      center:{},
      memberSearch: null,
      perPage: 10,
      currentPage: 1,
      rows: 10,
    }
  },
  setup() {
    const perPageOptions = [5, 10, 25, 50, 100]
    return {
      avatarText,
      perPageOptions,
    }
  },
  watch: {
   
    memberSearch(query) {
      this.getMembersFilter()
    },
    currentPage(newPage, oldPage) {
      this.getUsers(newPage, this.perPage)
    },
    perPage(newPage, oldPage) {
      this.getUsers(this.currentPage, newPage)
    },
   
  },
  mounted() {
    // if not login user go to login page
    const user = localStorage.getItem('user')
    if (!user) {
      this.$router.push({ name: 'login' })
    }
    this.getMembersFilter()
  },
  methods: {
    getMembers(page, perPage) {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      axios
        .get(`/api/admin/members?page=${page}&limit=${perPage}`, config)
        .then(res => {
          this.members = res.data.content
        })
        .then(err => console.log(err))
    },
    delMember(id) {
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
            .delete(`/api/admin/delete-admin/${id}`, config)
            .then(response => {
              this.$swal({
                icon: 'success',
                title: 'Deleted!',
                text: 'Your file has been deleted.',
                customClass: {
                  confirmButton: 'btn btn-success',
                },
              })
              this.getMembersFilter()
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
    getMembersFilter() {
      let url = `/api/admin/members?page=${
        this.currentPage
      }&limit=${
        this.perPage}`

      if (this.memberSearch != '' && this.memberSearch != null) {
        url = `${url}&search=${this.memberSearch}`
      }
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(url, config)
        .then(res => {
          this.center = res.data.content
          this.rows = res.data.paginator.total_count
        this.members =this.center.admins
        })
        .then(err => console.log(err))
    },

    exportExcel() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)
      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      const formdata = new FormData()
      if (this.memberSearch != '' && this.memberSearch != null) {
        formdata.append('search', this.memberSearch)
      }

      axios.post('/api/admin/user_exporte_excel',
        formdata,
        { responseType: 'arraybuffer' })
        .then(response => {
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          const fileLink = document.createElement('a')
          fileLink.href = fileURL
          fileLink.setAttribute('download', `users${Date.now()}.xlsx`)
          document.body.appendChild(fileLink)
          fileLink.click()
        })
        .then(err => console.log(err))
    },

    exportPdf() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)
      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      const formdata = new FormData()

      if (this.memberSearch != '' && this.memberSearch != null) {
        formdata.append('search', this.memberSearch)
      }

      axios.post('/api/admin/user_export_pdf',
        formdata,
        { responseType: 'arraybuffer' })
        .then(response => {
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          const fileLink = document.createElement('a')
          fileLink.href = fileURL
          fileLink.setAttribute('download', `admins_${Date.now()}.pdf`)
          document.body.appendChild(fileLink)
          fileLink.click()
        })
        .then(err => console.log(err))
    },
  },
}
</script>

<style lang="scss" scoped>
.per-page-selector {
    width: 90px;
}
.xx {

    color: #6e6b7b !important;
}
.xx:hover {
    color:#28c76f !important;
}
</style>

<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
</style>
