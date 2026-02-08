<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-900 to-blue-800 text-white py-16">
      <div class="max-w-6xl mx-auto px-4">
        <NuxtLink to="/" class="text-blue-200 hover:text-white mb-4 inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Torna alla Home
        </NuxtLink>
        <h1 class="text-4xl font-bold mb-4">Blog Adeguati Assetti</h1>
        <p class="text-xl text-blue-100">Guide, approfondimenti e novita sulla compliance aziendale Art. 2086 c.c.</p>
      </div>
    </header>

    <!-- Articles Grid -->
    <main class="max-w-6xl mx-auto px-4 py-12">
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="article in articles"
          :key="article.slug"
          class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
        >
          <div class="p-6">
            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full mb-4"
              :class="{
                'bg-blue-100 text-blue-800': article.category === 'Normativa',
                'bg-green-100 text-green-800': article.category === 'KPI',
                'bg-red-100 text-red-800': article.category === 'Legale',
                'bg-purple-100 text-purple-800': article.category === 'Giurisprudenza'
              }"
            >
              {{ article.category }}
            </span>
            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
              <NuxtLink :to="`/blog/${article.slug}`" class="hover:text-blue-600">
                {{ article.title }}
              </NuxtLink>
            </h2>
            <p class="text-gray-600 mb-4 line-clamp-3">{{ article.excerpt }}</p>
            <div class="flex items-center justify-between text-sm text-gray-500">
              <span>{{ formatDate(article.date) }}</span>
              <span>{{ article.readTime }} lettura</span>
            </div>
          </div>
        </article>
      </div>
    </main>

    <!-- CTA Section -->
    <section class="bg-blue-900 text-white py-16">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Verifica la Conformita della Tua Azienda</h2>
        <p class="text-xl text-blue-100 mb-8">Inizia gratuitamente con Adeguati Assetti Impresa</p>
        <NuxtLink to="/register" class="inline-block bg-white text-blue-900 px-8 py-4 rounded-lg font-bold hover:bg-blue-50 transition">
          Inizia la Prova Gratuita
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useBlogArticles } from '~/composables/useBlogArticles'

const { articles } = useBlogArticles()

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleDateString('it-IT', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

useHead({
  title: 'Blog - Adeguati Assetti Impresa',
  meta: [
    { name: 'description', content: 'Guide e approfondimenti su Art. 2086 c.c., KPI CNDCEC, compliance aziendale e adeguati assetti organizzativi.' }
  ]
})
</script>
