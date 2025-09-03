<template>
    <b-card no-body>
        <b-card-header class="pb-50">
            <h5>
                Filters
            </h5>
        </b-card-header>
        <b-card-body>
            <b-row>

                <b-col
                    cols="12"
                    md="4"
                    class="mb-md-0 mb-2"
                >
                    <label>Status</label>
                    <v-select
                        v-model="statusVal"
                        :options="userStatus"
                        class="w-100"
                        @change="filterStatus"
                    />
                </b-col>
            </b-row>
        </b-card-body>
    </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardBody, BRow, BCol } from 'bootstrap-vue'
import vSelect from 'vue-select'

export default {
    components: {
        BRow,
        BCol,
        BCard,
        BCardHeader,
        BCardBody,
        vSelect,
    },
    props: {
        roleFilter: {
            type: [String, null],
            default: null,
        },

        statusFilter: {
            type: [String, null],
            default: null,
        },


    },
    data(){
        return{
            userStatus: ['active', 'inactive'],

            userRoles: ['admin', 'pharmacist','sales','Developer'],
            statusVal:'',
            roleVal:''
        }
    },
    watch:{
        statusVal(value){
            this.filterStatus();
        },
        roleVal(value){
            this.filterRole();

        }

    },
    methods:{
        filterStatus(){
            this.$emit('update:statusFilter', this.statusVal);
        },
        filterRole(){
            this.$emit('update:roleFilter', this.roleVal);
        }
    }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
