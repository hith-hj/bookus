<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Admin</span>

                <Admin-Information></Admin-Information>

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import AdminInformation from './add-admin/AdminInformation.vue'
import axios from 'axios'

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
        // Local Components
        AdminInformation,

    },
    data(){
        return{
        }
    },
    created(){
    },
    methods:{

    }
}
</script>

<style>

</style>
