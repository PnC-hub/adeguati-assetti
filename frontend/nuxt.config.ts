// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-01-24',
  devtools: { enabled: true },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxt/icon'
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'https://adeguatiassettiimpresa.it/api',
      apiKey: process.env.NUXT_PUBLIC_API_KEY || 'sk_adeguatiassetti_2025_xK9mP3nQ7rT2wY5v'
    }
  },

  app: {
    head: {
      title: 'Adeguati Assetti - Monitoraggio KPI Aziendale',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Sistema di monitoraggio degli Adeguati Assetti Organizzativi, Amministrativi e Contabili - Art. 2086 Codice Civile' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  },

  css: ['~/assets/css/main.css']
})
