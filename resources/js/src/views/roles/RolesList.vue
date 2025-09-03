<template>

 <div>
     <!-- <div>hi</div> -->
      <!-- Filters -->
    <!-- <region-list-filters :typeFilter.sync="filterType" :statusFilter.sync="filterStatus" /> -->

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
               v-model="regionSearch"
                class="d-inline-block mr-1"
                placeholder="Search..."
              />
              <!-- <input type="text" class="form-control" placeholder="search.." v-model="regionSearch" name="regionSearch"> -->
              <b-button
                variant="primary"
            v-if="true"
              >
                <router-link :to="{ name: 'add-role'}" class="text-nowrap" tag="span">Add Role</router-link>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table
        ref="refUserListTable"
        class="position-relative"
        :fields="fields"
        :items="roles"
        responsive
        primary-key="id"
        show-empty
        empty-text="No matching records found"
      >

   <!-- Column: First name -->
        <template #cell(name)="data">

           <b class="text-primary xx">{{ data.value.toUpperCase() }}</b>
        </template>
          <!-- Column: Actions -->
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
            v-if="$store.getters['auth/isExistPermission']('update_roles')"
              :to="{ name: 'edit-roles', params: { id: data.item.id } }"
            >
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Edit</span>
            </b-dropdown-item>
            <b-dropdown-item @click="deleteRole(data.item.id)"
            v-if="$store.getters['auth/isExistPermission']('delete_roles')"
            >
              <feather-icon icon="TrashIcon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>

      </b-table>
     <div class="mx-2 mb-2">
        <b-row>

          <b-col
            cols="12"
            sm="6"
            class="d-flex align-items-center justify-content-center justify-content-sm-start"
          >
            <span class="text-muted">Showing entries</span>
          </b-col>
          <!-- Pagination -->
          <b-col
            cols="12"
            sm="6"
            class="d-flex align-items-center justify-content-center justify-content-sm-end"
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
import {  BCard,
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
  BPagination, } from 'bootstrap-vue'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import UserListAddNew from './UserListAddNew.vue'
// import RegionListFilters from './RegionListFilters.vue'
import store from '@/store/index'
import { onMounted, ref ,inject} from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import { ThermometerIcon } from 'vue-feather-icons'
// import { methods } from 'vue-echarts'
export default {
  components: {
    UserListAddNew,
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
   data(){
      return {
        fields: ['id','name', 'action'],
       roles : {},
       regionSearch: null,
        perPage: 10, currentPage: 1, rows:10
        ,filterType:null,
        filterStatus:null
      }
  },
   setup() {
     const perPageOptions = [5,10, 25, 50, 100]
    return {
      avatarText,perPageOptions
    }
  },
  watch: {
    regionSearch(query){
      this.getCitiesFilter();

    },
    currentPage(newPage, oldPage) {
      this.getRegions(newPage,this.perPage);
    },
     perPage(newPage, oldPage) {
      this.getRegions(this.currentPage,newPage);
    },
       filterType(){
      this.getCitiesFilter();
    },
    filterStatus(){
      this.getCitiesFilter();
    }


  },
  mounted() {
    //if not login user go to login page
  // let user=localStorage.getItem("user");
  // if(!user){
  //  this.$router.push({ name: 'login' });
  // }
    this.getRoles();
  },
  methods:{

    getRoles()
    {
        const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"Authorization" : `Bearer ` + token}
          }
          axios.get("/api/admin/roles", config)
      .then(res =>{
        this.roles = res.data.content;
        console.log(this.roles);
      })
      .then(err => console.log(err))
    },

     deleteRole(id) {
    const userToken = localStorage.getItem("userToken");
      let token = JSON.parse(userToken);
       let config = {
        headers: { Authorization: `Bearer ` + token },
      };
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
              .delete('/api/web/delete-role/' + id,config)
              .then((response) => {
          this.$swal({
            icon: 'success',
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
                                                          this.getRoles;
           });
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


  }
}
</script>


<style lang="scss" scoped>
.per-page-selector {
  width:
 90px;
}
.xx {

  color: #6e6b7b !important;
}
.xx:hover {
    color:#28c76f !important;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
