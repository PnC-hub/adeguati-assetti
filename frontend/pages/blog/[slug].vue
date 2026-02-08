<template>
  <div class="min-h-screen bg-gray-50">
    <template v-if="article">
      <!-- Header -->
      <header class="bg-gradient-to-r from-blue-900 to-blue-800 text-white py-12">
        <div class="max-w-4xl mx-auto px-4">
          <NuxtLink to="/blog" class="text-blue-200 hover:text-white mb-4 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Torna al Blog
          </NuxtLink>
          <span class="inline-block px-3 py-1 text-sm font-medium rounded-full mb-4"
            :class="{
              'bg-blue-700 text-blue-100': article.category === 'Normativa',
              'bg-green-700 text-green-100': article.category === 'KPI',
              'bg-red-700 text-red-100': article.category === 'Legale',
              'bg-purple-700 text-purple-100': article.category === 'Giurisprudenza'
            }"
          >
            {{ article.category }}
          </span>
          <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ article.title }}</h1>
          <div class="flex items-center gap-4 text-blue-200">
            <span>{{ formatDate(article.date) }}</span>
            <span>|</span>
            <span>{{ article.readTime }} lettura</span>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
          <div class="prose prose-lg max-w-none" v-html="article.content"></div>

          <!-- Keywords -->
          <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Argomenti correlati:</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="keyword in article.keywords"
                :key="keyword"
                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"
              >
                {{ keyword }}
              </span>
            </div>
          </div>
        </div>

        <!-- CTA Box -->
        <div class="mt-8 bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-8 text-white text-center">
          <h2 class="text-2xl font-bold mb-4">Non rischiare sanzioni personali</h2>
          <p class="text-red-100 mb-6">Verifica gratuitamente la conformita della tua azienda con Adeguati Assetti Impresa</p>
          <NuxtLink to="/register" class="inline-block bg-white text-red-600 px-8 py-4 rounded-lg font-bold hover:bg-red-50 transition">
            Inizia la Prova Gratuita di 14 Giorni
          </NuxtLink>
        </div>

        <!-- Related Articles -->
        <div class="mt-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Articoli Correlati</h2>
          <div class="grid md:grid-cols-2 gap-6">
            <article
              v-for="related in relatedArticles"
              :key="related.slug"
              class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow"
            >
              <span class="inline-block px-3 py-1 text-xs font-medium rounded-full mb-3"
                :class="{
                  'bg-blue-100 text-blue-800': related.category === 'Normativa',
                  'bg-green-100 text-green-800': related.category === 'KPI',
                  'bg-red-100 text-red-800': related.category === 'Legale',
                  'bg-purple-100 text-purple-800': related.category === 'Giurisprudenza'
                }"
              >
                {{ related.category }}
              </span>
              <h3 class="font-bold text-gray-900 mb-2">
                <NuxtLink :to="`/blog/${related.slug}`" class="hover:text-blue-600">
                  {{ related.title }}
                </NuxtLink>
              </h3>
              <p class="text-gray-600 text-sm line-clamp-2">{{ related.excerpt }}</p>
            </article>
          </div>
        </div>
      </main>
    </template>

    <!-- 404 -->
    <template v-else>
      <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Articolo non trovato</h1>
          <NuxtLink to="/blog" class="text-blue-600 hover:underline">Torna al blog</NuxtLink>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { useBlogArticles } from '~/composables/useBlogArticles'

const route = useRoute()
const { getArticle, articles } = useBlogArticles()

const article = computed(() => getArticle(route.params.slug as string))

const relatedArticles = computed(() => {
  if (!article.value) return []
  return articles
    .filter(a => a.slug !== article.value?.slug)
    .slice(0, 2)
})

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleDateString('it-IT', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

useHead(() => ({
  title: article.value ? `${article.value.title} - Adeguati Assetti` : 'Articolo non trovato',
  meta: [
    { name: 'description', content: article.value?.excerpt || '' },
    { name: 'keywords', content: article.value?.keywords.join(', ') || '' }
  ]
}))
</script>

<style scoped>
.prose h2 {
  @apply text-2xl font-bold text-gray-900 mt-8 mb-4;
}
.prose p {
  @apply text-gray-700 mb-4 leading-relaxed;
}
.prose ul, .prose ol {
  @apply mb-4 pl-6;
}
.prose li {
  @apply text-gray-700 mb-2;
}
.prose ul li {
  @apply list-disc;
}
.prose ol li {
  @apply list-decimal;
}
.prose strong {
  @apply font-semibold text-gray-900;
}
</style>
