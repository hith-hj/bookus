<template>
    <div>

        <!-- Filters -->
        <users-list-filters
            :roleFilter.sync="filterRole"
            :statusFilter.sync="filterStatus"
        />
        <!-- Table Container Card -->
        <b-card no-body class="mb-0">
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
                    <b-col cols="12" md="6">
                        <div class="d-flex align-items-center justify-content-end">
                            <b-form-input
                                v-model="userSearch"
                                class="d-inline-block mr-1"
                                placeholder="Search..."
                            />
<!--                            <b-button-->
<!--                                variant="primary"-->
<!--                                @click="isAddNewWholesalerSidebarActive = true"-->
<!--                            >-->
<!--                                <span class="text-nowrap">Add Admin</span>-->
<!--                            </b-button>-->
                            <b-button
                                variant="primary"
                            >
                                <router-link :to="{ name: 'add-admin'}" class="text-nowrap" tag="span">Add Admin</router-link>
                            </b-button>
                        </div>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12"   >
                        <div class="d-flex justify-content-start p-0 m-0">
                            <b-button  :to="{}"   @click="exportExcel" variant="primary"  class="d-inline mr-1 mt-sm-1 "> <feather-icon icon="DownloadCloudIcon" />Export</b-button>

                            <b-button  :to="{}"  @click="exportPdf"  variant="outline-secondary"  class="d-inline mr-1 mt-sm-1 "> <feather-icon icon="DownloadCloudIcon" />PDF</b-button>


                        </div>
                    </b-col>
                </b-row>
            </div>

            <b-table
                ref="refUserListTable"
                class="position-relative"
                :fields="fields"
                :items="users"
                responsive
                primary-key="id"
                show-empty
                empty-text="No matching records found"
            >
                <!-- Column: Actions -->
                <!-- pharmacy-verification -->
                <template #cell(action)="data">
                    <b-dropdown variant="link" no-caret>
                        <template #button-content>
                            <feather-icon
                                icon="MoreVerticalIcon"
                                size="16"
                                class="align-middle text-body"
                            />
                        </template>
                        <b-dropdown-item :to="{ name: 'admin-edit', params: { id: data.item.id } }"

                        >
                            <feather-icon icon="EditIcon" />
                            <span class="align-middle ml-50">Edit</span>
                        </b-dropdown-item>
                        <b-dropdown-item v-b-modal.modal-prevent-closing
                                         @click="adminID(data.item.id)"
                        >
                            <feather-icon icon="EditIcon" />
                            <span class="align-middle ml-50">change password</span>
                        </b-dropdown-item>

                        <b-dropdown-item @click="deleteUser(data.item.id)"


                        >
                            <feather-icon icon="TrashIcon" />
                            <span class="align-middle ml-50">Delete</span>
                        </b-dropdown-item>
                    </b-dropdown>
                </template>
                <!-- name -->
                <template #cell(name)="data">
                    <b-media vertical-align="center">
                        <template #aside>
                            <b-avatar
                                size="32"
                                :src=data.item.profile_image
                                :text="avatarText(data.item.first_name + data.item.last_name)"
                                :to="{ name: 'users-details', params: { id: data.item.id } }"
                            />
                        </template>
                        <b-link
                            :to="{ name: 'users-details', params: { id: data.item.id } }"
                            class="font-weight-bold d-block text-nowrap xx"
                        >
                            {{ data.item.first_name + "  " + data.item.last_name }}
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
                <template #cell(role)="data">

                        {{ data.item.roles.length>0? data.item.roles[0].name:""}}

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
                                <feather-icon icon="ChevronLeftIcon" size="18" />
                            </template>
                            <template #next-text>
                                <feather-icon icon="ChevronRightIcon" size="18" />
                            </template>
                        </b-pagination>
                    </b-col>
                </b-row>
            </div>
        </b-card>



       

        <b-modal
            id="modal-prevent-closing"
            ref="modal"
            title="New password"
            @show="resetModal"
            @hidden="resetModal"
            @ok="handleOk"
        >
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group
                    label="New password"
                    label-for="name-input"
                    invalid-feedback="Password is required"
                    :state="nameState"
                >
                    <b-form-input
                        id="name-input"
                        v-model="newPassword"
                        :state="nameState"
                        required
                    ></b-form-input>
                </b-form-group>
            </form>
        </b-modal>

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
} from "bootstrap-vue";
import vSelect from "vue-select";
import { avatarText } from "@core/utils/filter";
import UsersListFilters from "./UsersListFilters.vue";
import { onMounted, ref, inject } from "@vue/composition-api";

import Swal from 'sweetalert2'
import axios from "axios";
import { ThermometerIcon } from "vue-feather-icons";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
// import { methods } from 'vue-echarts'
export default {
    components: {
        UsersListFilters ,
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
            fields: ["first_name","last_name","email","role","status","action"],
            users: {},
            userSearch: null,
            perPage: 10,
            currentPage: 1,
            rows: 10,
            filterRole: null,
            filterStatus: null,

            id:"",
            newPassword: '',
            nameState: null,
            submittedNames: []
        };
    },
    setup() {
        const perPageOptions = [5, 10, 25, 50, 100];
        return {
            avatarText,
            perPageOptions,
        };
    },
    watch: {

        userSearch(query) {
            this.getUsersFilter();
        },
        currentPage(newPage, oldPage) {
            this.getUsers(newPage, this.perPage);
        },
        perPage(newPage, oldPage) {
            this.getUsers(this.currentPage, newPage);
        },
        filterRole() {
            this.getUsersFilter();
        },
        filterStatus() {
            this.getUsersFilter();
        },
    },
    mounted() {
        //if not login user go to login page
        let user = localStorage.getItem("user");
        if (!user) {
            this.$router.push({ name: "login" });
        }
        this.getUsers(this.currentPage, this.perPage);
    },
    methods: {
        getUsers(page, perPage) {
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: { Authorization: `Bearer ` + token },
            };

            axios
                .get("/api/admin/getAdmins?page=" + page + "&limit=" + perPage, config)
                .then((res) => {
                    this.users = res.data.content;

                })
                .then((err) => console.log(err));
        },
        deleteUser(id) {
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
                        .delete("/api/admin/delete-admin/" + id, config)
                        .then((response) => {
                            this.$swal({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Your file has been deleted.',
                                customClass: {
                                    confirmButton: 'btn btn-success',
                                },
                            })
                            this.getUsers(this.currentPage,  this.perPage);
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
        getUsersFilter() {
            let url =
                "/api/admin/getAdmins?page=" +
                this.currentPage +
                "&limit=" +
                this.perPage;
            if (this.filterRole != "" && this.filterRole != null) {
                url = url + "&roles=" + this.filterRole;
            }
            if (this.filterStatus != "" && this.filterStatus != null) {
                url = url + "&status=" + this.filterStatus;
            }
            if (this.userSearch != "" && this.userSearch != null) {
                url = url + "&search=" + this.userSearch;
            }
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: { Authorization: `Bearer ` + token },
            };
            axios
                .get(url, config)
                .then((res) => {
                    this.users = res.data.content;
                    this.rows = res.data.paginator.total_count;

                    localStorage.setItem("users", JSON.stringify(this.users));
                })
                .then((err) => console.log(err));
        },


    ///modallllllll
    adminID(id){

        this.id=id;
    },
    checkFormValidity() {
        const valid = this.$refs.form.checkValidity()
        this.nameState = valid
        return valid
    },
    resetModal() {
        this.newPassword = ''
        this.nameState = null
    },
    handleOk(bvModalEvent) {
        // Prevent modal from closing
        bvModalEvent.preventDefault()
        // Trigger submit handler
        this.handleSubmit()
    },
    handleSubmit() {
        // Exit when the form isn't valid
        if (!this.checkFormValidity()) {
            return
        }
        // Push the name to submitted names
        const userToken = localStorage.getItem("userToken");
        let token = JSON.parse(userToken);

        let config = {
            headers: { Authorization: `Bearer ` + token ,  "content-type": "multipart/form-data"},
        };
        let url="api/admin/edit-admin";
        let formdata = new FormData();

        formdata.append("new_password", this.newPassword);
        formdata.append("id", this.id);


        axios.post('/api/admin/change-admin-password',formdata,config)
            .then(res =>{
                    this.$toast({
                        component: ToastificationContent,
                        timeout: 5000,
                        props: {
                            title: 'change password successfuly',
                            icon: 'EditIcon',
                            variant: 'success',
                        },
                    })
                this.msg = 'Add Admin Successufly';
                this.iserror=false;
                this.issuccess=true;

            }).catch(error=>{
            if(error.response.status == 422){
                this.msg = error.response.data.message;
                this.iserror=true;
                this.issuccess=false;
            }

        });

        // Hide the modal manually
        this.$nextTick(() => {
            this.$bvModal.hide('modal-prevent-closing')
        })
    }
,
        exportExcel(){
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);
            let config = {
                headers: { Authorization: `Bearer ` + token },
            };

            let formdata = new FormData();



                //filterWholesealer

                if (this.categorySearch != "" && this.categorySearch != null) {
                    formdata.append("search", this.categorySearch);
                }
            if (this.userSearch != "" && this.userSearch != null) {
                formdata.append("search", this.userSearch);

            }

            axios.post("/api/admin/exportExcel"
                ,formdata,
                {responseType: 'arraybuffer'})
        .then(response => {

                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'admins_'+Date.now()+'.xlsx');
                document.body.appendChild(fileLink);
                fileLink.click();
            })
                .then((err) => console.log(err));

        },

        exportPdf(){
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);
            let config = {
                headers: { Authorization: `Bearer ` + token },
            };

            let formdata = new FormData();



            //filterWholesealer

            if (this.categorySearch != "" && this.categorySearch != null) {
                formdata.append("search", this.categorySearch);
            }
            if (this.userSearch != "" && this.userSearch != null) {
                formdata.append("search", this.userSearch);

            }

            axios.post("/api/admin/exportPdf"
                ,formdata,
                {responseType: 'arraybuffer'})
                .then(response => {

                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'admins_'+Date.now()+'.pdf');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                })
                .then((err) => console.log(err));

        }
    },
};
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
