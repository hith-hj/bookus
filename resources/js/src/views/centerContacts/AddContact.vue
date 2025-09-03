<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Contact</span>

                <contact-Information></contact-Information>

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import ContactInformation from './add-contact/ContactInformation.vue'
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
        ContactInformation,

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
