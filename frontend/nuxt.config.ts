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
      apiBase: process.env.NUXT_PUBLIC_API_BASE || '/api',
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
      ],
      script: [
        // Google Analytics 4
        {
          src: 'https://www.googletagmanager.com/gtag/js?id=G-2J2JV80XT4',
          async: true,
        },
        {
          children: `window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-2J2JV80XT4');`,
        },
        // Meta Pixel
        {
          children: `!function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'PIXEL_ID');
            fbq('track', 'PageView');`,
        },
      ],
      noscript: [
        {
          children: '<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=PIXEL_ID&ev=PageView&noscript=1"/>',
        },
      ],
    }
  },

  css: ['~/assets/css/main.css']
})
