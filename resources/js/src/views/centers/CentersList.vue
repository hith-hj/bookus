<template>
  <div>

    <!-- Filters -->
    <users-list-filters
      :role-filter.sync="filterRole"
      :status-filter.sync="filterStatus"
    />
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
                v-model="userSearch"
                class="d-inline-block mr-1"
                placeholder="Search..."
              />

              <b-button
                variant="primary"
              >
                <router-link
                  :to="{ name: 'add-center'}"
                  class="text-nowrap"
                  tag="span"
                >
                  Add Center
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
        class="position-relative  overflow-visible"
        :fields="fields"
        :items="centers"
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
              :to="{ name: 'center-edit', params: { id: data.item.id } }"
            >
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Edit</span>
            </b-dropdown-item>
            <b-dropdown-item
              :to="{ name: 'add-appointment', params: { id: data.item.id } }"
            >
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Add Apointment</span>
            </b-dropdown-item>
            <b-dropdown-item
              v-b-modal.modal-prevent-closing
              @click="adminID(data.item.id)"
            >
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Add contact</span>
            </b-dropdown-item>

            <b-dropdown-item
              @click="deleteCenter(data.item.id)"
            >
              <feather-icon icon="TrashIcon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>
        <!-- name -->
        <template
          #cell(name)="data"
          class=" mt-0 pt-0 "
          style="min-width: 500px;"
        >
          <b-media
            vertical-align="center"
            class=" mt-0 pt-0"
          >
            <template #aside>
              <b-avatar
                size="32"
                :src="imgProcess(data.item.images)"
              />
            </template>
            <b-link

              class="font-weight-bold d-block text-nowrap "
            >

              <span
                :id="`tooltip-target-${data.item.id}`"
                class="xx"
                style="font-size: 14px; "
              >{{ data.item.name }}</span>

              <b-tooltip
                :target="`tooltip-target-${data.item.id}`"
                triggers="hover"
              >
                {{ data.item.images.length != 0 ?data.item.images.logo:"null" }}
              </b-tooltip>
            </b-link>
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

    <b-modal
      id="modal-prevent-closing"
      ref="modal"
      title="New Contact"
      @show="resetModal"
      @hidden="resetModal"
      @ok="handleOk"
    >
      <form
        ref="form"
        @submit.stop.prevent="handleSubmit"
      >
        <b-row>
          <b-col
            cols="6"

            class="d-flex align-items-center justify-content-start  m-0 p-0"
          >

            <label>select contact:</label>

            <v-select
              v-model="contactSelect"
              :options="listContacte"
              :clearable="false"
              class="contact d-inline-block mx-100"
            />
          </b-col>
          <b-col cols="6">

            <b-form-input
              id="name-input"
              v-model="newContact"
              :state="nameState"
              required
            />

          </b-col>
        </b-row>

      </form>
    </b-modal>
  </div>

</template>

<script>
import {
  BCard, BTooltip,
  BRow, BImg,
  BCol,
  BFormInput,
  BButton,
  BTable,
  BMedia,
  BLink,
  BBadge,
  BDropdown, BAvatar,
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
import UsersListFilters from './UsersListFilters.vue'

export default {
  components: {
    UsersListFilters,
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
      fields: ['name', 'currency', 'email', 'status', 'action'],
      listContacte: ['phone_number', 'whatsapp', 'X', 'landline', 'telegram', 'facebook'],
      centers: {},
      userSearch: null,
      perPage: 10,
      currentPage: 1,
      rows: 10,
      filterRole: null,
      filterStatus: null,
      id: '',
      nameState: null,
      submittedNames: [],
      contactSelect: '',
      newContact: '',
    }
  },
  setup() {
    const perPageOptions = [2, 5, 10, 25, 50, 100]
    return {
      avatarText,
      perPageOptions,
    }
  },
  watch: {
    userSearch(query) {
      this.getCentersFilter()
    },
    currentPage(newPage, oldPage) {
      this.getCentersFilter()
    },
    perPage(newPage, oldPage) {
      this.getCentersFilter()
    },
    filterRole() {
      this.getCentersFilter()
    },
    filterStatus() {
      this.getCentersFilter()
    },
  },
  mounted() {
    // if not login user go to login page
    const user = localStorage.getItem('user')
    if (!user) {
      this.$router.push({ name: 'login' })
    }
    this.getCenters(this.currentPage, this.perPage)
  },
  methods: {
    getCenters(page, perPage) {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      axios
        .get(`/api/admin/centers?page=${page}&limit=${perPage}`, config)
        .then(res => {
          this.centers = res.data.content
          console.log(this.centers)
        })
        .then(err => console.log(err))
    },
    deleteCenter(id) {
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
            .delete(`/api/admin/centers/${id}`, config)
            .then(response => {
              this.$swal({
                icon: 'success',
                title: 'Deleted!',
                text: 'Your file has been deleted.',
                customClass: {
                  confirmButton: 'btn btn-success',
                },
              })
              this.getCenters(this.currentPage, this.perPage)
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
    getCentersFilter() {
      let url = `/api/admin/centers?page=${
        this.currentPage
      }&limit=${
        this.perPage}`

      if (this.userSearch != '' && this.userSearch != null) {
        url = `${url}&search=${this.userSearch}`
      }
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(url, config)
        .then(res => {
          this.centers = res.data.content
          this.rows = res.data.paginator.total_count
          console.log('centers', this.centers)
          localStorage.setItem('users', JSON.stringify(this.users))
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
      if (this.userSearch != '' && this.userSearch != null) {
        formdata.append('search', this.userSearch)
      }

      axios.post('/api/admin/center_exporte_excel',
        formdata,
        { responseType: 'arraybuffer' })
        .then(response => {
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          const fileLink = document.createElement('a')
          fileLink.href = fileURL
          fileLink.setAttribute('download', `centers_${Date.now()}.xlsx`)
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

      if (this.userSearch != '' && this.userSearch != null) {
        formdata.append('search', this.userSearch)
      }

      axios.post('/api/admin/center_export_pdf',
        formdata,
        { responseType: 'arraybuffer' })
        .then(response => {
          const fileURL = window.URL.createObjectURL(new Blob([response.data]))
          const fileLink = document.createElement('a')
          fileLink.href = fileURL
          fileLink.setAttribute('download', `centers_${Date.now()}.pdf`)
          document.body.appendChild(fileLink)
          fileLink.click()
        })
        .then(err => console.log(err))
    },
    imgProcess(images) {
      if (images != null) {
        return `/storage/${images.logo}`
      }
      return null
    },
    checkFormValidity() {
      const valid = this.$refs.form.checkValidity()
      this.nameState = valid
      return valid
    },
    resetModal() {
      this.newContact = ''
      this.nameState = null
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    adminID(id) {
      this.id = id
    },
    handleSubmit() {
      // Exit when the form isn't valid
      if (!this.checkFormValidity()) {
        return
      }
      // Push the name to submitted names
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}`, 'content-type': 'multipart/form-data' },
      }
      const url = 'api/admin/edit-admin'
      const formdata = new FormData()

      formdata.append('key', this.contactSelect)
      formdata.append('value', this.newContact)

      formdata.append('id', this.id)

      axios.post('/api/admin/add_contact', formdata, config)
        .then(res => {
          this.$toast({
            component: ToastificationContent,
            timeout: 5000,
            props: {
              title: 'add new contact successfuly',
              icon: 'EditIcon',
              variant: 'success',
            },
          })
          this.msg = 'Add new contact Successufly'
          this.iserror = false
          this.issuccess = true
        }).catch(error => {
          if (error.response.status == 422) {
            this.msg = error.response.data.message
            this.iserror = true
            this.issuccess = false
          }
        })

      // Hide the modal manually
      this.$nextTick(() => {
        this.$bvModal.hide('modal-prevent-closing')
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
