/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './wp-content/plugins/atricon-sidebar-menu/**/*.php',
    './wp-content/plugins/atricon-sidebar-menu/assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        'atricon': {
          'primary': '#2271b1',
          'primary-dark': '#1e5a8a',
          'secondary': '#6b7280',
          'light': '#f8fafc',
          'lighter': '#f1f5f9',
        },
      },
      spacing: {
        'sidebar': '250px',
        'sidebar-collapsed': '60px',
      },
      boxShadow: {
        'sidebar': '4px 0 20px rgba(0,0,0,0.08)',
        'sidebar-hover': '6px 0 30px rgba(0,0,0,0.15)',
      },
      transitionTimingFunction: {
        'sidebar': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [],
}

