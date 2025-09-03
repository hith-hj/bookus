export default [
  {
    title: 'Centers',
    gate: 'list centers',
    icon: 'PackageIcon',

    tagVariant: 'light-warning',
    children: [
      {
        title: 'centers',
        route: 'centers-list',
        gate: 'list centers',
        icon: 'PackageIcon',
      },
    ],
  },
  {
    title: 'Dashboards',
    gate: 'list dashboards',
    icon: 'HomeIcon',
    tag: '2',
    tagVariant: 'light-warning',
    children: [
      {
        title: 'eCommerce',
        route: 'dashboard-ecommerce',
      },
      {
        title: 'Analytics',
        route: 'dashboard-analytics',
      },

    ],
  },
  {
    title: 'Category Template',
    gate: 'list categories',
    icon: 'HomeIcon',

    tagVariant: 'light-warning',
    children: [
      {
        title: 'Categories',
        gate: 'list categories',
        route: 'categories-list',
        icon: 'PackageIcon',
      }, {
        title: 'Services',
        gate: 'list services',
        route: 'services-list',
        icon: 'PackageIcon',
      },
    ],
  },

  {
    title: 'admins',
    gate: 'list admins',
    route: 'admin-list',
    icon: 'UserIcon',
  },
  {
    title: 'roles',
    route: 'roles-list',
    gate: 'list roles',
    icon: 'UserIcon',
  },
  {
    title: 'Clients',
    route: 'clients-list',
    gate: 'list calander',
    icon: 'CircleIcon',
  },
  {
    title: 'Appointments',
    gate: 'list appointments',
    route: 'appointments-list',
    icon: 'ActivityIcon',
  },
  // appointments-center-list
  {
    title: 'Appointments',
    gate: 'list center_appointments',
    route: 'appointments-center-list',
    icon: 'ActivityIcon',
  },
  {
    title: 'calender',
    gate: 'list calander',

    route: 'calendar',
    icon: 'CalendarIcon',
  },
  {
    title: 'Catalogue',
    gate: 'list center_catalogue',
    route: 'catalogue-list',
    icon: 'HomeIcon',
  },
  // {
  //   title: 'Team',
  //   gate: 'list calander',
  //   route: 'team-list',
  //   icon: 'UsersIcon',
  // },
  {
    title: 'Contact',
    gate: 'list center_contact',
    route: 'contact-list',
    icon: 'UsersIcon',
  },

  {
    title: 'Resource',
    gate: 'list center_contact',
    route: 'resource-list',
    icon: 'SlidersIcon',
  },
  {
    title: 'Days Work',
    gate: 'list calander',
    route: 'center-days',
    icon: 'list day_work',
  },
  {
    title: 'Team',
    gate: 'list center_team',
    icon: 'UsersIcon',

    tagVariant: 'light-warning',
    children: [
      {
        title: 'Members',
        gate: 'list center_team',
        route: 'team-list',
        icon: 'PackageIcon',
      }, {
        title: 'Team permission',
        gate: 'list center_team',
        route: 'team-permission',
        icon: 'PackageIcon',
      },
    ],
  },

  {
    title: 'setting',
    gate: 'list calander',
    route: 'setting',
    icon: 'SettingsIcon',
  },
]
