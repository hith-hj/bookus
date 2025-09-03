<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Member</span>

                <member-Information></member-Information>

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import MemberInformation from './add-member/MemberInformation.vue'
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
        MemberInformation,

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
