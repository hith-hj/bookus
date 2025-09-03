<template>
    <b-card>
        <b-row>
            <!-- User Info: Left col -->
            <b-col
                cols="21"
                xl="6"
                class="d-flex justify-content-between flex-column"
            >
                <!-- User Avatar & Action Buttons -->
                <div class="d-flex justify-content-start">
                    <img v-if="userData.user.profile"
                         :src="userData.user.profile.profile_image"
                         width="140px"
                         alt="image"
                    />
                    <img v-else :src="require(`@/assets/images/avatars/notfound.png`)" width="140px" alt="image" />

                    <div class="d-flex flex-column ml-1">
                        <div class="mb-1">
                            <h4 class="mb-0">
                                {{ userData.user.first_name }}
                            </h4>
                            <span class="card-text">{{ userData.user.email }}</span>
                        </div>
                        <div class="d-flex flex-wrap"></div>
                    </div>
                </div>

                <!-- User Stats -->
                <div class="d-flex align-items-center mt-2">
                    <div class="d-flex align-items-center" v-if="userData.user.opl_id">
                        <b-avatar variant="light-success" rounded>
                            <feather-icon icon="TrendingUpIcon" size="18" />
                        </b-avatar>
                        <div class="ml-1">
                            <h5 class="mb-0">
                                {{ userData.user.opl_id }}
                            </h5>
                            <small>opl id</small>
                        </div>
                    </div>
                </div>
            </b-col>

            <!-- Right Col: Table -->
            <b-col cols="12" xl="6">
                <table class="mt-2 mt-xl-0 w-100">
                    <tr>
                        <th class="pb-50">
                            <feather-icon icon="UserIcon" class="mr-75" />
                            <span class="font-weight-bold">Username</span>
                        </th>
                        <td class="pb-50">
                            {{ userData.user.first_name }}
                        </td>
                    </tr>
                    <tr>
                        <th class="pb-50">
                            <feather-icon icon="CheckIcon" class="mr-75" />
                            <span class="font-weight-bold">Status</span>
                        </th>
                        <td class="pb-50 text-capitalize">
                            {{ userData.user.active == 1 ? "active" : "not active" }}
                        </td>
                    </tr>
                    <tr>
                        <th class="pb-50">
                            <feather-icon icon="StarIcon" class="mr-75" />
                            <span class="font-weight-bold">Role</span>
                        </th>
                        <td class="pb-50 text-capitalize">
                            {{ userData.user.Role ?userData.user.Role:"" }}
                        </td>
                    </tr>
                    <tr>
                        <th class="pb-50">
                            <feather-icon icon="FlagIcon" class="mr-75" />
                            <span class="font-weight-bold">address</span>
                        </th>
                        <td class="pb-50" v-if="userData.user.profile">
                            {{ userData.user.profile.addres ? userData.user.profile.addres :"" }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <feather-icon icon="PhoneIcon" class="mr-75" />
                            <span class="font-weight-bold">phone number</span>
                        </th>
                        <td v-if="userData.user.profile" >
                            {{ userData.user.profile.phone_number ?userData.user.profile.phone_number :"" }}
                        </td>
                    </tr>
                </table>
            </b-col>
        </b-row>
    </b-card>
</template>

<script>
import { BCard, BButton, BAvatar, BRow, BCol } from "bootstrap-vue";
import { avatarText } from "@core/utils/filter";
import useUsersList from "../users-list/useUsersList";

export default {
    components: {
        BCard,
        BButton,
        BRow,
        BCol,
        BAvatar,
    },
    props: {
        userData: {
            type: Object,
            required: true,
        },
    },
    setup() {
        const { resolveUserRoleVariant } = useUsersList();
        return {
            avatarText,
            resolveUserRoleVariant,
        };
    },
};
</script>

<style>
</style>
