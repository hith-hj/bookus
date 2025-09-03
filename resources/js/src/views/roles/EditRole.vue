<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->
            <b-tab active>
                <template #title>
                    <feather-icon
                        icon="UserIcon"
                        size="16"
                        class="mr-0 mr-sm-50"
                    />
                    <span class="d-none d-sm-inline">Edit permissions</span>
                </template>
                <div>

                    <!-- Media -->
                    <b-media class="mb-2">
                        <template #aside>

                        </template>
                     

                    </b-media>
                    <error-msg v-show="iserror" :msg="msg"></error-msg>
                    <success-msg v-show="issuccess" :msg="msg"></success-msg>

                    <validation-observer ref="simpleRules">
                        <!-- User Info: Input Fields -->
                        <b-form>
                            <b-row>

                                <!-- Field:  Name -->
                               <!-- Field: First Name -->
        <b-col
          cols="12"
          md="6"
        >
          <b-form-group
            label="Role Name"
            label-for="role_Name"
          >
            <validation-provider
                #default="{ errors }"
                name="Role Name"
                rules="required"
              >
            <b-form-input
              :state="errors.length > 0 ? false:null"
              v-model="role.name"
                  placeholder=""
            />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
          </b-form-group>
        </b-col>

        <table class="table">
  <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">read</th>
      <th scope="col">add</th>
      <th scope="col">update</th>
      <th scope="col">delete</th>


    </tr>
  </thead>
  <tbody>
    <tr v-for="permission in role.permissions" :key="permission.id">
      <td>{{permission.name}}</td>

      <td>
      <span v-show="false">{{permission.can_read == 1?permission.can_read=true:permission.can_read=false }}</span>
        <b-form-checkbox v-model="permission.can_read"  >
                  can read
          </b-form-checkbox>

        </td>
      <td>
    <span v-show="false">    {{permission.can_create == 1?permission.can_create=true:permission.can_create=false }}</span>
         <b-form-checkbox v-model="permission.can_create"  >
                  can create
          </b-form-checkbox>
        </td>
      <td>
        <span v-show="false">   {{permission.can_update == 1?permission.can_update=true:permission.can_update=false }}</span>
         <b-form-checkbox v-model="permission.can_update"  >
                  can update
          </b-form-checkbox>
        </td>
      <td>
        <span v-show="false">     {{permission.can_delete == 1?permission.can_delete=true :permission.can_delete=false }}</span>

         <b-form-checkbox v-model="permission.can_delete"  >
                  can delete
          </b-form-checkbox>
        </td>
    </tr>

  </tbody>
</table>

                            </b-row>
                            <!-- Action Buttons -->
                            <b-button
                                variant="primary"
                                class="mb-1 mb-sm-0 mr-0 mr-sm-1"
                                type="submit"
                                @click.prevent="validationForm"
                            >
                                Save Changes
                            </b-button>
                            <b-button
                                variant="outline-secondary"
                                type="reset"
                            >
                                Reset
                            </b-button>
                        </b-form>
                    </validation-observer>

                </div>
            </b-tab>


        </b-tabs>
    </b-card>

</template>

<script>
import {
    BCard,
    BCardText,
    BAlert,
    BRow, BCol, BLink, BTab, BTabs,
    BButton,
    BMedia,
    BAvatar,
    BFormGroup,
    BFormInput,
    BFormFile,
    BForm,
    BTable,
    BCardHeader,
    BCardTitle,
    BFormCheckbox,
    BInputGroupPrepend,
} from 'bootstrap-vue'
import {ValidationProvider, ValidationObserver} from 'vee-validate'
import {
    required,

} from '@validations'
import EditInformation from './edit-user/EditInformation.vue'
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ErrorMsg from '@/components/Aleart/Error'
import SuccessMsg from '@/components/Aleart/Success'


export default {
    components: {
        BCard,
        BCardText,
        BAlert,
        BRow,
        BCol,
        BLink,
        BTab,
        BTabs,
        EditInformation,
        BButton,
        BMedia,
        BAvatar,
        BFormGroup,
        BFormInput,
        BFormFile,
        BForm,
        BTable,
        BCardHeader,
        BCardTitle,
        BFormCheckbox,
        BInputGroupPrepend,

        ValidationProvider,
        ValidationObserver,


        ErrorMsg,
        SuccessMsg,

    },
    setup() {

        return {}
    },
    data() {
        return {
            required,
            roleName:"",
            role:{},
            msg: '',
            iserror: false,
            issuccess: false,
        }
    },
    mounted() {

    },

    created() {

        this.getPermissions();
    },
    methods: {
        validationForm() {
      this.$refs.simpleRules.validate().then(success => {
        if (success) {
          // eslint-disable-next-line
           const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"content-type" : "multipart/form-data","Authorization" : `Bearer ` + token}
          }
          let formdata= new FormData();

          formdata.append('name',this.role.name)
          formdata.append('role_id',this.$route.params.id)


        formdata.append("permissions", JSON.stringify(this.role.permissions));
          axios.post('/api/web/update-role',formdata,config)
            .then(res =>{
          this.$router.replace('/roles/list').then(()=>{
            this.$toast({
            component: ToastificationContent,
             timeout: 5000,
            props: {
              title: 'add Role sucessfuly',
              icon: 'EditIcon',
              variant: 'success',
            },
          })})
          this.msg = 'Add Role successufly';
          this.iserror=false;
          this.issuccess=true;

            }).catch(error=>{
                if(error.response.status == 422){
                    this.msg = error.response.data.message;
                    this.iserror=true;
                   this.issuccess=false;
                }

        });

        }
      })
    },
        // getCity() {
        //      const   userToken =localStorage.getItem('userToken');
        //     let token=JSON.parse(userToken);

        //   let config ={
        //     headers :{"Authorization" : `Bearer ` + token}
        //   }
        //     axios.get('/api/web/cities/' + this.$route.params.id,config)
        //         .then(res => {
        //             this.nameCity = res.data.content.city
        //         })
        //         .catch(err => {
        //             console.log(err)
        //         })
        //     return
        // },
        getPermissions(){
       const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"Authorization" : `Bearer ` + token}
          }
      axios.get('/api/web/get-permissions/' + this.$route.params.id,config)
      .then(res =>{
        this.role = res.data ;
        console.log("hello permission",this.role);
      })
      .then(err => console.log(err))
    }
    }
}
</script>

<style lang="scss" scoped>
.per-page-selector {
    width: 90px;
}

.Status-filter-select {
    width: 190px;

    ::v-deep .vs__selected-options {
        flex-wrap: nowrap;
    }

    ::v-deep .vs__selected {
        width: 100px;
    }
}
</style>
<style>
.costoum-image {
    padding-left: 24px;
    padding-bottom: 10px;
    padding-top: 38px;
    height: 75px;
}
</style>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
