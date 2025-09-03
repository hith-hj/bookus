<template>
    <b-sidebar
        id="add-new-wholesaler-sidebar"
        :visible="isAddNewWholesalerSidebarActive"
        bg-variant="white"
        sidebar-class="sidebar-lg"
        shadow
        backdrop
        no-header
        right
        @hidden="resetForm"
        @change="(val) => $emit('update:is-add-new-wholesaler-sidebar-active', val)"
    >
        <template #default="{ hide }">
            <!-- Header -->
            <div
                class="
          d-flex
          justify-content-between
          align-items-center
          content-sidebar-header
          px-2
          py-1
        "
            >
                <h5 class="mb-0">Add New Admin</h5>

                <feather-icon
                    class="ml-1 cursor-pointer"
                    icon="XIcon"
                    size="16"
                    @click="hide"
                />
            </div>

            <!-- BODY -->
            <validation-observer #default="{ handleSubmit }" ref="refFormObserver">
<!--                <error-msg v-show="iserror" :msg="msg"></error-msg>-->
<!--                <success-msg v-show="issuccess" :msg="msg"></success-msg>-->

                <!-- Form -->
                <b-form
                    class="p-2"
                    @submit.prevent="handleSubmit(onSubmit)"
                    @reset.prevent="resetForm"
                >
                    <!-- First Name -->
                    <validation-provider
                        #default="validationContext"
                        name="First Name"
                        rules="required"
                    >
                        <b-form-group label="First Name" label-for="first-name">
                            <b-form-input
                                id="first-name"
                                class="text-capitalize"
                                v-model="firstName"
                                autofocus
                                :state="getValidationState(validationContext)"
                                trim
                                placeholder=""
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                    <!-- Last Name -->
                    <validation-provider
                        #default="validationContext"
                        name="Last Name"
                        rules="required"
                    >
                        <b-form-group label="Last Name" label-for="last-name">
                            <b-form-input
                                id="first-name"
                                class="text-capitalize"
                                v-model="lastName"
                                autofocus
                                :state="getValidationState(validationContext)"
                                trim
                                placeholder=""
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>


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
                                v-model="email"
                                placeholder="Email"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>


                    <!-- Phone Number -->

                    <label>Mobile</label>
<!--                    <VuePhoneNumberInput-->
<!--                        default-country-code="LB"-->
<!--                        @update="onUpdate"-->
<!--                        v-model="phoneNumber"-->
<!--                    />-->
                    <!-- User statues -->
                    <validation-provider
                        #default="validationContext"
                        name="Wholesaler statues"
                        rules="required"
                    >
                        <b-form-group
                            class="mt-1"
                            label="Statues"
                            label-for="wholesaler_status"
                            :state="getValidationState(validationContext)"
                        >
                            <v-select

                                v-model="wholesalersStatues"
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="statusOptions"
                                :clearable="false"
                                input-id="pharmacy_statues"
                            />
                            <b-form-invalid-feedback
                                :state="getValidationState(validationContext)"
                            >
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>




                    <!-- password  -->
                    <!-- password -->
                    <b-form-group
                        label="New Password"
                        label-for="reset-password-new"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="Password"
                            vid="Password"
                            rules="required"
                        >
                            <b-input-group
                                class="input-group-merge"
                                :class="errors.length > 0 ? 'is-invalid':null"
                            >
                                <b-form-input
                                    id="reset-password-new"
                                    v-model="password"
                                    :type="password1FieldType"
                                    :state="errors.length > 0 ? false:null"
                                    class="form-control-merge"
                                    name="reset-password-new"
                                    placeholder="············"
                                />
                                <b-input-group-append is-text>
                                    <feather-icon
                                        class="cursor-pointer"
                                        :icon="password1ToggleIcon"
                                        @click="togglePassword1Visibility"
                                    />
                                </b-input-group-append>
                            </b-input-group>
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>

                    <!-- confirm password -->
                    <b-form-group
                        label-for="reset-password-confirm"
                        label="Confirm Password"
                    >
                        <validation-provider
                            #default="{ errors }"
                            name="Confirm Password"
                            rules="required|confirmed:Password"
                        >
                            <b-input-group
                                class="input-group-merge"
                                :class="errors.length > 0 ? 'is-invalid':null"
                            >
                                <b-form-input
                                    id="reset-password-confirm"
                                    v-model="cPassword"
                                    :type="password2FieldType"
                                    class="form-control-merge"
                                    :state="errors.length > 0 ? false:null"
                                    name="reset-password-confirm"
                                    placeholder="············"
                                />
                                <b-input-group-append is-text>
                                    <feather-icon
                                        class="cursor-pointer"
                                        :icon="password2ToggleIcon"
                                        @click="togglePassword2Visibility"
                                    />
                                </b-input-group-append>
                            </b-input-group>
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>

                    <!-- Payment Terms -->
                    <b-form-group label="Role Name" label-for="Role_Name">
                        <validation-provider
                            #default="{ errors }"
                            name="Role Name"
                            rules=""
                        >
                            <v-select
                                v-model="roleId"
                                label="name"
                                :options="roles"
                                :reduce="(name) => name.id"
                                class="w-100"
                            />
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>
                    </b-form-group>


                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="primary"
                            class="mr-2"
                            @click="createAdmin"
                        >
                            Add
                        </b-button>
                        <b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            type="button"
                            variant="outline-secondary"
                            @click="hide"
                        >
                            Cancel
                        </b-button>
                    </div>
                </b-form>
            </validation-observer>
        </template>
    </b-sidebar>
</template>

<script>
import axios from "axios";
import {
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BFormInvalidFeedback,
    BFormCheckbox, BCard,
    BCardTitle,
    BCardText,
    BInputGroup,
    BInputGroupAppend,
    BLink,
    BButton,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { ref } from "@vue/composition-api";
import { required, alphaNum, email } from "@validations";
import formValidation from "@core/comp-functions/forms/form-validation";
import Ripple from "vue-ripple-directive";
import vSelect from "vue-select";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
// import ErrorMsg from "@/components/Aleart/Error";
// import SuccessMsg from "@/components/Aleart/Success";
// import VuePhoneNumberInput from "vue-phone-number-input";
// import "vue-phone-number-input/dist/vue-phone-number-input.css";

export default {
    components: {
        BFormCheckbox,
        BSidebar,
        BForm,
        BFormGroup, BCard,
        BCardTitle,
        BCardText,
        BInputGroup,
        BInputGroupAppend,
        BLink,
        BFormInput,
        BFormInvalidFeedback,
        BButton,
        vSelect,
        // ErrorMsg,
        // SuccessMsg,
        // VuePhoneNumberInput,
        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    model: {
        prop: "isAddNewWholesalerSidebarActive",
        event: "update:is-add-new-wholesaler-sidebar-active",
    },
    props: {
        isAddNewWholesalerSidebarActive: {
            type: Boolean,
            required: true,
        },

    },
    data() {
        return {
            required,
            alphaNum,
            email,
            firstName: "",
            lastName:"",
            phoneNumber: "",
            landline: "",
            address: "",
            wholesalersStatues: "active",
            wholesalersType: "",
            receveReturn: false,
            receveOrder: false,
            mof_number: "",
            vat: "",
            type: "wholesaler", //seller ,pharmacy
            entity_type: "Sub-distributor", //distriputrt.....
            minOrder: "", //minmum order
            website: "",

            payment: "", //Payment Terms
            cachOnDelvery: true,

            disabled: false,
            results: {},
            roles:[],
            roleId:"",

            msg: "",
            // iserror: false,
            // issuccess: false,

            cPassword: '',
            password: '',
            // validation

            // Toggle Password
            password1FieldType: 'password',
            password2FieldType: 'password',

            //   countries,
        };
    },
    setup(props, { emit }) {
        const blankUserData = {
            fullName: "",
            username: "",
            email: "",
            role: null,
            currentPlan: null,
            company: "",
            country: "",
            contact: "",

        };
        const statusOptions = ["active", "inactive"];
        const userData = ref(JSON.parse(JSON.stringify(blankUserData)));
        const resetuserData = () => {
            userData.value = JSON.parse(JSON.stringify(blankUserData));
        };

        const onSubmit = () => {};

        const { refFormObserver, getValidationState, resetForm } =
            formValidation(resetuserData);

        return {
            userData,
            onSubmit,

            refFormObserver,
            getValidationState,
            resetForm,
            statusOptions,
        };
    },
    computed: {
        password1ToggleIcon() {
            return this.password1FieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        password2ToggleIcon() {
            return this.password2FieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
    },
    watch:{
        cachOnDelvery(query){
            if(query){
                this.payment=""
            }
        },
    },
    mounted() {
        this. getRoles();
    },
    methods: {
        togglePassword1Visibility() {
            this.password1FieldType = this.password1FieldType === 'password' ? 'text' : 'password'
        },
        togglePassword2Visibility() {
            this.password2FieldType = this.password2FieldType === 'password' ? 'text' : 'password'
        },
        createAdmin() {
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: {
                    "content-type": "multipart/form-data",
                    Authorization: `Bearer ` + token,
                },
            };
            let formdata = new FormData();
            formdata.append("first_name", this.firstName);
            formdata.append("last_name", this.lastName);
            formdata.append("email", this.email);

            formdata.append("phone_number", this.results.formattedNumber);

            formdata.append("entity_type", "admin");

            formdata.append("registered", 1);
            formdata.append("password", this.password);
            formdata.append("role_id", this.roleId);


            if (this.wholesalersStatues == "active") formdata.append("status", 1);
            else formdata.append("status", 0);

            axios
                .post("/api/web/add-admin", formdata, config)
                .then((res) => {
                    this.$toast({
                        component: ToastificationContent,
                        timeout: 5000,
                        props: {
                            title: "add admin sucessfuly",
                            icon: "EditIcon",
                            variant: "success",
                        },
                    });
                    // this.msg = "Add admin successufly";
                    // this.iserror = false;
                    // this.issuccess = true;
                    this.$emit("update:is-add-new-wholesaler-sidebar-active", false);
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        // this.msg = error.response.data.message;
                        // this.iserror = true;
                        // this.issuccess = false;
                        this.$toast({
                            component: ToastificationContent,
                            timeout: 5000,
                            props: {
                                title:  this.msg ,
                                icon: "EditIcon",
                                variant: "danger",
                            },
                        });
                    }
                    if (error.response.status == 400) {
                        // this.msg = error.response.data.message;
                        // this.iserror = true;
                        // this.issuccess = false;
                    }
                });
        },
        getRoles() {
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: { Authorization: `Bearer ` + token },
            };

            axios
                .get("/api/web/roles", config)
                .then((res) => {
                    this.roles = res.data.content.roles;

                })
                .then((err) => console.log(err));
        },
        onUpdate(payload) {
            this.results = payload;
        },
    },
};
</script>

<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
@import '~@resources/scss/vue/pages/page-auth.scss';
#add-new-wholesaler-sidebar {
    .vs__dropdown-menu {
        max-height: 200px !important;
    }
}
</style>
