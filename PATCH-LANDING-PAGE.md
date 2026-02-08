# Patch per Landing Page - Exit Intent Popup

## File da modificare
`frontend/pages/index.vue`

## Modifiche da applicare

### 1. Dopo la chiusura del Demo Modal (linea ~724), aggiungere:

```vue
    <!-- Exit Intent Popup -->
    <ExitIntentPopup :show="showExitPopup" @close="showExitPopup = false" @submit="handleExitSubmit" />
```

### 2. Nel script setup, dopo `const showDemo = ref(false)`, aggiungere:

```typescript
const showExitPopup = ref(false)

// Exit intent detection
onMounted(() => {
  if (typeof window === 'undefined') return

  // Check if popup was already shown
  const shown = document.cookie.includes('exitPopupShown=true')
  if (shown) return

  const handleMouseLeave = (e: MouseEvent) => {
    // Only trigger when mouse leaves at the top
    if (e.clientY <= 0 && !showExitPopup.value && !showDemo.value) {
      showExitPopup.value = true
      document.removeEventListener('mouseout', handleMouseLeave)
    }
  }

  // Add listener after a delay to avoid immediate trigger
  setTimeout(() => {
    document.addEventListener('mouseout', handleMouseLeave)
  }, 5000)
})

const handleExitSubmit = (email: string) => {
  // TODO: Submit to newsletter service or backend
  console.log('Exit popup email:', email)
}
```

## Componente gia creato
Il componente ExitIntentPopup.vue e stato creato in:
`frontend/components/ExitIntentPopup.vue`
