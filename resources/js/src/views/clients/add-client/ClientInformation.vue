<template>
    <div>

        <!-- Media -->
        <b-media class="mb-0">
            <template #aside>

            </template>
        </b-media>
<!--        <error-msg v-show="iserror"  :msg="msg" ></error-msg>-->
<!--        <success-msg v-show="issuccess" :msg="msg"></success-msg>-->
        <validation-observer ref="simpleRules">
            <!-- User Info: Input Fields -->
            <b-form>
                <b-row>

                    <!-- Field: First Name -->
                    <b-col
                        cols="6"
                    >
                        <b-form-group
                            label="First Name"
                            label-for="First_name"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="First Name"
                                rules="required"
                            >
                                <b-form-input
                                    :state="errors.length > 0 ? false:null"
                                    v-model="FirstName"
                                    placeholder=""
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!-- Field: Last Name -->
                    <b-col
                        cols="6"
                    >
                        <b-form-group
                            label="Last Name"
                            label-for="Last_name"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Last Name"
                                rules="required"
                            >
                                <b-form-input
                                    :state="errors.length > 0 ? false:null"
                                    v-model="LastName"
                                    placeholder=""
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>
                    <!-- email -->
                    <b-col cols="12">
                        <validation-provider
                            #default="{ errors }"
                            name="Email"
                            rules=""
                        >
                            <!-- Field: Email -->

                            <b-form-group label="Email" label-for="email">
                                <validation-provider
                                    #default="{ errors }"
                                    name="email"
                                    rules="required|email"
                                >
                                    <b-form-input
                                        id="email"
                                        type="email"
                                        :state="errors.length > 0 ? false : null"
                                        v-model="mail"
                                        placeholder="Email"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>


                        </validation-provider>
                    </b-col>
                    <!-- details  -->
                    <!-- Phone Number -->
                    <b-col cols="6">
                        <label>Mobile</label>
                        <br>
                        <VuePhoneNumberInput
                            default-country-code="AE"
                            @update="onUpdate"
                            v-model="phoneNumber"
                        />
                    </b-col>

                    <!-- Field: Password -->
                    <b-col
                        cols="6"
                    >
                        <b-form-group
                            label="Password"
                            label-for="Password"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Password"
                                rules="required"
                            >
                                <b-form-input
                                    :state="errors.length > 0 ? false:null"
                                    v-model="password"
                                    placeholder="password"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- Field: address -->
                    <b-col
                        cols="6"
                    >
                        <b-form-group
                            label="Address"
                            label-for="Address"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Address"
                                rules="required"
                            >
                                <b-form-input
                                    :state="errors.length > 0 ? false:null"
                                    v-model="address"
                                    placeholder="address"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>


                    <!-- image -->

                    <b-col cols="12"  class="p-1">
                        <SingleImageVue :ImageFile.sync="image"></SingleImageVue>
                    </b-col>
                </b-row>

                <!-- Action Buttons -->
                <b-button
                    variant="primary"
                    class=""
                    type="submit"
                    @click.prevent="validationForm"
                >
                    Save
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
</template>

<script>
import {
    BButton,
    BMedia,
    BAvatar,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormFile,
    BForm,
    BTable,
    BCard,
    BCardHeader,
    BCardTitle,
    BFormTextarea,
    BFormCheckbox,
    BInputGroupPrepend,
} from 'bootstrap-vue'

import axios from 'axios'
import { useInputImageRenderer } from '@core/comp-functions/forms/form-utils'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, alphaNum, email } from "@validations";

import VuePhoneNumberInput from "vue-phone-number-input";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
// import SingleImageVue from 'src/components/SingleImag/SingleImage.vue'
import SingleImageVue from "@/components/SingleImag/SingleImage.vue";

import vSelect from "vue-select";

export default {

    components: {
        BButton,
        BMedia,
        BAvatar,
        BRow,
        BCol,
        BFormGroup,
        BFormInput,
        BFormFile,
        BForm,
        BTable,
        BCard,
        BCardHeader,
        BCardTitle,
        BFormCheckbox,
        BInputGroupPrepend,vSelect,
        BFormTextarea,

        ValidationProvider,
        ValidationObserver,
        SingleImageVue,
        // ErrorMsg,
        // SuccessMsg,
        VuePhoneNumberInput,

    },
    setup(){

    }
    ,
    data() {
        return {
            // codeSimple,
            required,
            email,
            FirstName : '',
            LastName : '',
            password:'',
            image:null,
            mail:"",
            address:"",
            results: {},
            msg:'',
            iserror:false,
            issuccess:false,
            phoneNumber:'',
        }
    },
    mounted(){

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

                    formdata.append('first_name',this.FirstName)
                    formdata.append('last_name',this.LastName)
                    formdata.append('address',this.address)
                    formdata.append('email',this.mail)
                    formdata.append('password',this.password)

                    formdata.append('status',1)

                    if(this.image)
                        formdata.append("image", this.image);

                    axios.post('/api/admin/users',formdata,config)
                        .then(res =>{
                            this.$router.replace("/clientsList").then(() => {
                                this.$toast({
                                    component: ToastificationContent,
                                    timeout: 5000,
                                    props: {
                                        title: 'add Client successfuly',
                                        icon: 'EditIcon',
                                        variant: 'success',
                                    },
                                })})
                            this.msg = 'Add Client Successufly';
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
        onUpdate(payload) {
            this.results = payload;
        },
    },

}
</script>

<style lang="scss" scoped>
.per-page-selector {
}

.Status-filter-select {

    ::v-deep .vs__selected-options {
        flex-wrap: nowrap;
    }

    ::v-deep .vs__selected {
        width: 100px;
    }
}
</style>
<style>
.costoum-image{
    padding-left:24px ;
    padding-bottom: 10px;
    padding-top:38px;
    height: 75px;
}
</style>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
