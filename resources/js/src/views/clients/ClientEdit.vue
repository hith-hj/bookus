<template>
    <b-card>
        <b-tabs v-if="true" pills>
                    <span class="d-none d-sm-inline">Edit Clients</span>
                <div>
                       <!-- Media -->
                    <b-media class="mb-2">
                        <template #aside> </template>
                    </b-media>
                    <error-msg v-show="iserror" :msg="msg"></error-msg>
                    <success-msg v-show="issuccess" :msg="msg"></success-msg>

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
                                                v-model="nameUser.first_name"
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
                                                v-model="nameUser.last_name"
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
                                        name="Products"
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
                                                    v-model="nameUser.email"
                                                    placeholder="Email"
                                                />
                                                <small class="text-danger">{{ errors[0] }}</small>
                                            </validation-provider>
                                        </b-form-group>


                                    </validation-provider>
                                </b-col>
                                <!-- phone-->
                                <!-- Phone Number -->
                                <b-col cols="6">
                                    <label>Mobile</label>
                                    <br>
                                    <VuePhoneNumberInput
                                        default-country-code="AE"
                                        @update="onUpdate"
                                        v-model="nameUser.phone_number"
                                    />
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
                                                v-model="nameUser.address"
                                                placeholder="address"
                                            />
                                            <small class="text-danger">{{ errors[0] }}</small>
                                        </validation-provider>
                                    </b-form-group>
                                </b-col>

                                <!-- mmm -->
                                <b-col cols="12" md="4" lg="4">
                                    <SingleImageVue :ImageFile.sync="image"></SingleImageVue>
                                </b-col>
                            </b-row>
                            <br />
                            <!-- Action Buttons -->
                            <b-button
                                variant="primary"
                                class="mb-1 mb-sm-0 mr-0 mr-sm-1"
                                type="submit"
                                @click.prevent="edituser"
                            >
                                Save changes
                            </b-button>
                            <b-button variant="outline-secondary" type="reset">
                                Reset
                            </b-button>
                        </b-form>
                    </validation-observer>
                </div>
<!--            </b-tab>-->
        </b-tabs>
    </b-card>
</template>

<script>
import {
    BCard,
    BCardText,
    BAlert,
    BRow,
    BCol,
    BLink,
    BTab,
    BTabs,
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
    BFormTextarea,
    BFormCheckbox,
    BInputGroupPrepend,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required } from "@validations";

import VuePhoneNumberInput from "vue-phone-number-input";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import axios from "axios";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
import ErrorMsg from "@/components/Aleart/Error";
import SuccessMsg from "@/components/Aleart/Success";
import SingleImageVue from "@/components/SingleImag/SingleImage.vue";
import vSelect from "vue-select";

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
        VuePhoneNumberInput,
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
        BFormTextarea,

        ValidationProvider,
        ValidationObserver,
        SingleImageVue,vSelect,

        ErrorMsg,
        SuccessMsg,
    },
    setup() {
        return {

        };
    },
    data() {
        return {
            required,
            nameUser: {},


            image:null,
            result:{},
            msg: "",
            iserror: false,
            issuccess: false,
            phoneNumber:""
        };
    },
    mounted() {
        this.getUser();
    },
    watch:{

    }
   ,
    methods: {
        validationForm() {
            this.$refs.simpleRules.validate().then((success) => {
                if (success) {

                    const userToken = localStorage.getItem("userToken");
                    let token = JSON.parse(userToken);

                    let config = {
                        headers: {Authorization: `Bearer ` + token ,  "content-type": "multipart/form-data"},
                    };
                    let url="api/admin/users/";
                    let formdata = new FormData();
                    formdata.append("first_name", this.nameUser.first_name);
                    formdata.append("last_name", this.nameUser.last_name);
                    formdata.append("email", this.nameUser.email);
                    formdata.append("address", this.nameUser.address);
                    // formdata.append('id', this.$route.params.id);
                    if (typeof this.image != 'string') {
                        formdata.append("image", this.image);
                    }
                    // axios.post(url+ this.$route.params.id,formdata,config)
                    axios.post(url,this.nameUser,config)
                        .then(res =>{
                            this.$router.replace("/adminsList").then(() => {
                                this.$toast({
                                    component: ToastificationContent,
                                    timeout: 5000,
                                    props: {
                                        title: 'add Admin successfuly',
                                        icon: 'EditIcon',
                                        variant: 'success',
                                    },
                                })})
                            this.msg = 'Add client Successufly';
                            this.iserror=false;
                            this.issuccess=true;

                        }).catch(error=>{
                        if(error.response.status == 422){
                            this.msg = error.response.data.message;
                            this.iserror=true;
                            this.issuccess=false;
                        }

                    });

                }});



        },
        edituser(){
            this.$refs.simpleRules.validate().then((success) => {
                if (success) {

                    const userToken = localStorage.getItem("userToken");
                    let token = JSON.parse(userToken);

                    let config = {
                        headers: {Authorization: `Bearer ` + token ,  "content-type": "multipart/form-data"},
                    };
                    let url="api/admin/edit-admin";
                    let formdata = new FormData();
                    formdata.append("first_name",this.nameUser.first_name);
                    formdata.append("last_name",this.nameUser.last_name);
                    formdata.append("address",this.nameUser.address);
                    formdata.append("email",this.nameUser.email);
                    if (typeof this.image != 'string') {
                         formdata.append("image", this.image);
                    }

                    if(this.results != undefined)
                        formdata.append('phone_number', this.results.formattedNumber);
                    formdata.append("_method", "put");

                    axios.post
('/api/admin/users/'+ this.$route.params.id,formdata,config)
                        .then(res =>{
                            this.$router.replace("/clientsList").then(() => {
                                this.$toast({
                                    component: ToastificationContent,
                                    timeout: 5000,
                                    props: {
                                        title: 'add client successfuly',
                                        icon: 'EditIcon',
                                        variant: 'success',
                                    },
                                })})
                            this.msg = 'Add client Successufly';
                            this.iserror=false;
                            this.issuccess=true;

                        }).catch(error=>{
                        if(error.response.status == 422){
                            this.msg = error.response.data.message;
                            this.iserror=true;
                            this.issuccess=false;
                        }

                    });
                }})



        },

        getUser() {
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: { Authorization: `Bearer ` + token },
            };
            axios
                .get("/api/admin/users/" + this.$route.params.id, config)
                .then((res) => {
                    this.nameUser = res.data.content.user;
                    this.image = "storage/"+this.nameUser.user;
                    console.log(res.data)
                })
                .catch((err) => {
                    console.log(err);
                });
            return;
        },
        onUpdate (payload) {
            this.results = payload
        },
    },
};
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
@import "~@resources/scss/vue/libs/vue-select.scss";
</style>
