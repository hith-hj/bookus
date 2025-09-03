<template>
  <!-- Need to add height inherit because Vue 2 don't support multiple root ele -->
 <div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Special title treatment</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
</div>
</template>

<script>
import store from '@/store'
import {
  ref, onUnmounted, computed, watch,
  // ref, watch, computed, onUnmounted,
} from '@vue/composition-api'
import {
  BFormInput, BInputGroup, BInputGroupPrepend, BDropdown, BDropdownItem,
  BFormCheckbox, BMedia, BMediaAside, BMediaBody, BAvatar,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { filterTags, formatDateToMonthShort } from '@core/utils/filter'
import { useRouter } from '@core/utils/utils'
import { useResponsiveAppLeftSidebarVisibility } from '@core/comp-functions/ui/app'
import EmailLeftSidebar from './EmailLeftSidebar.vue'
import EmailView from './EmailView.vue'
import emailStoreModule from './emailStoreModule'
import useEmail from './useEmail'
import EmailCompose from './EmailCompose.vue'

export default {
  components: {
    BFormInput,
    BInputGroup,
    BInputGroupPrepend,
    BDropdown,
    BDropdownItem,
    BFormCheckbox,
    BMedia,
    BMediaAside,
    BMediaBody,
    BAvatar,

    // 3rd Party
    VuePerfectScrollbar,

    // App SFC
    EmailLeftSidebar,
    EmailView,
    EmailCompose,
  },
  setup() {
    const EMAIL_APP_STORE_MODULE_NAME = 'app-email'

    // Register module
    if (!store.hasModule(EMAIL_APP_STORE_MODULE_NAME)) store.registerModule(EMAIL_APP_STORE_MODULE_NAME, emailStoreModule)

    // UnRegister on leave
    onUnmounted(() => {
      if (store.hasModule(EMAIL_APP_STORE_MODULE_NAME)) store.unregisterModule(EMAIL_APP_STORE_MODULE_NAME)
    })

    const { route, router } = useRouter()
    const { resolveLabelColor } = useEmail()

    // Route Params
    const routeParams = computed(() => route.value.params)
    watch(routeParams, () => {
      // eslint-disable-next-line no-use-before-define
      fetchEmails()
    })

    // Emails & EmailsMeta
    const emails = ref([])
    const emailsMeta = ref({})

    const perfectScrollbarSettings = {
      maxScrollbarLength: 150,
    }

    // Search Query
    const routeQuery = computed(() => route.value.query.q)
    const searchQuery = ref(routeQuery.value)
    watch(routeQuery, val => {
      searchQuery.value = val
    })
    // eslint-disable-next-line no-use-before-define
    watch(searchQuery, () => fetchEmails())
    const updateRouteQuery = val => {
      const currentRouteQuery = JSON.parse(JSON.stringify(route.value.query))

      if (val) currentRouteQuery.q = val
      else delete currentRouteQuery.q

      router.replace({ name: route.name, query: currentRouteQuery })
    }

    const fetchEmails = () => {
      store.dispatch('app-email/fetchEmails', {
        q: searchQuery.value,
        folder: router.currentRoute.params.folder || 'inbox',
        label: router.currentRoute.params.label,
      })
        .then(response => {
          emails.value = response.data.emails
          emailsMeta.value = response.data.emailsMeta
        })
    }

    fetchEmails()

    // ------------------------------------------------
    // Mail Selection
    // ------------------------------------------------
    const selectedEmails = ref([])
    const toggleSelectedMail = mailId => {
      const mailIndex = selectedEmails.value.indexOf(mailId)

      if (mailIndex === -1) selectedEmails.value.push(mailId)
      else selectedEmails.value.splice(mailIndex, 1)
    }
    const selectAllEmailCheckbox = computed(() => emails.value.length && (emails.value.length === selectedEmails.value.length))
    const isSelectAllEmailCheckboxIndeterminate = computed(() => Boolean(selectedEmails.value.length) && emails.value.length !== selectedEmails.value.length)
    const selectAllCheckboxUpdate = val => {
      selectedEmails.value = val ? emails.value.map(mail => mail.id) : []
    }
    // ? Watcher to reset selectedEmails is somewhere below due to watch dependecy fullfilment

    // ------------------------------------------------
    // Mail Actions
    // ------------------------------------------------
    const toggleStarred = email => {
      store.dispatch('app-email/updateEmail', {
        emailIds: [email.id],
        dataToUpdate: { isStarred: !email.isStarred },
      }).then(() => {
        // eslint-disable-next-line no-param-reassign
        email.isStarred = !email.isStarred
      })
    }

    const moveSelectedEmailsToFolder = folder => {
      store.dispatch('app-email/updateEmail', {
        emailIds: selectedEmails.value,
        dataToUpdate: { folder },
      })
        .then(() => { fetchEmails() })
        .finally(() => { selectedEmails.value = [] })
    }

    const updateSelectedEmailsLabel = label => {
      store.dispatch('app-email/updateEmailLabels', {
        emailIds: selectedEmails.value,
        label,
      })
        .then(() => { fetchEmails() })
        .finally(() => { selectedEmails.value = [] })
    }

    const markSelectedEmailsAsUnread = () => {
      store.dispatch('app-email/updateEmail', {
        emailIds: selectedEmails.value,
        dataToUpdate: { isRead: false },
      })
        .then(() => { fetchEmails() })
        .finally(() => { selectedEmails.value = [] })
    }

    // ------------------------------------------------
    // Email Details
    // ------------------------------------------------
    const showEmailDetails = ref(false)
    const emailViewData = ref({})
    const opendedEmailMeta = computed(() => {
      const openedEmailIndex = emails.value.findIndex(e => e.id === emailViewData.value.id)
      return {
        hasNextEmail: Boolean(emails.value[openedEmailIndex + 1]),
        hasPreviousEmail: Boolean(emails.value[openedEmailIndex - 1]),
      }
    })
    const updateEmailViewData = email => {
      // Mark email is read
      store.dispatch('app-email/updateEmail', {
        emailIds: [email.id],
        dataToUpdate: { isRead: true },
      })
        .then(() => {
          // If opened email is unread then decrease badge count for email meta based on email folder
          if (!email.isRead && (email.folder === 'inbox' || email.folder === 'spam')) {
            emailsMeta.value[email.folder] -= 1
          }

          // eslint-disable-next-line no-param-reassign
          email.isRead = true
        })
        .finally(() => {
          emailViewData.value = email
          showEmailDetails.value = true
        })
    }
    const moveOpenEmailToFolder = folder => {
      selectedEmails.value = [emailViewData.value.id]
      moveSelectedEmailsToFolder(folder)
      selectedEmails.value = []
      showEmailDetails.value = false
    }
    const updateOpenEmailLabel = label => {
      selectedEmails.value = [emailViewData.value.id]
      updateSelectedEmailsLabel(label)

      // Update label in opened email
      const labelIndex = emailViewData.value.labels.indexOf(label)
      if (labelIndex === -1) emailViewData.value.labels.push(label)
      else emailViewData.value.labels.splice(labelIndex, 1)

      selectedEmails.value = []
    }

    const markOpenEmailAsUnread = () => {
      selectedEmails.value = [emailViewData.value.id]
      markSelectedEmailsAsUnread()

      selectedEmails.value = []
      showEmailDetails.value = false
    }

    const changeOpenedEmail = dir => {
      const openedEmailIndex = emails.value.findIndex(e => e.id === emailViewData.value.id)
      const newEmailIndex = dir === 'previous' ? openedEmailIndex - 1 : openedEmailIndex + 1
      emailViewData.value = emails.value[newEmailIndex]
    }

    // * If someone clicks on filter while viewing detail => Close the email detail view
    watch(routeParams, () => {
      showEmailDetails.value = false
    })

    // * Watcher to reset selectedEmails
    // ? You can also use showEmailDetails (instead of `emailViewData`) but it will trigger execution twice in this case
    // eslint-disable-next-line no-use-before-define
    watch([emailViewData, routeParams], () => {
      selectedEmails.value = []
    })

    // Compose
    const shallShowEmailComposeModal = ref(false)

    // Left Sidebar Responsiveness
    const { mqShallShowLeftSidebar } = useResponsiveAppLeftSidebarVisibility()

    return {
      // UI
      perfectScrollbarSettings,

      // Emails & EmailsMeta
      emails,
      emailsMeta,

      // Mail Selection
      selectAllEmailCheckbox,
      isSelectAllEmailCheckboxIndeterminate,
      selectedEmails,
      toggleSelectedMail,
      selectAllCheckboxUpdate,

      // Mail Actions
      toggleStarred,
      moveSelectedEmailsToFolder,
      updateSelectedEmailsLabel,
      markSelectedEmailsAsUnread,

      // Email Details
      showEmailDetails,
      emailViewData,
      opendedEmailMeta,
      updateEmailViewData,
      moveOpenEmailToFolder,
      updateOpenEmailLabel,
      markOpenEmailAsUnread,
      changeOpenedEmail,

      // Search Query
      searchQuery,
      updateRouteQuery,

      // UI Filters
      filterTags,
      formatDateToMonthShort,

      // useEmail
      resolveLabelColor,

      // Compose
      shallShowEmailComposeModal,

      // Left Sidebar Responsiveness
      mqShallShowLeftSidebar,
    }
  },
}
</script>

<style lang="scss" scoped>

</style>

<style lang="scss">
@import "~@resources/scss/base/pages/app-email.scss";
</style>
