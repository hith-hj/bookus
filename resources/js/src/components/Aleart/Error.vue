<template>
    <b-card class="mt-0 mb-0">
        <div>

            <!-- danger -->
            <b-alert
                variant="danger"
                show
            >
                <h4 class="alert-heading">
                    Danger
                </h4>
                <div class="alert-body">
                    <span>{{this.msg}}</span>
                </div>
            </b-alert>
        </div>
    </b-card>
</template>

<script>
import BCardCode from '@core/components/b-card-code'
import { BAlert, BCardText ,BCard} from 'bootstrap-vue'
export default {
    components: {
        BCard,
        BAlert,
        BCardText,
    },
    data() {
        return {

        }
    },
    props:{
        msg:String,
        isError:Boolean
    }
}
</script>

<style>

</style>
