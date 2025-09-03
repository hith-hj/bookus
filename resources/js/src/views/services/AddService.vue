<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Service</span>

                <Service-Information></Service-Information>

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import ServiceInformation from './add-service/ServiceInformation.vue'
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
        ServiceInformation,

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
