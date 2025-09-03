export default [
  {
    path: '/dashboard/analytics',
    name: 'dashboard-analytics',
    component: () => import('@/views/dashboard/analytics/Analytics.vue'),
  },
  {
    path: '/dashboard/ecommerce',
    name: 'dashboard-ecommerce',
    component: () => import('@/views/dashboard/ecommerce/Ecommerce.vue'),
  },
  // Admin
  {
    path: '/adminsList',
    name: 'admin-list',
    component: () => import('@/views/admins/AdminList.vue'),
  },
  {
    path: '/admin-edit/:id',
    name: 'admin-edit',
    component: () => import('@/views/admins/AdminEdit.vue'),
  },
  // admin-edit
  {
    path: '/add-admin',
    name: 'add-admin',
    component: () => import('@/views/admins/AddAdmin.vue'),
  },

  // ROLE
  {
    path: '/roles',
    name: 'roles-list',
    component: () => import('@/views/roles/RolesList.vue'),
  },
  {
    path: '/add-role',
    name: 'add-role',
    component: () => import('@/views/roles/AddRole.vue'),
  },
  // Clients
  { // LIST
    path: '/clientsList',
    name: 'clients-list',
    component: () => import('@/views/clients/ClientList.vue'),
  },
  // admin-edit
  {
    path: '/add-client',
    name: 'add-client',
    component: () => import('@/views/clients/AddClient.vue'),
  },
  { // client-edit
    path: '/client-edit/:id',
    name: 'client-edit',
    component: () => import('@/views/clients/ClientEdit.vue'),
  },
  // CENTERS
  { // LIST
    path: '/centersList',
    name: 'centers-list',
    component: () => import('@/views/centers/CentersList.vue'),
  },
  { // ADD CENTER
    path: '/add-center',
    name: 'add-center',
    component: () => import('@/views/centers/AddCenter.vue'),
  }, { // center-edit
    path: '/center-edit/:id',
    name: 'center-edit',
    component: () => import('@/views/centers/editCenter.vue'),
  },
  { // add-appointment
    path: '/add-appointment/:id',
    name: 'add-appointment',
    component: () => import('@/views/centers/addAppointment.vue'),
  },
  // services
  { // LIST
    path: '/servicesList',
    name: 'services-list',
    component: () => import('@/views/services/ServicesList.vue'),
  },
  { // ADD service
    path: '/add-service',
    name: 'add-service',
    component: () => import('@/views/services/AddService.vue'),
  },
  { // service-edit
    path: '/service-edit/:id',
    name: 'service-edit',
    component: () => import('@/views/services/editService.vue'),
  },

  // categories
  { // LIST
    path: '/categoriesList',
    name: 'categories-list',
    component: () => import('@/views/categories/CategoriesList.vue'),
  },
  { // ADD category
    path: '/add-category',
    name: 'add-category',
    component: () => import('@/views/categories/AddCategory.vue'),
  }, { // category-edit
    path: '/category1-edit/:id',
    name: 'category1-edit',
    component: () => import('@/views/categories/editCategory.vue'),
  },
  // appointment
  { // LIST
    path: '/appointmentsList',
    name: 'appointments-list',
    component: () => import('@/views/appointment/appointmentsList.vue'),
  },
  // Calender
  { // LIST
    path: '/calendar',
    name: 'calendar',
    component: () => import('@/views/calendar/Calendar.vue'),
  },
  // CATELOG .....

  { // LIST
    path: '/catalogueList',
    name: 'catalogue-list',
    component: () => import('@/views/catalogue/CentersList.vue'),
  },
  { // ADD category
    path: '/add-cat-center',
    name: 'add-cat-center',
    component: () => import('@/views/catalogue/AddCategory.vue'),
  },
  { // ADD service
    path: '/add-serv-center',
    name: 'add-serv-center',
    component: () => import('@/views/catalogue/AddService.vue'),
  },
  { // category-edit
    path: '/edit-center-cat/:id',
    name: 'dit-center-cat',
    component: () => import('@/views/catalogue/editCategory.vue'),
  },

  // Team
  { // LIST
    path: '/teamList',
    name: 'team-list',
    component: () => import('@/views/team/TeamList.vue'),
  },
  { // ADD member team
    path: '/add-member',
    name: 'add-member',
    component: () => import('@/views/team/AddMember.vue'),
  },
  { // ADD permission team
    path: '/team-permission',
    name: 'team-permission',
    component: () => import('@/views/team/TeamPermission.vue'),
  },
  { // category-edit
    path: '/team-edit/:id',
    name: 'team-edit',
    component: () => import('@/views/team/editCategory.vue'),
  },
  // Contact
  { // LIST
    path: '/contactList',
    name: 'contact-list',
    component: () => import('@/views/centerContacts/ContactList.vue'),
  },
  { // ADD Contact team
    path: '/add-Contact',
    name: 'add-contact',
    component: () => import('@/views/centerContacts/AddContact.vue'),
  },
  // Center Days
  { // ADD Contact team
    path: '/center-days',
    name: 'center-days',
    component: () => import('@/views/CenterDays/CenterDays.vue'),
  },
  { // LIST
    path: '/appointments-center-list',
    name: 'appointments-center-list',
    component: () => import('@/views/AppointmentCenter/AppointmentsList.vue'),
  },
  { // add-appointment
    path: '/add-center-appointment',
    name: 'add-center-appointment',
    component: () => import('@/views/AppointmentCenter/addAppointment.vue'),
  },
  // setting
  {
    path: '/setting',
    name: 'setting',
    component: () => import('@/views/Setting/SettingList.vue'),
  },
  // Resource
  { // LIST
    path: '/resourcesList',
    name: 'resource-list',
    component: () => import('@/views/Resources/ResourceList.vue'),
  },
  { // ADD category
    path: '/add-resource',
    name: 'add-resource',
    component: () => import('@/views/categories/AddCategory.vue'),
  }, { // category-edit
    path: '/resource-edit/:id',
    name: 'resource-edit',
    component: () => import('@/views/Resources/ResourceList.vue'),
  },
]
// TeamPermission
